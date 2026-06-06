@extends('layouts.user_type.auth')

@section('content')
<div class="species-page">
  <div class="page-header">
    <div class="header-content">
      <h1 class="page-title">Species Management</h1>
      <p class="page-subtitle">Manage animal species classifications</p>
    </div>
    <div class="header-actions">
      <div class="search-wrapper">
        <x-ui.icon name="search" class="search-icon w-4 h-4" />
        <input 
          type="text" 
          id="speciesSearchInput"
          class="search-input"
          placeholder="Search species..." 
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
      
      <x-ui.button 
        variant="primary" 
        size="default" 
        icon-name="add"
        onclick="openSidebar('speciesSidebar')"
      >
        Add New Species
      </x-ui.button>
    </div>
  </div>

  <div class="species-content">
    <div class="species-table-wrapper">
      <table class="species-table" id="speciesTable">
        <thead>
          <tr>
            <th>Species Name</th>
            <th>Clinic</th>
            <th>Breeds Count</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($species as $specie)
          <tr data-search="{{ strtolower($specie->name) }}">
            <td>
              <div class="species-info">
                <div class="species-name">{{ $specie->name }}</div>
                <div class="species-id">ID: #{{ $specie->id }}</div>
              </div>
            </td>
            <td>
              <div class="clinic-name">{{ $specie->clinic->name ?? 'N/A' }}</div>
            </td>
            <td>
              <x-ui.badge variant="info">
                {{ $specie->breeds_count ?? 0 }} breeds
              </x-ui.badge>
            </td>
            <td>
              <div class="actions-group">
                <x-ui.button 
                  variant="primary" 
                  size="xs" 
                  icon-name="edit"
                  onclick="editSpecies({{ $specie->toJson() }})"
                  title="Edit Species"
                >Edit</x-ui.button>
                <x-ui.button 
                  variant="danger" 
                  size="xs" 
                  icon-name="delete"
                  onclick="deleteSpecies({{ $specie->id }})"
                  title="Delete Species"
                >Delete</x-ui.button>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Species Sidebar -->
<div id="speciesSidebar" class="sidebar-overlay hidden">
  <div class="sidebar-backdrop" onclick="closeSidebar('speciesSidebar')"></div>
  <div class="sidebar-content">
    <div class="sidebar-header">
      <h3 class="sidebar-title" id="sidebarTitle">Add New Species</h3>
      <button type="button" class="sidebar-close" onclick="closeSidebar('speciesSidebar')">
        <x-ui.icon name="close" class="w-4 h-4" />
      </button>
    </div>
    
    <div class="sidebar-body">
      <form id="speciesForm" method="POST" action="/species">
        @csrf
        <input type="hidden" name="species_id" id="speciesId">
        
        <div class="form-group">
          <label class="form-label">Species Name *</label>
          <input type="text" 
                 class="form-input" 
                 name="name" 
                 id="speciesName" 
                 required 
                 placeholder="Enter species name">
        </div>

        @if(auth()->user()->role_id == 1)
        <div class="form-group">
          <label class="form-label">Clinic *</label>
          <select class="form-input" name="clinic_id" id="clinicId" required>
            <option value="">Select Clinic</option>
            @foreach($clinics as $clinic)
              <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
            @endforeach
          </select>
        </div>
        @else
          <input type="hidden" name="clinic_id" value="{{ auth()->user()->clinic_id }}">
        @endif
      </form>
    </div>
    
    <div class="sidebar-footer">
      <x-ui.button 
        type="button" 
        variant="secondary" 
        onclick="closeSidebar('speciesSidebar')"
      >
        Cancel
      </x-ui.button>
      <x-ui.button 
        type="submit" 
        variant="primary" 
        id="saveSpeciesButton"
        form="speciesForm"
      >
        Save Species
      </x-ui.button>
    </div>
  </div>
</div>

<!-- Filter Modal -->
<x-ui.modal id="filterModal" title="Filter Species" size="sm">
  <div class="filter-form">
    @if(auth()->user()->role_id == 1)
    <div class="form-group">
      <label class="form-label">Clinic</label>
      <select id="clinicFilter" class="form-select" onchange="filterTable()">
        <option value="">All Clinics</option>
        @foreach($clinics as $clinic)
          <option value="{{ strtolower($clinic->name) }}">{{ $clinic->name }}</option>
        @endforeach
      </select>
    </div>
    @endif
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
/* Clean & Minimalist Species Styles */
.species-page {
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
  margin: 0;
  letter-spacing: -0.025em;
  font-family: var(--font-family), sans-serif;
}

