@extends('layouts.user_type.auth')

@section('content')
<div class="schedule-page">
  <div class="page-header">
    <h1 class="page-title"></h1>
    <div class="header-actions">
      <div class="search-wrapper">
        <x-ui.icon name="search" class="search-icon w-4 h-4" />
        <input 
          type="text" 
          id="scheduleSearchInput"
          class="search-input"
          placeholder="Search schedule slots..." 
          onkeyup="filterSlots()"
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
      
      <x-ui.button 
        variant="primary" 
        size="default" 
          icon-name="add"
        onclick="openSidebar()"
      >
        Add New Slot
      </x-ui.button>
    </div>
  </div>

  <div class="schedule-content">
    <div class="schedule-table-wrapper">
      @if(isset($slots) && count($slots) > 0)
      <table class="schedule-table" id="scheduleTable">
        <thead>
          <tr>
            <th>Day</th>
            <th>Service</th>
            @if(auth()->user()->role_id == 1)
            <th>Clinic</th>
            @endif
            <th>Time Slots</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="slotsTableBody">
          @foreach($slots as $slot)
          <tr class="slot-row" 
              data-service="{{ $slot->service_id }}"
              data-day="{{ $slot->day }}"
              data-search="{{ strtolower($slot->day . ' ' . $slot->service->name . ' ' . ($slot->clinic->name ?? '')) }}">
            <td>
              <div class="day-info">
                <div class="day-name">{{ ucfirst($slot->day) }}</div>
              </div>
            </td>
            <td>
              <span class="service-name">{{ $slot->service->name }}</span>
            </td>
            @if(auth()->user()->role_id == 1)
            <td>
              <span class="clinic-name">{{ $slot->clinic->name ?? 'N/A' }}</span>
            </td>
            @endif
            <td>
              <div class="time-slots-display">
                @foreach(json_decode($slot->time_slots) as $time)
                  <span class="time-slot-badge">{{ $time }}</span>
                @endforeach
              </div>
            </td>
            <td>
              <span class="status-badge status-{{ $slot->status == 1 ? 'active' : 'inactive' }}">
                {{ $slot->status == 1 ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td>
              <div class="actions-group">
                <x-ui.button 
                  variant="secondary" 
                  size="xs" 
                  icon-name="edit"
                  onclick="openSidebar({{ json_encode($slot) }})"
                  title="Edit Slot"
                >Edit</x-ui.button>
                <x-ui.button 
                  variant="info" 
                  size="xs" 
                  icon-name="copy"
                  onclick="openDuplicateModal({{ json_encode($slot) }})"
                  title="Duplicate Slot"
                >Copy</x-ui.button>
                <button class="btn-clean btn-danger-clean btn-xs-clean" onclick="deleteSlot({{ $slot->id }})" title="Delete Slot">
                  <i class="fas fa-trash-alt" style="font-size:12px;margin-right:4px"></i> Delete
                </button>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @else
      <div class="empty-state">
        <div class="empty-icon">
          <x-ui.icon name="close" class="w-5 h-5" />
        </div>
        <h3>No Schedule Slots</h3>
        <p>Create your first schedule slot to get started.</p>
        <x-ui.button 
          variant="primary" 
          onclick="openSidebar()"
        icon-name="add"
        >
          Add New Slot
        </x-ui.button>
      </div>
      @endif
    </div>
  </div>
</div>

      <!-- Duplicate Modal -->
      <div class="modal fade" id="duplicateSlotModal" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Duplicate Schedule Slot</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="duplicateForm" onsubmit="handleDuplicateSubmit(event)">
              <input type="hidden" name="slot_id" id="duplicateSlotId">
              <div class="modal-body">
                <div class="mb-3">
                  <label for="duplicate_day" class="form-label">Select Day to Duplicate To</label>
                  <select class="form-select" id="duplicate_day" name="day" required>
                    @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                      <option value="{{ $day }}">{{ ucfirst($day) }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Duplicate</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

<script>
function filterSlots() {
  const searchInput = document.getElementById('scheduleSearchInput').value.toLowerCase();
  const serviceFilter = document.getElementById('serviceFilter').value;
  const dayFilter = document.getElementById('dayFilter').value;
  const rows = document.querySelectorAll('.slot-row');

  rows.forEach(row => {
    const searchText = row.dataset.search || '';
    const serviceMatch = !serviceFilter || row.dataset.service === serviceFilter;
    const dayMatch = !dayFilter || row.dataset.day === dayFilter;
    const searchMatch = !searchInput || searchText.includes(searchInput);
    
    row.style.display = (serviceMatch && dayMatch && searchMatch) ? '' : 'none';
  });
}

function handleDuplicateSubmit(e) {
  e.preventDefault();
  const slotId = document.getElementById('duplicateSlotId').value;
  const day = document.getElementById('duplicate_day').value;
  window.location.href = `{{ url('schedules/duplicate') }}/${slotId}/${day}`;
}

function openDuplicateModal(slot) {
  const modal = new bootstrap.Modal(document.getElementById('duplicateSlotModal'));
  document.getElementById('duplicateSlotId').value = slot.id;
  
  const daySelect = document.getElementById('duplicate_day');
  for(let i = 0; i < daySelect.options.length; i++) {
    if(daySelect.options[i].value === slot.day) {
      daySelect.options[i].disabled = true;
    } else {
      daySelect.options[i].disabled = false;
    }
  }
  
  modal.show();
}
</script>
</main>

<!-- Add/Edit Slot Sidebar -->
<div id="addSlotSidebar" class="sidebar-overlay hidden">
  <div class="sidebar-backdrop" onclick="closeSidebar()"></div>
  <div class="sidebar-content" style="width: 480px; max-width: 90vw;">
    <div class="sidebar-header" style="padding: 1.25rem 1.5rem;">
      <h5 id="sidebarTitle" style="margin:0;font-size:1.15rem;font-weight:600;">Customize Schedule Slots</h5>
      <button type="button" class="sidebar-close-btn" onclick="closeSidebar()" style="background:none;border:none;font-size:1.5rem;cursor:pointer;color:#6b7280;line-height:1;">&times;</button>
    </div>
    <div class="sidebar-body" style="padding: 1.5rem;flex:1;overflow-y:auto;">
      <form id="addSlotForm" method="POST" action="" onsubmit="return validateForm()">
        @csrf
        <input type="hidden" id="slot_id" name="slot_id">
        @if(auth()->user()->role_id == 1)
          <div style="margin-bottom:1rem;">
            <label for="clinic_id" style="display:block;margin-bottom:0.5rem;font-size:0.875rem;font-weight:500;color:#374151;">Select Clinic</label>
            <select class="form-input" id="clinic_id" name="clinic_id" required>
              <option value="">Select a clinic</option>
              @foreach($clinics as $clinic)
                <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
              @endforeach
            </select>
          </div>
        @else
          <input type="hidden" name="clinic_id" value="{{ auth()->user()->clinic_id }}">
        @endif

        <div style="margin-bottom:1rem;">
          <label for="service_id" style="display:block;margin-bottom:0.5rem;font-size:0.875rem;font-weight:500;color:#374151;">Select Service</label>
          <select class="form-input" id="service_id" name="service_id" required>
            <option value="">Select a service</option>
            @foreach($services as $service)
              <option value="{{ $service->id }}">{{ $service->name }}</option>
            @endforeach
          </select>
        </div>

        <div style="margin-bottom:1rem;">
          <label for="day" style="display:block;margin-bottom:0.5rem;font-size:0.875rem;font-weight:500;color:#374151;">Select Day</label>
          <select class="form-input" id="day" name="day" required>
            <option value="">Select a day</option>
            @php
              $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            @endphp
            @foreach($days as $day)
              <option value="{{ $day }}">{{ ucfirst($day) }}</option>
            @endforeach
          </select>
        </div>

        <div style="margin-bottom:1rem;display:flex;gap:0.75rem;">
          <div style="flex:1;">
            <label for="startTime" style="display:block;margin-bottom:0.5rem;font-size:0.875rem;font-weight:500;color:#374151;">Start Time</label>
            <input type="time" class="form-input" id="startTime" value="08:00" required>
          </div>
          <div style="flex:1;">
            <label for="endTime" style="display:block;margin-bottom:0.5rem;font-size:0.875rem;font-weight:500;color:#374151;">End Time</label>
            <input type="time" class="form-input" id="endTime" value="20:00" required>
          </div>
        </div>
        
        <div style="margin-bottom:1rem;">
          <label style="display:block;margin-bottom:0.5rem;font-size:0.875rem;font-weight:500;color:#374151;">Available Time Slots</label>
          <div id="timeSlots" style="border:1px solid #e2e8f0;border-radius:8px;padding:0.75rem;max-height:240px;overflow-y:auto;">
            <div class="time-slots-grid">
            </div>
          </div>
        </div>

        <div style="margin-bottom:1rem;">
          <label for="status" style="display:block;margin-bottom:0.5rem;font-size:0.875rem;font-weight:500;color:#374151;">Status</label>
          <select class="form-input" id="status" name="status" required>
            <option value="">Select status</option>
            <option value="1">Active</option>
            <option value="0">Inactive</option>
          </select>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:0.75rem;padding-top:1rem;border-top:1px solid #e5e7eb;margin-top:1.5rem;">
          <button type="button" class="btn-clean btn-secondary-clean" onclick="closeSidebar()">Close</button>
          <button type="submit" class="btn-clean btn-primary-clean" id="saveScheduleBtn">Save Schedule</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
/* Schedule Page Layout */
.schedule-page {
  padding: 1.5rem;
  background: #fafbfc;
  min-height: 100vh;
  font-family: 'Poppins', sans-serif;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.page-title {
  font-size: 1.75rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
  letter-spacing: -0.025em;
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
  padding: 1px;
}

.header-actions .search-icon {
  position: absolute;
  left: 13px;
  top: 50%;
  transform: translateY(-50%);
  color: #9ca3af;
  pointer-events: none;
  z-index: 1;
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

.schedule-content {
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
  min-width: 140px;
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

.schedule-table-wrapper {
  overflow-x: auto;
}

.schedule-table {
  width: 100%;
  border-collapse: collapse;
  font-family: 'Poppins', sans-serif;
}

.schedule-table thead {
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
}

.schedule-table th {
  padding: 1rem 1.5rem;
  text-align: left;
  font-weight: 600;
  font-size: 0.75rem;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-family: 'Poppins', sans-serif;
}

.schedule-table td {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
  font-size: 0.875rem;
}

.schedule-table tbody tr {
  transition: background-color 0.2s ease;
}

.schedule-table tbody tr:hover {
  background: #f8fafc;
}

.day-info {
  display: flex;
  flex-direction: column;
}

.day-name {
  font-weight: 600;
  color: #111827;
  font-size: 0.875rem;
}

.service-name {
  font-weight: 500;
  color: #374151;
  font-size: 0.875rem;
}

.clinic-name {
  font-size: 0.875rem;
  color: #6b7280;
}

.time-slots-display {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.time-slot-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.25rem 0.5rem;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 500;
  background: #e0e7ff;
  color: #3730a3;
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

.status-active {
  background: #d1fae5;
  color: #065f46;
}

.status-inactive {
  background: #fee2e2;
  color: #991b1b;
}

.actions-group {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  color: #6b7280;
}

.empty-icon {
  font-size: 3rem;
  color: #d1d5db;
  margin-bottom: 1rem;
}

.empty-state h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.5rem;
}

.empty-state p {
  margin-bottom: 2rem;
}

/* Sidebar Styles - Standard Pattern */
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
  max-width: 90vw;
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
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.sidebar-body {
  flex: 1;
  padding: 1.5rem;
  overflow-y: auto;
}

.time-slots-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 8px;
}

.time-slot-chip {
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 6px 12px;
  cursor: pointer;
  transition: all 0.2s ease;
  background: #f8f9fa;
  text-align: center;
  min-height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.time-slot-chip label {
  margin: 0;
  cursor: pointer;
  width: 100%;
  text-align: center;
  font-size: 0.8125rem;
  font-weight: 500;
}

.time-slot-chip input[type="checkbox"] {
  display: none;
}

.time-slot-chip.selected {
  background: #3b82f6;
  color: white;
  border-color: #2563eb;
}

.sidebar-close-btn:hover {
  background: #f3f4f6;
  border-radius: 6px;
  padding: 2px 6px;
}

@media (max-width: 768px) {
  .schedule-page {
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
  
  .schedule-table th,
  .schedule-table td {
    padding: 0.75rem 0.5rem;
    font-size: 0.75rem;
  }
  
  .sidebar-content {
    width: 100vw;
    max-width: 100vw;
  }
  
  .sidebar-header {
    padding: 1rem 1.25rem;
  }
  
  .sidebar-body {
    padding: 1.25rem;
  }
}
</style>

<!-- Filter Modal -->
<x-ui.modal id="filterModal" title="Filter Schedule" size="sm">
  <div class="filter-form">
    <div class="form-group">
      <label class="form-label">Service</label>
      <select id="serviceFilter" class="form-select" onchange="filterSlots()">
        <option value="">All Services</option>
        @if(isset($services))
          @foreach($services as $service)
            <option value="{{ $service->id }}">{{ $service->name }}</option>
          @endforeach
        @endif
      </select>
    </div>
    
    <div class="form-group">
      <label class="form-label">Day</label>
      <select id="dayFilter" class="form-select" onchange="filterSlots()">
        <option value="">All Days</option>
        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
          <option value="{{ $day }}">{{ ucfirst($day) }}</option>
        @endforeach
      </select>
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

<script>
function openSidebar(slot = null) {
  const sidebar = document.getElementById('addSlotSidebar');
  const title = document.getElementById('sidebarTitle');
  const daySelect = document.getElementById('day');
  
  // Reset form before opening
  resetForm();
  
  // Set title based on whether editing or adding
  title.textContent = slot ? 'Edit Schedule Slot' : 'Add New Schedule Slot';
  
  // If editing, populate form with slot data
  if (slot) {
    const slotData = typeof slot === 'string' ? JSON.parse(slot) : slot;
    document.getElementById('slot_id').value = slotData.id;
    if(document.getElementById('clinic_id')) {
      document.getElementById('clinic_id').value = slotData.clinic_id;
    }
    document.getElementById('service_id').value = slotData.service_id;
    document.getElementById('status').value = slotData.status;
    
    // Add the current day as an option if it doesn't exist
    let dayExists = false;
    for(let i = 0; i < daySelect.options.length; i++) {
      if(daySelect.options[i].value === slotData.day) {
        dayExists = true;
        break;
      }
    }
    if(!dayExists) {
      const option = new Option(slotData.day.charAt(0).toUpperCase() + slotData.day.slice(1), slotData.day);
      daySelect.add(option);
    }
    daySelect.value = slotData.day;
    
    // Generate time slots and mark selected ones
    generateTimeSlots();
    setTimeout(() => {
      const selectedSlots = JSON.parse(slotData.time_slots);
      selectedSlots.forEach(time => {
        const slotId = `slot_${time.replace(/\s/g, '')}`;
        const checkbox = document.getElementById(slotId);
        if (checkbox) {
          checkbox.checked = true;
          checkbox.closest('.time-slot-chip').classList.add('selected');
        }
      });
    }, 100);
  } else {
    generateTimeSlots();
  }
  
  // Show sidebar
  sidebar.classList.remove('hidden');
  document.body.style.overflow = 'hidden';
}

function closeSidebar() {
  const sidebar = document.getElementById('addSlotSidebar');
  
  sidebar.classList.add('hidden');
  document.body.style.overflow = 'auto';
  resetForm();
}

function resetForm() {
  document.getElementById('addSlotForm').reset();
  document.getElementById('slot_id').value = '';
  document.getElementById('startTime').value = '08:00';
  document.getElementById('endTime').value = '20:00';
  const timeSlotsGrid = document.querySelector('.time-slots-grid');
  if (timeSlotsGrid) {
    timeSlotsGrid.innerHTML = '';
  }
}

document.getElementById('startTime').addEventListener('change', generateTimeSlots);
document.getElementById('endTime').addEventListener('change', generateTimeSlots);
document.getElementById('day').addEventListener('change', generateTimeSlots);

function generateTimeSlots() {
  const startTimeInput = document.getElementById('startTime').value;
  const endTimeInput = document.getElementById('endTime').value;
  
  if (!startTimeInput || !endTimeInput) return;
  
  const timeSlotsContainer = document.getElementById('timeSlots').querySelector('.time-slots-grid');
  timeSlotsContainer.innerHTML = '';
  
  const [startHours, startMinutes] = startTimeInput.split(':').map(Number);
  const [endHours, endMinutes] = endTimeInput.split(':').map(Number);
  
  const startTime = new Date();
  startTime.setHours(startHours, startMinutes, 0);
  
  const endTime = new Date();
  endTime.setHours(endHours, endMinutes, 0);
  
  if (startTime >= endTime) {
    alert('Start time must be before end time');
    return;
  }
  
  while (startTime < endTime) {
    const timeString = startTime.toLocaleTimeString('en-US', { 
      hour: '2-digit', 
      minute: '2-digit',
      hour12: true 
    });
    
    const slotDiv = document.createElement('div');
    slotDiv.className = 'time-slot-chip';
    const timeId = timeString.replace(/\s/g, '');
    slotDiv.innerHTML = `
      <input type="checkbox" name="time_slots[]" 
             value="${timeString}" id="slot_${timeId}">
      <label for="slot_${timeId}">
        ${timeString}
      </label>
    `;
    
    slotDiv.addEventListener('click', function() {
      const checkbox = this.querySelector('input[type="checkbox"]');
      checkbox.checked = !checkbox.checked;
      this.classList.toggle('selected', checkbox.checked);
    });
    
    timeSlotsContainer.appendChild(slotDiv);
    startTime.setMinutes(startTime.getMinutes() + 30);
  }
}

document.getElementById('addSlotForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  const saveBtn = document.getElementById('saveScheduleBtn');
  saveBtn.disabled = true;
  saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
  
  const formData = new FormData(this);
  const selectedSlots = Array.from(formData.getAll('time_slots[]'));
  const slotId = formData.get('slot_id');
  
  const data = {
    id: slotId,
    clinic_id: formData.get('clinic_id'),
    service_id: formData.get('service_id'),
    status: parseInt(formData.get('status')),
    day: formData.get('day'),
    time_slots: selectedSlots
  };
  
  const url = slotId ? `/services-schedule/${slotId}` : '/services-schedule';
  const method = 'POST';
  
  fetch(url, {
    method: method,
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(data)
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      location.reload();
    } else {
      alert('Error saving schedule: ' + data.message);
      saveBtn.disabled = false;
      saveBtn.innerHTML = 'Save Schedule';
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred while saving the schedule');
    saveBtn.disabled = false;
    saveBtn.innerHTML = 'Save Schedule';
  });
});

function deleteSlot(id) {
  if (confirm('Are you sure you want to delete this slot?')) {
    fetch(`/services-schedule/${id}/delete`, {
      method: 'GET',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        location.reload();
      } else {
        alert('Error deleting slot: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred while deleting the slot');
    });
  }
}

// Generate time slots when page loads
window.addEventListener('load', function() {
  document.getElementById('startTime').value = '08:00';
  document.getElementById('endTime').value = '20:00';
});

function clearFilters() {
  document.getElementById('serviceFilter').value = '';
  document.getElementById('dayFilter').value = '';
  filterSlots();
}

function filterSlots() {
  const searchInput = document.getElementById('scheduleSearchInput').value.toLowerCase();
  const serviceFilter = document.getElementById('serviceFilter').value;
  const dayFilter = document.getElementById('dayFilter').value;
  const rows = document.querySelectorAll('.slot-row');

  rows.forEach(row => {
    const searchText = row.dataset.search || '';
    const serviceMatch = !serviceFilter || row.dataset.service === serviceFilter;
    const dayMatch = !dayFilter || row.dataset.day === dayFilter;
    const searchMatch = !searchInput || searchText.includes(searchInput);
    
    row.style.display = (serviceMatch && dayMatch && searchMatch) ? '' : 'none';
  });
}
</script>

@endsection
