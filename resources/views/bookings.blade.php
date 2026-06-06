@extends('layouts.user_type.auth')

@section('content')
<div class="bookings-page">
  <div class="page-header">
    <h1 class="page-title"></h1>
    <div class="header-actions">
      <div class="search-wrapper">
        <x-ui.icon name="search" class="search-icon w-4 h-4" />
        <input 
          type="text" 
          id="appointmentSearchInput"
          class="search-input"
          placeholder="Search appointments..." 
          onkeyup="filterTable()"
        />
      </div>
      
      <x-ui.button 
        variant="secondary" 
        size="default" 
        icon-name="filter"
        onclick="openModal('filterModal')"
      >
        Filters
      </x-ui.button>
      
      @if(in_array(auth()->user()->role_id, [1, 2, 3]))
      <x-ui.button 
        variant="primary" 
        size="default" 
        icon-name="add"
        onclick="openSidebar('bookingSidebar')"
      >
        New Appointment
      </x-ui.button>
      @endif
    </div>
  </div>

  <div class="bookings-content">

    <div class="bookings-table-wrapper">
      <table class="bookings-table" id="bookingsTable">
        <thead>
          <tr>
            <th>Pet & Owner</th>
            <th>Service</th>
            <th>Date & Time</th>
            <th>Veterinarian</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @if(isset($bookings))
            @forelse($bookings as $booking)
            <tr>
              <td>
                <div class="booking-pet-info">
                  <div class="pet-name">{{ $booking->pet->name ?? 'N/A' }}</div>
                  <div class="owner-name">{{ $booking->pet->owner->name ?? 'N/A' }}</div>
                </div>
              </td>
              <td>
                <span class="service-name">{{ $booking->service->name ?? 'N/A' }}</span>
              </td>
              <td>
                <div class="appointment-datetime">
                  <div class="appointment-date">{{ \Carbon\Carbon::parse($booking->appointment_datetime)->format('M d, Y') }}</div>
                  <div class="appointment-time">{{ \Carbon\Carbon::parse($booking->appointment_datetime)->format('g:i A') }}</div>
                </div>
              </td>
              <td>
                <span class="vet-name">{{ $booking->staff->name ?? 'Not Assigned' }}</span>
              </td>
              <td>
                <span class="status-badge status-{{ $booking->status }}">
                  {{ ucfirst($booking->status) }}
                </span>
              </td>
              <td>
                <div class="actions-group">
                  <x-ui.button 
                    variant="secondary" 
                    size="xs" 
                    icon-name="eye"
                    onclick="viewBooking({{ $booking->id }})"
                    title="View Details"
                  >View</x-ui.button>
                  @if($booking->status !== 'completed')
                    <x-ui.button 
                      variant="primary" 
                      size="xs" 
                      icon-name="edit"
                      onclick="editBooking({{ $booking->id }})"
                      title="Edit Booking"
                    >Edit</x-ui.button>
                  @endif
                  @if($booking->status === 'pending')
                <x-ui.button 
                      variant="success" 
                      size="xs" 
                      icon-name="check"
                      onclick="confirmBooking({{ $booking->id }})"
                      title="Confirm"
                    >Confirm</x-ui.button>
                    <x-ui.button 
                      variant="danger" 
                      size="xs" 
                      icon-name="close"
                      onclick="openDeclineModal({{ $booking->id }})"
                      title="Decline"
                    >Decline</x-ui.button>
                  @endif
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="empty-state">
                <div class="empty-state-content">
                  <div class="empty-state-icon">
                    <x-ui.icon name="calendar" class="w-16 h-16 text-gray-300" />
                  </div>
                  <h3 class="empty-state-title">No Appointments Found</h3>
                  <p class="empty-state-description">There are no bookings scheduled yet. Create your first appointment to get started.</p>
                  @if(in_array(auth()->user()->role_id, [1, 2, 3]))
                  <button class="empty-state-action" onclick="openSidebar('bookingSidebar')">
                    <x-ui.icon name="add" class="w-5 h-5" />
                    Schedule First Appointment
                  </button>
                  @endif
                </div>
              </td>
            </tr>
            @endforelse
          @else
          <tr>
            <td colspan="6" class="empty-state">
              <div class="empty-state-content">
                <div class="empty-state-icon">
                  <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                  </svg>
                </div>
                <h3 class="empty-state-title">No Appointments Found</h3>
                <p class="empty-state-description">There are no bookings scheduled yet. Create your first appointment to get started.</p>
                @if(in_array(auth()->user()->role_id, [1, 2, 3]))
                <button class="empty-state-action" onclick="openSidebar('bookingSidebar')">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                  </svg>
                  Schedule First Appointment
                </button>
                @endif
              </div>
            </td>
          </tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Booking Sidebar -->
