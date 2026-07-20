@extends('layouts.user_type.auth')

@section('content')
<div class="services-page">
  <div class="page-header">
    <h1 class="page-title"></h1>
    <div class="header-actions">
      <div class="search-wrapper">
        <x-ui.icon name="search" class="search-icon w-4 h-4" />
        <input 
          type="text" 
          id="serviceSearchInput"
          class="search-input"
          placeholder="Search services..." 
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
        onclick="openSidebar('serviceSidebar')"
      >
        Add New Service
      </x-ui.button>
      @endif
    </div>
  </div>

  <div class="services-content" style="background: white; border-radius: 16px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #f1f5f9; overflow: hidden;">

    <div class="services-table-wrapper">
      <table class="services-table" id="servicesTable">
        <thead>
          <tr>
            <th>Service</th>
            <th>Category</th>
            <th>Price</th>
            <th>Duration</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($services as $service)
          <tr data-name="{{ strtolower($service->name) }}" data-category="{{ $service->category }}">
            <td>
              <div class="service-info">
                <div class="service-name">{{ $service->name }}</div>
                <div class="service-description">{{ $service->description }}</div>
              </div>
            </td>
            <td>
              @php
                $categoryVariant = match($service->category) {
                  'vaccination' => 'primary',
                  'grooming' => 'info',
                  'deworming' => 'warning',
                  'checkup' => 'success',
                  'surgery' => 'danger',
                  default => 'secondary'
                };
              @endphp
              <x-ui.badge :variant="$categoryVariant">
                {{ ucfirst($service->category) }}
              </x-ui.badge>
            </td>
            <td>
              <span class="service-price">₱{{ number_format($service->price, 2) }}</span>
            </td>
            <td>
              <span class="service-duration">{{ $service->duration }} mins</span>
            </td>
            <td>
              <x-ui.badge :variant="$service->status === 'active' ? 'success' : 'danger'">
                {{ ucfirst($service->status) }}
              </x-ui.badge>
            </td>
            <td>
              <div class="actions-group">
                <x-ui.button 
                  variant="secondary" 
                  size="xs" 
                  icon-name="eye"
                  onclick="viewService({{ $service->id }})"
                  title="View Details"
                >View</x-ui.button>
                <x-ui.button 
                  variant="primary" 
                  size="xs" 
                  icon-name="edit"
                  onclick="editService({{ json_encode($service) }})"
                  title="Edit Service"
                >Edit</x-ui.button>
                <button class="btn-clean btn-danger-clean btn-xs-clean" onclick="deleteService({{ $service->id }})" title="Delete Service">
                  <i class="fas fa-trash-alt" style="font-size:12px;margin-right:4px"></i> Delete
                </button>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="text-center py-8">
              <div class="empty-state">
                <x-ui.icon name="notification" class="w-12 h-12 text-gray-400 mb-4" />
                <h3 class="text-lg font-medium text-gray-900 mb-2">No services found</h3>
                <p class="text-gray-500 mb-4">Start by adding your first service.</p>
                <button onclick="openSidebar('serviceSidebar')" class="btn btn-primary">
                  <x-ui.icon name="add" class="w-4 h-4" />Add Service
                </button>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Service Sidebar -->
<div id="serviceSidebar" class="sidebar-overlay hidden">
  <div class="sidebar-backdrop" onclick="closeSidebar('serviceSidebar')"></div>
  <div class="sidebar-content">
    <div class="sidebar-header">
      <h3 class="sidebar-title">Add New Service</h3>
      <button type="button" class="sidebar-close" onclick="closeSidebar('serviceSidebar')">
        <x-ui.icon name="close" class="w-3.5 h-3.5" />
      </button>
    </div>

    <div class="sidebar-body">
      <form id="serviceForm" method="POST" action="/services">
        @csrf
        <input type="hidden" name="service_id" id="serviceId">
        
        <!-- Form Fields -->
        <div class="form-fields">
          <div class="form-group">
            <label class="form-label">Service Name *</label>
            <input type="text" name="name" id="serviceName" class="form-input" required placeholder="Enter service name">
          </div>
          
          <div class="form-group">
            <label class="form-label">Category *</label>
            <select name="category" id="serviceCategory" class="form-select" required>
              <option value="">Select Category</option>
              <option value="vaccination">Vaccination</option>
              <option value="grooming">Grooming</option>
              <option value="deworming">Deworming</option>
              <option value="checkup">Check-up</option>
              <option value="surgery">Surgery</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Price (₱) *</label>
            <input type="number" step="0.01" min="0" name="price" id="servicePrice" class="form-input" required placeholder="Enter price">
          </div>

          <div class="form-group">
            <label class="form-label">Duration (minutes) *</label>
            <input type="number" min="1" name="duration" id="serviceDuration" class="form-input" required placeholder="Enter duration">
          </div>

          <div class="form-group">
            <label class="form-label">Status *</label>
            <select name="status" id="serviceStatus" class="form-select" required>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" id="serviceDescription" rows="3" class="form-input" placeholder="Enter service description..."></textarea>
          </div>
        </div>

        <div class="sidebar-actions">
          <button type="button" class="btn-secondary" onclick="closeSidebar('serviceSidebar')">
            Cancel
          </button>
          <button type="submit" class="btn-primary" id="saveServiceButton">
            Save Service
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Filter Modal -->
<x-ui.modal id="filterModal" title="Filter Services" size="sm">
  <div class="filter-form">
    <div class="form-group">
      <label class="form-label">Category</label>
      <select id="categoryFilter" class="form-select" onchange="filterTable()">
        <option value="">All Categories</option>
        <option value="vaccination">Vaccination</option>
        <option value="grooming">Grooming</option>
        <option value="deworming">Deworming</option>
        <option value="checkup">Check-up</option>
        <option value="surgery">Surgery</option>
      </select>
    </div>
    
    <div class="form-group">
      <label class="form-label">Status</label>
      <select id="statusFilter" class="form-select" onchange="filterTable()">
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
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