.page-subtitle {
  color: var(--secondary-font-color);
  font-size: 0.875rem;
  margin: 0;
  font-family: var(--font-family), sans-serif;
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

/* Clean Table Styles */
.species-content {
  background: white;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  overflow: hidden;
  margin-top: 0;
}

.species-table-wrapper {
  overflow-x: auto;
}

.species-table {
  width: 100%;
  border-collapse: collapse;
  font-family: 'Poppins', sans-serif;
}

.species-table th {
  background: #f8fafc;
  padding: 1rem 1.5rem;
  text-align: left;
  font-weight: 600;
  font-size: 0.875rem;
  color: #374151;
  border-bottom: 1px solid #e5e7eb;
}

.species-table td {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
  font-size: 0.875rem;
}

.species-table tbody tr:hover {
  background: #f9fafb;
}

.species-info {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.species-name {
  font-weight: 500;
}

.species-id {
  font-size: 0.75rem;
  color: #6b7280;
}

.clinic-name {
  color: #6b7280;
  font-size: 0.875rem;
}

.actions-group {
  display: flex;
  gap: 0.5rem;
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
  width: 400px;
  max-width: 90vw;
  background: var(--sidebar-bg-color);
  box-shadow: -4px 0 24px rgba(0, 0, 0, 0.15);
  display: flex;
  flex-direction: column;
  height: 100vh;
  transform: translateX(100%);
  transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.sidebar-overlay:not(.hidden) .sidebar-content {
  transform: translateX(0);
}

.sidebar-header {
  padding: 1.5rem 2rem;
  border-bottom: 1px solid var(--sidebar-border-color);
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: var(--sidebar-header-color);
}

.sidebar-title {
  font-size: 1.25rem;
  font-weight: 600;
  margin: 0;
  font-family: var(--font-family), sans-serif;
}

.sidebar-close {
  background: none;
  border: none;
  color: var(--secondary-font-color);
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.sidebar-close:hover {
  background: var(--sidebar-border-color);
  color: var(--font-color);
}

.sidebar-body {
  flex: 1;
  overflow-y: auto;
  padding: 2rem;
  background: var(--sidebar-bg-color);
}

.sidebar-footer {
  padding: 1.5rem 2rem;
  border-top: 1px solid var(--sidebar-border-color);
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  background: var(--sidebar-header-color);
}

/* Form Styles */
.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  margin-bottom: 0.5rem;
  font-family: var(--font-family), sans-serif;
}

.form-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid var(--sidebar-border-color);
  border-radius: 8px;
  font-size: var(--font-size);
  transition: all 0.2s ease;
  background: var(--sidebar-bg-color);
  color: var(--font-color);
  font-family: var(--font-family), sans-serif;
}

.form-input:focus {
  outline: none;
  border-color: var(--primary-color);
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

@media (max-width: 768px) {
  .species-page { padding: 1rem; }
  .page-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
  .header-actions { flex-direction: column; width: 100%; gap: 0.75rem; }
  .header-actions .search-wrapper { width: 100%; }
}
</style>

<script>
function filterTable() {
  const searchTerm = document.getElementById('speciesSearchInput').value.toLowerCase();
  const clinicFilter = document.getElementById('clinicFilter') ? document.getElementById('clinicFilter').value.toLowerCase() : '';
  const rows = document.querySelectorAll('#speciesTable tbody tr');

  rows.forEach(row => {
    const searchData = row.dataset.search || '';
    const clinicCell = row.cells[1].textContent.toLowerCase();
    
    const matchesSearch = searchData.includes(searchTerm);
    const matchesClinic = !clinicFilter || clinicCell.includes(clinicFilter);
    
    row.style.display = (matchesSearch && matchesClinic) ? '' : 'none';
  });
}

function clearFilters() {
  document.getElementById('speciesSearchInput').value = '';
  if (document.getElementById('clinicFilter')) {
    document.getElementById('clinicFilter').value = '';
  }
  filterTable();
}

// Sidebar functions
function openSidebar(sidebarId) {
  document.getElementById(sidebarId).classList.remove('hidden');
  document.body.style.overflow = 'hidden';
}

function closeSidebar(sidebarId) {
  document.getElementById(sidebarId).classList.add('hidden');
  document.body.style.overflow = 'auto';
  
  // Reset form when closing
  document.getElementById('speciesForm').reset();
  document.getElementById('speciesId').value = '';
  document.getElementById('saveSpeciesButton').textContent = 'Save Species';
  document.getElementById('sidebarTitle').textContent = 'Add New Species';
}

function editSpecies(species) {
  document.getElementById('speciesId').value = species.id;
  document.getElementById('speciesName').value = species.name;
  if (document.getElementById('clinicId')) {
    document.getElementById('clinicId').value = species.clinic_id;
  }
  
  document.getElementById('saveSpeciesButton').textContent = 'Update Species';
  document.getElementById('sidebarTitle').textContent = 'Edit Species';
  
  openSidebar('speciesSidebar');
}

async function deleteSpecies(speciesId) {
  if (confirm('Are you sure you want to delete this species?')) {
    try {
      const response = await fetch(`/species/${speciesId}/delete`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
      });
      
      if (response.ok) {
        window.location.reload();
      } else {
        alert('Error deleting species');
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Error deleting species');
    }
  }
}

// Form submission
document.getElementById('speciesForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  const speciesId = document.getElementById('speciesId').value;
  
  try {
    const url = speciesId ? `/species/${speciesId}` : '/species';
    const response = await fetch(url, {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    });

    if (response.ok) {
      closeSidebar('speciesSidebar');
      window.location.reload();
    } else {
      const data = await response.json();
      alert(data.message || 'Failed to save species');
    }
  } catch (error) {
    console.error('Error saving species:', error);
    alert('Failed to save species');
  }
});
</script>
@endsection