<div id="bookingSidebar" class="sidebar-overlay hidden">
  <div class="sidebar-backdrop" onclick="closeSidebar('bookingSidebar')"></div>
  <div class="sidebar-content">
    <div class="sidebar-header">
      <h3 class="sidebar-title">New Appointment</h3>
      <button type="button" class="sidebar-close" onclick="closeSidebar('bookingSidebar')">
        <x-ui.icon name="close" class="w-3.5 h-3.5" />
      </button>
    </div>

    <div class="sidebar-body">
      <form id="bookingForm" method="POST" action="/bookings">
        @csrf
        <input type="hidden" name="booking_id" id="bookingId">
        
        <!-- Form Fields -->
        <div class="form-fields">
          @if(in_array(auth()->user()->role_id, [1, 2]))
          <div class="form-group">
            <label class="form-label">Clinic *</label>
            <select name="clinic_id" id="bookingClinic" class="form-select" required onchange="loadClinicData(this.value)">
              <option value="">Select Clinic</option>
              @foreach($clinics as $clinic)
                <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
              @endforeach
            </select>
          </div>
          @endif
          
          <div class="form-group">
            <label class="form-label">Pet *</label>
            <select name="pet_id" id="bookingPet" class="form-select" required>
              <option value="">Select Pet</option>
            </select>
          </div>
          
          <div class="form-group">
            <label class="form-label">Service *</label>
            <select name="service_id" id="bookingService" class="form-select" required>
              <option value="">Select Service</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Date *</label>
            <input type="date" name="appointment_date" id="appointmentDate" class="form-input" required>
          </div>
          
          <div class="form-group">
            <label class="form-label">Time *</label>
            <input type="time" name="appointment_time" id="appointmentTime" class="form-input" required>
          </div>

          <div class="form-group">
            <label class="form-label">Veterinarian</label>
            <select name="staff_id" id="bookingStaff" class="form-select">
              <option value="">Select Veterinarian</option>
            </select>
          </div>
          
          <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" id="bookingStatus" class="form-select">
              <option value="pending">Pending</option>
              <option value="confirmed">Confirmed</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Notes</label>
            <textarea name="notes" id="bookingNotes" rows="3" class="form-input" placeholder="Additional notes..."></textarea>
          </div>
        </div>

        <div class="sidebar-actions">
          <button type="button" class="btn-secondary" onclick="closeSidebar('bookingSidebar')">
            Cancel
          </button>
          <button type="submit" class="btn-primary" id="saveBookingButton">
            Save Appointment
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Filter Modal -->
<x-ui.modal id="filterModal" title="Filter Appointments" size="sm">
  <div class="filter-form">
    <div class="form-group">
      <label class="form-label">Status</label>
      <select id="statusFilter" class="form-select" onchange="filterTable()">
        <option value="">All Status</option>
        <option value="pending">Pending</option>
        <option value="confirmed">Confirmed</option>
        <option value="completed">Completed</option>
        <option value="cancelled">Cancelled</option>
      </select>
    </div>
    
    <div class="form-group">
      <label class="form-label">Date</label>
      <input 
        type="date" 
        id="dateFilter" 
        class="form-input"
        onchange="filterTable()"
      />
    </div>
  </div>
  
  <div class="modal-actions">
    <x-ui.button 
      type="button" 
      variant="secondary" 
      onclick="clearFilters()"
      class="modal-action-btn"
    >
      Clear Filters
    </x-ui.button>
    <x-ui.button 
      type="button" 
      variant="primary" 
      onclick="closeModal('filterModal')"
      class="modal-action-btn"
    >
      Apply Filters
    </x-ui.button>
  </div>
</x-ui.modal>

<style>
/* Bookings Page Layout */
.bookings-page {
  padding: 1.5rem;
  background: #fafbfc;
  min-height: 100vh;
  font-family: 'Poppins', sans-serif;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 2rem;
}

.header-content {
  flex: 1;
}