<style>
/* Services Page Layout */
.services-page {
  padding: 1.5rem;
  background: #fff;
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
  font-size: 1.5rem;
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

.services-table-wrapper {
  overflow-x: auto;
}

.services-table {
  width: 100%;
  border-collapse: collapse;
  font-family: 'Poppins', sans-serif;
}

.services-table thead {
  background: #f8fafc;
  border-bottom: 1px solid #e5e7eb;
}

.services-table th {
  padding: 1rem 1.5rem;
  text-align: left;
  font-weight: 600;
  font-size: 0.875rem;
  color: #374151;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-family: 'Poppins', sans-serif;
}

.services-table td {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}

.services-table tbody tr:hover {
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

.service-info {
  display: flex;
  flex-direction: column;
}

.service-name {
  font-weight: 600;
  color: #111827;
  font-size: 0.875rem;
  margin-bottom: 0.25rem;
}

.service-description {
  font-size: 0.75rem;
  color: #6b7280;
}

.service-price {
  font-weight: 600;
  color: #059669;
  font-size: 0.875rem;
}

.service-duration {
  font-size: 0.875rem;
  color: #374151;
}

.actions-group {
  display: flex;
  gap: 0.5rem;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.grid {
  display: grid;
}
.grid-cols-1 {
  grid-template-columns: repeat(1, minmax(0, 1fr));
}
.md\:grid-cols-2 {
  grid-template-columns: repeat(2, minmax(0, 1fr));
}

@media (max-width: 768px) {
  .services-page {
    padding: 1rem;
  }
  
  .page-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .md\:grid-cols-2 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
  }
  
  .actions-group {
    flex-direction: column;
    gap: 0.25rem;
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
  document.getElementById('categoryFilter').value = '';
  document.getElementById('statusFilter').value = '';
  filterTable();
}

function filterTable() {
  const searchTerm = document.getElementById('serviceSearchInput').value.toLowerCase();
  const categoryFilter = document.getElementById('categoryFilter').value.toLowerCase();
  const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
  const rows = document.querySelectorAll('#servicesTable tbody tr');

  rows.forEach(row => {
    const serviceName = row.querySelector('.service-name').textContent.toLowerCase();
    const serviceCategory = row.querySelector('.service-category').textContent.toLowerCase();
    const serviceStatus = row.querySelector('.status-badge').textContent.toLowerCase();
    
    const matchesSearch = serviceName.includes(searchTerm);
    const matchesCategory = !categoryFilter || serviceCategory.includes(categoryFilter);
    const matchesStatus = !statusFilter || serviceStatus.includes(statusFilter);
    
    row.style.display = (matchesSearch && matchesCategory && matchesStatus) ? '' : 'none';
  });
}

function editService(serviceData) {
  document.getElementById('serviceId').value = serviceData.id;
  document.getElementById('serviceName').value = serviceData.name;
  document.getElementById('serviceCategory').value = serviceData.category;
  document.getElementById('servicePrice').value = serviceData.price;
  document.getElementById('serviceDuration').value = serviceData.duration;
  document.getElementById('serviceStatus').value = serviceData.status;
  document.getElementById('serviceDescription').value = serviceData.description;
  
  document.getElementById('saveServiceButton').textContent = 'Update Service';
  document.querySelector('#serviceSidebar .sidebar-title').textContent = 'Edit Service';
  
  openSidebar('serviceSidebar');
}

async function deleteService(serviceId) {
  console.log('Delete service called with ID:', serviceId);
  if (confirm('Are you sure you want to delete this service?')) {
    try {
      console.log('Making delete request...');
      const response = await fetch(`/services/${serviceId}/delete`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
      });
      
      console.log('Response status:', response.status);
      const data = await response.json();
      console.log('Response data:', data);
      
      if (data.success) {
        // Remove the row from the table
        const button = document.querySelector(`[onclick="deleteService(${serviceId})"]`);
        if (button) {
          const row = button.closest('tr');
          if (row) {
            row.remove();
          }
        }
        alert('Service deleted successfully');
      } else {
        alert('Failed to delete service: ' + data.message);
      }
    } catch (error) {
      console.error('Error deleting service:', error);
      alert('Failed to delete service: ' + error.message);
    }
  }
}

function viewService(serviceId) {
  console.log('View service:', serviceId);
}

// Form submission
document.getElementById('serviceForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  const serviceId = document.getElementById('serviceId').value;
  
  try {
    const url = serviceId ? `/services/${serviceId}` : '/services';
    const response = await fetch(url, {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    });

    if (response.ok) {
      closeModal('serviceModal');
      window.location.reload();
    } else {
      const data = await response.json();
      alert(data.message || 'Failed to save service');
    }
  } catch (error) {
    console.error('Error saving service:', error);
    alert('Failed to save service');
  }
});

// Reset form when modal closes
document.addEventListener('DOMContentLoaded', function() {
  const modal = document.getElementById('serviceModal');
  if (modal) {
    const observer = new MutationObserver(function(mutations) {
      mutations.forEach(function(mutation) {
        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
          if (modal.classList.contains('hidden')) {
            document.getElementById('serviceForm').reset();
            document.getElementById('serviceId').value = '';
            document.getElementById('saveServiceButton').textContent = 'Save Service';
            const titleElement = document.querySelector('#serviceModal .modal-content h3');
            if (titleElement) titleElement.textContent = 'Add New Service';
          }
        }
      });
    });
    observer.observe(modal, { attributes: true });
  }
});
</script>
@endsection
