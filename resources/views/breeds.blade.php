@extends('layouts.user_type.auth')

@section('content')
<div class="breeds-page">
  <div class="page-header">
    <div class="header-content">
      <h1 class="page-title">Breed Management</h1>
      <p class="page-subtitle">Manage animal breeds and species classifications</p>
    </div>
    <div class="header-actions">
      <div class="search-wrapper">
        <x-ui.icon name="search" class="search-icon w-4 h-4" />
        <input 
          type="text" 
          id="breedSearchInput"
          class="search-input"
          placeholder="Search breeds..." 
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
        onclick="openSidebar('breedSidebar')"
      >
        Add New Breed
      </x-ui.button>
    </div>
  </div>

  <div class="breeds-content">
    <div class="breeds-table-wrapper">
      <table class="breeds-table" id="breedsTable">
        <thead>
          <tr>
            <th>Breed Name</th>
            <th>Species</th>
            <th>Clinic</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($breeds as $breed)
          <tr data-search="{{ strtolower($breed->name . ' ' . $breed->species->name) }}">
            <td>
              <div class="breed-name">{{ $breed->name }}</div>
            </td>
            <td>
              <x-ui.badge :variant="$breed->species->name === 'Dog' ? 'info' : 'warning'">
                {{ $breed->species->name }}
              </x-ui.badge>
            </td>
            <td>
              <div class="clinic-name">{{ $breed->clinic->name ?? 'N/A' }}</div>
            </td>
            <td>
              <div class="actions-group">
                <x-ui.button 
                  variant="primary" 
                  size="xs" 
                  icon-name="edit"
                  onclick="editBreed({{ $breed->toJson() }})"
                  title="Edit Breed"
                >Edit</x-ui.button>
                <button class="btn-clean btn-danger-clean btn-xs-clean" onclick="deleteBreed({{ $breed->id }})" title="Delete Breed">
                  <i class="fas fa-trash-alt" style="font-size:12px;margin-right:4px"></i> Delete
                </button>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    
    @if($breeds->hasPages())
    <div class="pagination-wrapper">
      {{ $breeds->links() }}
    </div>
    @endif
  </div>
</div>

<!-- Breed Sidebar -->
<div id="breedSidebar" class="sidebar-overlay hidden">
  <div class="sidebar-backdrop" onclick="closeSidebar('breedSidebar')"></div>
  <div class="sidebar-content">
    <div class="sidebar-header">
      <h3 class="sidebar-title" id="sidebarTitle">Add New Breed</h3>
      <button type="button" class="sidebar-close" onclick="closeSidebar('breedSidebar')">
        <x-ui.icon name="close" class="w-4 h-4" />
      </button>
    </div>
    
    <div class="sidebar-body">
      <form id="breedForm" method="POST" action="/breeds">
        @csrf
        <input type="hidden" name="breed_id" id="breedId">
        
        <div class="form-group">
          <label class="form-label">Breed Name *</label>
          <input type="text" 
                 class="form-input" 
                 name="name" 
                 id="breedName" 
                 required 
                 placeholder="Enter breed name">
        </div>

        <div class="form-group">
          <label class="form-label">Species *</label>
          <select class="form-input" name="species_id" id="speciesId" required>
            <option value="">Select Species</option>
            @foreach($species as $specie)
              <option value="{{ $specie->id }}">{{ $specie->name }}</option>
            @endforeach
          </select>
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
        onclick="closeSidebar('breedSidebar')"
      >
        Cancel
      </x-ui.button>
      <x-ui.button 
        type="submit" 
        variant="primary" 
        id="saveBreedButton"
        form="breedForm"
      >
        Save Breed
      </x-ui.button>
    </div>
  </div>
</div>