.page-title {
  font-size: 1.75rem;
  font-weight: 600;
  color: #111827;
  margin: 0 0 0.5rem 0;
  letter-spacing: -0.025em;
  font-family: 'Poppins', sans-serif;
}

.page-subtitle {
  font-size: 1rem;
  color: #6b7280;
  margin: 0;
  font-weight: 400;
  font-family: 'Poppins', sans-serif;
}

.header-actions {
  display: flex;
  gap: 1rem;
  align-items: center;
}

/* Header Search Styles */
.header-actions .search-wrapper {
  position: relative;
  width: 300px;
}

.header-actions .search-input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 0.875rem;
  background: white;
  transition: all 0.2s ease;
  font-family: 'Poppins', sans-serif;
}

.header-actions .search-input:focus {
  outline: none;
  border: 1px solid #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Modal Actions */
.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 1.5rem;
}

.modal-action-btn {
  flex: 1;
  min-width: 120px;
}

.bookings-content {
  background: white;
  border-radius: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
  overflow: hidden;
}

.filters-section {
  padding: 1.25rem 1.5rem;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  border-bottom: 1px solid #e2e8f0;
}

.search-filter-container {
  display: flex;
  gap: 1rem;
  align-items: center;
  max-width: 800px;
}

.search-wrapper {
  position: relative;
  flex: 1;
}

.search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #9ca3af;
  pointer-events: none;
}

.search-input {
  width: 100%;
  padding: 0.875rem 1rem 0.875rem 2.5rem;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  font-size: 0.875rem;
  background: white;
  transition: all 0.2s ease;
  font-family: 'Poppins', sans-serif;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.search-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1), 0 1px 3px rgba(0, 0, 0, 0.1);
  transform: translateY(-1px);
}

.filter-select {
  min-width: 160px;
  padding: 0.875rem 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  font-size: 0.875rem;
  background: white;
  transition: all 0.2s ease;
  font-family: 'Poppins', sans-serif;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  cursor: pointer;
}

.filter-select:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1), 0 1px 3px rgba(0, 0, 0, 0.1);
  transform: translateY(-1px);
}

.filter-select:hover {
  border-color: #d1d5db;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.bookings-table-wrapper {
  overflow-x: auto;
}

.bookings-table {
  width: 100%;
  border-collapse: collapse;
  font-family: 'Poppins', sans-serif;
}

.bookings-table thead {
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
}

.bookings-table th {
  padding: 1rem 1.5rem;
  text-align: left;
  font-weight: 600;
  font-size: 0.75rem;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-family: 'Poppins', sans-serif;
}

.bookings-table td {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
  font-size: 0.875rem;
}

.bookings-table tbody tr {
  transition: background-color 0.2s ease;
}

.bookings-table tbody tr:hover {
  background: #f8fafc;
}

/* Sidebar Styles */
.sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 1000;
  display: flex;
  justify-content: flex-end;
  visibility: hidden;
  transition: visibility 0.3s ease;
}

.sidebar-overlay:not(.hidden) {
  visibility: visible;
}

.sidebar-backdrop {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.sidebar-overlay:not(.hidden) .sidebar-backdrop {
  opacity: 1;
}

.sidebar-content {
  position: relative;
  width: 480px;
  height: 100vh;
  background: white;
  box-shadow: -4px 0 20px rgba(0, 0, 0, 0.15);
  display: flex;
  flex-direction: column;
  transform: translateX(100%);
  transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.sidebar-overlay:not(.hidden) .sidebar-content {
  transform: translateX(0);
}

.sidebar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 2rem 2rem 1rem 2rem;
  border-bottom: 1px solid #e5e7eb;
}

.sidebar-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
  font-family: 'Poppins', sans-serif;
}

.sidebar-close {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border: none;
  background: #f8fafc;
  color: #64748b;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.sidebar-close:hover {
  background: #e2e8f0;
  color: #475569;
}

.sidebar-body {
  flex: 1;
  padding: 2rem;
  overflow-y: auto;
}

.form-fields {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-label {
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.5rem;
  font-family: 'Poppins', sans-serif;
}

.form-input, .form-select {
  padding: 1rem 1.25rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 0.875rem;
  transition: all 0.2s ease;
  font-family: 'Poppins', sans-serif;
  background: #fafbfc;
  width: 100%;
}

.form-input:focus, .form-select:focus {
  outline: none;
  border-color: #3b82f6;
  background: white;
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
  transform: translateY(-1px);
}

.form-input::placeholder {
  color: #9ca3af;
}

.sidebar-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  padding-top: 2rem;
  margin-top: 2rem;
  border-top: 1px solid #e5e7eb;
}

