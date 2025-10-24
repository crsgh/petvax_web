@extends('layouts.user_type.auth')

@section('content')
<div class="categories-page">
  <div class="page-header">
    <h1 class="page-title"></h1>
    <div class="header-actions">
      <div class="search-wrapper">
        <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8"></circle>
          <path d="m21 21-4.35-4.35"></path>
        </svg>
        <input 
          type="text" 
          id="activitySearchInput"
          class="search-input"
          placeholder="Search activities..." 
          onkeyup="filterTable()"
        />
      </div>
      
      <x-ui.button 
        variant="secondary" 
        size="default" 
        icon="fas fa-filter"
        onclick="openModal('filterModal')"
      >
        Filters
      </x-ui.button>
      <x-ui.button variant="primary" size="default" icon="fas fa-download">Export Logs</x-ui.button>
    </div>
  </div>
  <div class="activity-content">
    <div class="activity-table-wrapper">
      <table class="activity-table" id="activityTable">
        <thead>
          <tr>
            <th>User</th>
            <th>Action</th>
            <th>Module</th>
            <th>Details</th>
            <th>IP Address</th>
            <th>Date & Time</th>
          </tr>
        </thead>
        <tbody>
          @php
            $sampleActivities = [
              ['user' => 'Dr. Smith', 'action' => 'Created', 'module' => 'Appointment', 'details' => 'New appointment for Max (Dog)', 'ip' => '192.168.1.100', 'datetime' => '2024-01-15 14:30:00'],
              ['user' => 'Admin User', 'action' => 'Updated', 'module' => 'User Management', 'details' => 'Modified user permissions', 'ip' => '192.168.1.101', 'datetime' => '2024-01-15 13:45:00'],
              ['user' => 'Dr. Johnson', 'action' => 'Deleted', 'module' => 'Inventory', 'details' => 'Removed expired medication', 'ip' => '192.168.1.102', 'datetime' => '2024-01-15 12:20:00'],
              ['user' => 'Receptionist', 'action' => 'Created', 'module' => 'Pet Registration', 'details' => 'Registered new pet: Bella (Cat)', 'ip' => '192.168.1.103', 'datetime' => '2024-01-15 11:15:00'],
              ['user' => 'Dr. Smith', 'action' => 'Updated', 'module' => 'Medical Record', 'details' => 'Added vaccination record', 'ip' => '192.168.1.100', 'datetime' => '2024-01-15 10:30:00'],
            ];
          @endphp
          
          @foreach($sampleActivities as $activity)
          <tr data-search="{{ strtolower($activity['user'] . ' ' . $activity['action'] . ' ' . $activity['module'] . ' ' . $activity['details']) }}">
            <td>
              <div class="user-name">{{ $activity['user'] }}</div>
            </td>
            <td>
              <x-ui.badge variant="{{ $activity['action'] == 'Created' ? 'success' : ($activity['action'] == 'Updated' ? 'primary' : 'danger') }}">
                {{ $activity['action'] }}
              </x-ui.badge>
            </td>
            <td>
              <div class="module-name">{{ $activity['module'] }}</div>
            </td>
            <td>
              <div class="activity-details">{{ $activity['details'] }}</div>
            </td>
            <td>
              <div class="ip-address">{{ $activity['ip'] }}</div>
            </td>
            <td>
              <div class="activity-datetime">
                <div class="activity-date">{{ \Carbon\Carbon::parse($activity['datetime'])->format('M d, Y') }}</div>
                <div class="activity-time">{{ \Carbon\Carbon::parse($activity['datetime'])->format('g:i A') }}</div>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<style>
.categories-page { 
  padding: 1.5rem; 
  background: #fff; 
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

/* Clean Table Styles */
.activity-content {
  background: white;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  overflow: hidden;
  margin-top: 0;
}

.activity-table-wrapper {
  overflow-x: auto;
}

.activity-table {
  width: 100%;
  border-collapse: collapse;
  font-family: 'Poppins', sans-serif;
}

.activity-table th {
  background: #f8fafc;
  padding: 1rem 1.5rem;
  text-align: left;
  font-weight: 600;
  font-size: 0.875rem;
  color: #374151;
  border-bottom: 1px solid #e5e7eb;
}

.activity-table td {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
  font-size: 0.875rem;
}

.activity-table tbody tr:hover {
  background: #f9fafb;
}

.user-name {
  color: #111827;
  font-weight: 500;
}

.module-name {
  color: #111827;
}

.activity-details {
  color: #6b7280;
  max-width: 250px;
}

.ip-address {
  color: #6b7280;
  font-family: 'Monaco', 'Menlo', monospace;
  font-size: 0.8125rem;
}

.activity-datetime {
  color: #6b7280;
}

.activity-date {
  font-weight: 500;
  color: #111827;
}

.activity-time {
  font-size: 0.8125rem;
  color: #6b7280;
}

@media (max-width: 768px) { 
  .categories-page { padding: 1rem; } 
  .page-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
  .header-actions { flex-direction: column; width: 100%; gap: 0.75rem; }
  .header-actions .search-wrapper { width: 100%; }
}
</style>

<script>
function filterTable() {
  const searchTerm = document.getElementById('activitySearchInput').value.toLowerCase();
  const rows = document.querySelectorAll('#activityTable tbody tr');

  rows.forEach(row => {
    const searchData = row.dataset.search || '';
    const matchesSearch = searchData.includes(searchTerm);
    
    row.style.display = matchesSearch ? '' : 'none';
  });
}

function clearFilters() {
  document.getElementById('actionFilter').value = '';
  document.getElementById('moduleFilter').value = '';
  filterTable();
}
</script>

<!-- Filter Modal -->
<x-ui.modal id="filterModal" title="Filter Activities" size="sm">
  <div class="filter-form">
    <div class="form-group">
      <label class="form-label">Action</label>
      <select id="actionFilter" class="form-select" onchange="filterTable()">
        <option value="">All Actions</option>
        <option value="created">Created</option>
        <option value="updated">Updated</option>
        <option value="deleted">Deleted</option>
      </select>
    </div>
    
    <div class="form-group">
      <label class="form-label">Module</label>
      <select id="moduleFilter" class="form-select" onchange="filterTable()">
        <option value="">All Modules</option>
        <option value="appointment">Appointment</option>
        <option value="user management">User Management</option>
        <option value="inventory">Inventory</option>
        <option value="pet registration">Pet Registration</option>
        <option value="medical record">Medical Record</option>
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

@endsection