<!-- Filter Modal -->
<x-ui.modal id="filterModal" title="Filter Breeds" size="sm">
  <div class="filter-form">
    <div class="form-group">
      <label class="form-label">Species</label>
      <select id="speciesFilter" class="form-select" onchange="filterTable()">
        <option value="">All Species</option>
        @foreach($species as $specie)
          <option value="{{ strtolower($specie->name) }}">{{ $specie->name }}</option>
        @endforeach
      </select>
    </div>
    
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
/* Clean & Minimalist Breeds Styles */
.breeds-page {
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
.breeds-content {
  background: white;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  overflow: hidden;
  margin-top: 0;
}

.breeds-table-wrapper {
  overflow-x: auto;
}

.breeds-table {
  width: 100%;
  border-collapse: collapse;
  font-family: 'Poppins', sans-serif;
}

.breeds-table th {
  background: #f8fafc;
  padding: 1rem 1.5rem;
  text-align: left;
  font-weight: 600;
  font-size: 0.875rem;
  color: #374151;
  border-bottom: 1px solid #e5e7eb;
}

.breeds-table td {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
  font-size: 0.875rem;
}

.breeds-table tbody tr:hover {
  background: #f9fafb;
}

.breed-name {
  font-weight: 500;
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

.pagination-wrapper {
  padding: 1rem 1.5rem;
  border-top: 1px solid #f1f5f9;
  background: #f8fafc;
}

@media (max-width: 768px) {
  .breeds-page { padding: 1rem; }
  .page-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
  .header-actions { flex-direction: column; width: 100%; gap: 0.75rem; }
  .header-actions .search-wrapper { width: 100%; }
}
</style>

<script>
function filterTable() {
  const searchTerm = document.getElementById('breedSearchInput').value.toLowerCase();
  const speciesFilter = document.getElementById('speciesFilter').value.toLowerCase();
  const clinicFilter = document.getElementById('clinicFilter') ? document.getElementById('clinicFilter').value.toLowerCase() : '';
  const rows = document.querySelectorAll('#breedsTable tbody tr');

  rows.forEach(row => {
    const searchData = row.dataset.search || '';
    const speciesCell = row.cells[1].textContent.toLowerCase();
    const clinicCell = row.cells[2].textContent.toLowerCase();
    
    const matchesSearch = searchData.includes(searchTerm);
    const matchesSpecies = !speciesFilter || speciesCell.includes(speciesFilter);
    const matchesClinic = !clinicFilter || clinicCell.includes(clinicFilter);
    
    row.style.display = (matchesSearch && matchesSpecies && matchesClinic) ? '' : 'none';
  });
}

function clearFilters() {
  document.getElementById('breedSearchInput').value = '';
  document.getElementById('speciesFilter').value = '';
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
  document.getElementById('breedForm').reset();
  document.getElementById('breedId').value = '';
  document.getElementById('saveBreedButton').textContent = 'Save Breed';
  document.getElementById('sidebarTitle').textContent = 'Add New Breed';
}

function editBreed(breed) {
  document.getElementById('breedId').value = breed.id;
  document.getElementById('breedName').value = breed.name;
  document.getElementById('speciesId').value = breed.species_id;
  if (document.getElementById('clinicId')) {
    document.getElementById('clinicId').value = breed.clinic_id;
  }
  
  document.getElementById('saveBreedButton').textContent = 'Update Breed';
  document.getElementById('sidebarTitle').textContent = 'Edit Breed';
  
  openSidebar('breedSidebar');
}

async function deleteBreed(breedId) {
  if (confirm('Are you sure you want to delete this breed?')) {
    try {
      const response = await fetch(`/breeds/${breedId}/delete`, {
        method: 'GET',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
      });
      
      if (response.ok) {
        window.location.reload();
      } else {
        alert('Error deleting breed');
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Error deleting breed');
    }
  }
}

// Form submission
document.getElementById('breedForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  const breedId = document.getElementById('breedId').value;
  
  try {
    const url = breedId ? `/breeds/${breedId}` : '/breeds';
    const response = await fetch(url, {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    });

    if (response.ok) {
      closeSidebar('breedSidebar');
      window.location.reload();
    } else {
      const data = await response.json();
      alert(data.message || 'Failed to save breed');
    }
  } catch (error) {
    console.error('Error saving breed:', error);
    alert('Failed to save breed');
  }
});
</script>
@endsection