.btn-primary, .btn-secondary {
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 600;
  font-family: 'Poppins', sans-serif;
  transition: all 0.2s ease;
  border: none;
  cursor: pointer;
}

.btn-primary {
  background: #3b82f6;
  color: white;
}

.btn-primary:hover {
  background: #2563eb;
}

.btn-secondary {
  background: #f3f4f6;
  color: #374151;
}

.btn-secondary:hover {
  background: #e5e7eb;
}

@media (max-width: 768px) {
  .sidebar-content {
    width: 100vw;
  }
  
  .sidebar-header {
    padding: 1.5rem;
  }
  
  .sidebar-body {
    padding: 1.5rem;
  }
}

.booking-pet-info {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.pet-name {
  font-weight: 600;
  color: #111827;
  font-size: 0.875rem;
}

.owner-name {
  font-size: 0.75rem;
  color: #6b7280;
  font-weight: 500;
}

.service-name {
  font-weight: 500;
  color: #374151;
  font-size: 0.875rem;
}

.appointment-datetime {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.appointment-date {
  font-weight: 500;
  color: #111827;
  font-size: 0.875rem;
}

.appointment-time {
  font-size: 0.75rem;
  color: #6b7280;
}

.status-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.375rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: capitalize;
}

.status-pending {
  background: #fef3c7;
  color: #92400e;
}

.status-confirmed {
  background: #dbeafe;
  color: #1e40af;
}

.status-completed {
  background: #d1fae5;
  color: #065f46;
}

.status-cancelled {
  background: #fee2e2;
  color: #991b1b;
}

.service-name {
  font-size: 0.875rem;
  color: #374151;
}

.appointment-time {
  display: flex;
  flex-direction: column;
}

.date {
  font-weight: 600;
  color: #111827;
  font-size: 0.875rem;
  margin-bottom: 0.25rem;
}

.time {
  font-size: 0.75rem;
  color: #6b7280;
}

.vet-name {
  font-size: 0.875rem;
  color: #374151;
}

.actions-group {
  display: flex;
  gap: 0.5rem;
}

@media (max-width: 768px) {
  .bookings-page {
    padding: 1rem;
  }
  
  .page-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .search-filter-container {
    flex-direction: column;
    align-items: stretch;
    gap: 0.75rem;
  }
  
  .filter-select {
    min-width: auto;
  }
  
  .actions-group {
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .bookings-table th,
  .bookings-table td {
    padding: 0.75rem 0.5rem;
    font-size: 0.75rem;
  }
}
</style>

<script>
// Sidebar Functions
function openSidebar(sidebarId) {
  document.getElementById(sidebarId).classList.remove('hidden');
  document.body.style.overflow = 'hidden';
}

function closeSidebar(sidebarId) {
  document.getElementById(sidebarId).classList.add('hidden');
  document.body.style.overflow = 'auto';
}

function clearFilters() {
  document.getElementById('statusFilter').value = '';
  document.getElementById('dateFilter').value = '';
  filterTable();
}

function filterTable() {
  const searchInput = document.getElementById('appointmentSearchInput').value.toLowerCase();
  const statusFilter = document.getElementById('statusFilter').value;
  const table = document.getElementById('bookingsTable');
  const rows = table.querySelectorAll('tbody tr');

  rows.forEach(row => {
    const cells = row.querySelectorAll('td');
    if (cells.length > 0) {
      const petName = cells[0] ? cells[0].textContent.toLowerCase() : '';
      const ownerName = cells[1] ? cells[1].textContent.toLowerCase() : '';
      const service = cells[2] ? cells[2].textContent.toLowerCase() : '';
      const status = cells[4] ? cells[4].textContent.trim().toLowerCase() : '';

      const matchesSearch = petName.includes(searchInput) || ownerName.includes(searchInput) || service.includes(searchInput);
      const matchesStatus = !statusFilter || status.includes(statusFilter.toLowerCase());

      row.style.display = matchesSearch && matchesStatus ? '' : 'none';
    }
  });
}

function filterBookings() {
  const searchTerm = document.getElementById('searchInput').value.toLowerCase();
  const statusFilter = document.getElementById('statusFilter').value;
  const dateFilter = document.getElementById('dateFilter').value;
  const rows = document.querySelectorAll('#bookingsTable tbody tr');

  rows.forEach(row => {
    const petName = row.querySelector('.pet-name')?.textContent.toLowerCase() || '';
    const ownerName = row.querySelector('.owner-name')?.textContent.toLowerCase() || '';
    const serviceName = row.querySelector('.service-name')?.textContent.toLowerCase() || '';
    const status = row.querySelector('.badge')?.textContent.toLowerCase().trim() || '';
    
    const matchesSearch = petName.includes(searchTerm) || ownerName.includes(searchTerm) || serviceName.includes(searchTerm);
    const matchesStatus = !statusFilter || status === statusFilter;
    
    row.style.display = matchesSearch && matchesStatus ? '' : 'none';
  });
}

function viewBooking(bookingId) {
  console.log('View booking:', bookingId);
}

async function editBooking(bookingId) {
  try {
    // Fetch booking data
    const response = await fetch(`/bookings/${bookingId}/edit`);
    if (!response.ok) {
      alert('Failed to load booking data');
      return;
    }
    
    const booking = await response.json();
    
    // Populate form fields
    document.getElementById('bookingId').value = bookingId;
    document.getElementById('bookingPet').value = booking.pet_id;
    document.getElementById('bookingService').value = booking.service_id;
    document.getElementById('bookingStaff').value = booking.staff_id;
    document.getElementById('bookingDate').value = booking.appointment_datetime;
    document.getElementById('bookingNotes').value = booking.notes || '';
    
    // If clinic selection exists (for admin/super admin)
    const clinicSelect = document.getElementById('bookingClinic');
    if (clinicSelect) {
      clinicSelect.value = booking.clinic_id;
      // Load clinic data to populate dropdowns
      await loadClinicData(booking.clinic_id);
      // Re-set the values after loading clinic data
      setTimeout(() => {
        document.getElementById('bookingPet').value = booking.pet_id;
        document.getElementById('bookingService').value = booking.service_id;
        document.getElementById('bookingStaff').value = booking.staff_id;
      }, 100);
    }
    
    document.querySelector('#bookingSidebar .sidebar-title').textContent = 'Edit Appointment';
    document.getElementById('saveBookingButton').textContent = 'Update Appointment';
    
    openSidebar('bookingSidebar');
  } catch (error) {
    console.error('Error loading booking data:', error);
    alert('Failed to load booking data: ' + error.message);
  }
}

async function confirmBooking(bookingId) {
  if (confirm('Are you sure you want to confirm this booking?')) {
    try {
      const formData = new FormData();
      formData.append('booking_id', bookingId);
      formData.append('action', 'confirmed');
      
      const response = await fetch('/bookings/action', {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
      });
      
      if (response.ok) {
        alert('Booking confirmed successfully');
        window.location.reload();
      } else {
        alert('Failed to confirm booking');
      }
    } catch (error) {
      console.error('Error confirming booking:', error);
      alert('Failed to confirm booking: ' + error.message);
    }
  }
}

// Form submission
document.getElementById('bookingForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  console.log('Booking form submitted');
  
  const formData = new FormData(this);
  const submitButton = document.getElementById('saveBookingButton');
  const originalText = submitButton.textContent;
  
  submitButton.textContent = 'Saving...';
  submitButton.disabled = true;
  
  try {
    const response = await fetch('/bookings', {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      }
    });
    
    console.log('Response status:', response.status);
    
    if (response.ok) {
      alert('Appointment saved successfully');
      closeSidebar('bookingSidebar');
      window.location.reload();
    } else {
      const errorData = await response.text();
      console.error('Error response:', errorData);
      alert('Failed to save appointment');
    }
  } catch (error) {
    console.error('Error saving appointment:', error);
    alert('Failed to save appointment: ' + error.message);
  } finally {
    submitButton.textContent = originalText;
    submitButton.disabled = false;
  }
});

// Decline booking functions
function openDeclineModal(bookingId) {
  document.getElementById('declineBookingId').value = bookingId;
  document.getElementById('declineReason').value = '';
  document.getElementById('declineModal').classList.remove('hidden');
}

function closeDeclineModal() {
  document.getElementById('declineModal').classList.add('hidden');
}

async function submitDecline() {
  const bookingId = document.getElementById('declineBookingId').value;
  const reason = document.getElementById('declineReason').value.trim();
  
  if (!reason) {
    alert('Please provide a reason for declining this booking.');
    return;
  }
  
  try {
    const formData = new FormData();
    formData.append('notes', reason);
    
    const response = await fetch(`/bookings/${bookingId}/decline`, {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      }
    });
    
    if (response.ok) {
      alert('Booking declined successfully');
      closeDeclineModal();
      window.location.reload();
    } else {
      alert('Failed to decline booking');
    }
  } catch (error) {
    console.error('Error declining booking:', error);
    alert('Failed to decline booking: ' + error.message);
  }
}

// Load clinic-specific data for admin/super admin
function loadClinicData(clinicId) {
  if (!clinicId) {
    // Clear dropdowns if no clinic selected
    document.getElementById('bookingPet').innerHTML = '<option value="">Select Pet</option>';
    document.getElementById('bookingService').innerHTML = '<option value="">Select Service</option>';
    document.getElementById('bookingStaff').innerHTML = '<option value="">Select Veterinarian</option>';
    return;
  }
  
  // Load pets for selected clinic
  fetch(`/api/clinic/${clinicId}/pets`)
    .then(response => response.json())
    .then(pets => {
      const petSelect = document.getElementById('bookingPet');
      petSelect.innerHTML = '<option value="">Select Pet</option>';
      pets.forEach(pet => {
        petSelect.innerHTML += `<option value="${pet.id}">${pet.name} (${pet.owner?.name || 'Unknown Owner'})</option>`;
      });
    })
    .catch(error => console.error('Error loading pets:', error));
    
  // Load services for selected clinic
  fetch(`/api/clinic/${clinicId}/services`)
    .then(response => response.json())
    .then(services => {
      const serviceSelect = document.getElementById('bookingService');
      serviceSelect.innerHTML = '<option value="">Select Service</option>';
      services.forEach(service => {
        serviceSelect.innerHTML += `<option value="${service.id}">${service.name} - ₱${service.price}</option>`;
      });
    })
    .catch(error => console.error('Error loading services:', error));
    
  // Load veterinarians for selected clinic
  fetch(`/api/clinic/${clinicId}/veterinarians`)
    .then(response => response.json())
    .then(vets => {
      const vetSelect = document.getElementById('bookingStaff');
      vetSelect.innerHTML = '<option value="">Select Veterinarian</option>';
      vets.forEach(vet => {
        vetSelect.innerHTML += `<option value="${vet.id}">${vet.name}</option>`;
      });
    })
    .catch(error => console.error('Error loading veterinarians:', error));
}
</script>

<style>
/* Empty State Styles */
.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  background: #fafafa;
}

.empty-state-content {
  max-width: 400px;
  margin: 0 auto;
}

.empty-state-icon {
  margin-bottom: 1.5rem;
  color: #9ca3af;
}

.empty-state-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.5rem;
}

.empty-state-description {
  color: #6b7280;
  margin-bottom: 2rem;
  line-height: 1.6;
}

.empty-state-action {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: #3b82f6;
  color: white;
  border: none;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}

.empty-state-action:hover {
  background: #2563eb;
}
</style>

<!-- Decline Modal -->
<div id="declineModal" class="sidebar-overlay hidden">
  <div class="sidebar-backdrop" onclick="closeDeclineModal()"></div>
  <div class="sidebar-content" style="max-width: 500px;">
    <div class="sidebar-header">
      <h3 class="sidebar-title">Decline Booking</h3>
      <button class="sidebar-close" onclick="closeDeclineModal()">
        <x-ui.icon name="close" class="w-4 h-4" />
      </button>
    </div>
    
    <div class="sidebar-body">
      <form id="declineForm">
        <input type="hidden" id="declineBookingId" name="booking_id">
        
        <div class="form-group">
          <label class="form-label">Reason for declining *</label>
          <textarea name="notes" id="declineReason" class="form-input" rows="4" required 
                    placeholder="Please provide a reason for declining this booking..."></textarea>
        </div>
      </form>
    </div>
    
    <div class="sidebar-footer">
      <button type="button" class="btn-secondary" onclick="closeDeclineModal()">
        Cancel
      </button>
      <button type="button" class="btn-danger" onclick="submitDecline()">
        Decline Booking
      </button>
    </div>
  </div>
</div>

@endsection
