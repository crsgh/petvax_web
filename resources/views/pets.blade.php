@extends('layouts.user_type.auth')

@section('content')
<div class="pets-page">
  <div class="page-header">
    <h1 class="page-title"></h1>
    <div class="header-actions">
      <div class="search-wrapper">
        <x-ui.icon name="search" class="search-icon w-4 h-4" />
        <input 
          type="text" 
          id="petSearchInput" 
          class="search-input"
          placeholder="Search pets..." 
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
        onclick="openSidebar('petSidebar')"
      >
        Add New Pet
      </x-ui.button>
      @endif
    </div>
  </div>

  <div class="pets-content" style="background: white; border-radius: 16px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #f1f5f9; overflow: hidden;">

    <div class="pets-table-wrapper">

      <table class="pets-table" id="petsTable">
        <thead>
          <tr>
            <th>Pet</th>
            <th>Owner</th>
            <th>Species & Breed</th>
            <th>Age & Gender</th>
            <th>Weight</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($pets as $pet)
          <tr data-pet-name="{{ strtolower($pet->name) }}" 
              data-owner-name="{{ strtolower($pet->petOwner->name ?? '') }}" 
              data-species="{{ strtolower($pet->species ?? '') }}" 
              data-gender="{{ strtolower($pet->gender) }}">
            <td>
              <div class="pet-info">
                <div class="pet-avatar">
                  @if($pet->image)
                    <img src="{{ asset('storage/' . $pet->image) }}" alt="{{ $pet->name }}" class="pet-image">
                  @else
                    <div class="pet-placeholder">
                      <x-ui.icon name="pets" class="w-5 h-5" />
                    </div>
                  @endif
                </div>
                <div class="pet-details">
                  <div class="pet-name">{{ $pet->name }}</div>
                  <div class="pet-id">ID: #{{ $pet->id }}</div>
                </div>
              </div>
            </td>
            <td>
              <div class="owner-info">
                <div class="owner-name">{{ $pet->petOwner->name ?? 'N/A' }}</div>
                <div class="owner-email">{{ $pet->petOwner->email ?? 'N/A' }}</div>
              </div>
            </td>
            <td>
              <div class="species-info">
                <div class="species-name">{{ $pet->species ?? 'Unknown' }}</div>
                <div class="breed-name">{{ $pet->breed ?? 'Mixed' }}</div>
              </div>
            </td>
            <td>
              <div class="age-gender-info">
                <div class="pet-age">
                  @if($pet->birth_date)
                    {{ \Carbon\Carbon::parse($pet->birth_date)->age }} years old
                  @else
                    Age unknown
                  @endif
                </div>
                <div class="pet-gender">
                  <x-ui.badge :variant="$pet->gender === 'male' ? 'info' : 'warning'">
                    {{ ucfirst($pet->gender) }}
                  </x-ui.badge>
                </div>
              </div>
            </td>
            <td>
              <div class="weight-info">
                @if($pet->weight)
                  <span class="weight-value">{{ $pet->weight }} kg</span>
                @else
                  <span class="text-gray-500">Not recorded</span>
                @endif
              </div>
            </td>
            <td>
              @php
                $isActive = true; // You can add an active status field to pets table
                $statusVariant = $isActive ? 'success' : 'danger';
                $statusText = $isActive ? 'Active' : 'Inactive';
              @endphp
              <x-ui.badge :variant="$statusVariant">
                {{ $statusText }}
              </x-ui.badge>
            </td>
            <td>
              <div class="flex gap-2">
                <x-ui.button 
                  variant="secondary" 
                  size="xs" 
                  icon-name="eye"
                  onclick="viewPet({{ $pet->id }})"
                  title="View Details"
                >View</x-ui.button>
                @if(auth()->user()->role_id != 4)
                  <x-ui.button 
                    variant="primary" 
                    size="xs" 
                    icon-name="edit"
                    onclick="editPet({{ $pet }})"
                    title="Edit Pet"
                  >Edit</x-ui.button>
                  <x-ui.button 
                    variant="danger" 
                    size="xs" 
                    icon-name="delete"
                    onclick="deletePet({{ $pet->id }})"
                    title="Delete Pet"
                  >Delete</x-ui.button>
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="empty-state">
              <div class="empty-state-content">
                <div class="empty-state-icon">
                  <x-ui.icon name="pets" class="w-16 h-16 text-gray-300" />
                </div>
                <h3 class="empty-state-title">No Pets Found</h3>
                <p class="empty-state-description">There are no pets registered yet. Add your first pet to get started.</p>
                @if(in_array(auth()->user()->role_id, [1, 2, 3]))
                <button class="empty-state-action" onclick="openSidebar('petSidebar')">
                  <x-ui.icon name="add" class="w-5 h-5" />
                  Add First Pet
                </button>
                @endif
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Pet Sidebar -->
<div id="petSidebar" class="sidebar-overlay hidden">
  <div class="sidebar-backdrop" onclick="closeSidebar('petSidebar')"></div>
  <div class="sidebar-content">
    <div class="sidebar-header">
      <h3 class="sidebar-title">Add New Pet</h3>
      <button type="button" class="sidebar-close" onclick="closeSidebar('petSidebar')">
        <x-ui.icon name="close" class="w-3.5 h-3.5" />
      </button>
    </div>

    <div class="sidebar-body">
      <form id="petForm" enctype="multipart/form-data" method="POST" action="/pets">
        @csrf
        <input type="hidden" name="pet_id" id="petId">
        
        <!-- Pet Image -->
        <div class="form-section">
          <label class="form-label">Pet Photo</label>
          <div class="profile-image-center">
            <div class="profile-image-upload" onclick="document.getElementById('petImageInputSidebar').click()">
              <img id="petImagePreview" src="{{ asset('assets/img/default-pet.jpg') }}" alt="Pet Preview" class="profile-preview">
              <div class="upload-overlay">
                <x-ui.icon name="image" class="w-5 h-5" />
                <span>Add Photo</span>
              </div>
              <input type="file" name="image" id="petImageInputSidebar" accept="image/*" onchange="previewPetImage(this)" style="display: none;">
            </div>
            <div class="upload-hint">Click to upload photo</div>
          </div>
        </div>

        <!-- Form Fields -->
        <div class="form-fields">
          <div class="form-group">
            <label class="form-label">Pet Name *</label>
            <input type="text" name="name" id="petNameSidebar" class="form-input" required placeholder="Enter pet name">
          </div>
          
          <div class="form-group">
            <label class="form-label">Owner *</label>
            <select name="owner_id" id="petOwner" class="form-select" required>
              <option value="">Select Owner</option>
              @if(isset($owners))
                @foreach($owners as $owner)
                  <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                @endforeach
              @endif
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Species *</label>
            <select name="species_id" id="petSpeciesSidebar" class="form-select" required onchange="loadBreedsBySpecies(this.value)">
              <option value="">Select Species</option>
              @if(isset($species))
                @foreach($species as $specie)
                  <option value="{{ $specie->id }}">{{ ucfirst($specie->name) }}</option>
                @endforeach
              @endif
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Breed *</label>
            <select name="breed_id" id="petBreedSidebar" class="form-select" required>
              <option value="">Select Breed</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Gender *</label>
            <select name="gender" id="petGenderSidebar" class="form-select" required>
              <option value="">Select Gender</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Birth Date *</label>
            <input type="date" name="birth_date" id="petBirthDateSidebar" class="form-input" required>
          </div>

          <div class="form-group">
            <label class="form-label">Weight (kg)</label>
            <input type="number" step="0.1" name="weight" id="petWeightSidebar" class="form-input" placeholder="Enter weight">
          </div>

          <div class="form-group">
            <label class="form-label">Color</label>
            <input type="text" name="color" id="petColor" class="form-input" placeholder="Enter pet color">
          </div>
        </div>

        <div class="sidebar-actions">
          <button type="button" class="btn-secondary" onclick="closeSidebar('petSidebar')">
            Cancel
          </button>
          <button type="submit" class="btn-primary" id="savePetButtonSidebar">
            Save Pet
          </button>
        </div>
    </div>
  </div>
</div>

<!-- Pet Details Modal -->
<x-ui.modal id="petDetailsModal" title="Pet Details" size="lg">
  <div id="petDetailsContent">
    <!-- Content will be populated by JavaScript -->
  </div>
</x-ui.modal>

<!-- Pet Edit Modal - REMOVED TO PREVENT FORM CONFLICTS -->

<style>
/* Pets Page Layout */
.pets-page {
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

.pets-table-wrapper {
  overflow-x: auto;
}

.pets-table {
  width: 100%;
  border-collapse: collapse;
  font-family: 'Poppins', sans-serif;
}

.pets-table thead {
  background: #f8fafc;
  border-bottom: 1px solid #e5e7eb;
}

.pets-table th {
  padding: 1rem 1.5rem;
  text-align: left;
  font-weight: 600;
  font-size: 0.875rem;
  color: #374151;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-family: 'Poppins', sans-serif;
}

.pets-table td {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}

.pets-table tbody tr:hover {
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

.form-section {
  margin-bottom: 2rem;
}

.profile-image-center {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.profile-image-upload {
  position: relative;
  cursor: pointer;
  border-radius: 50%;
  overflow: hidden;
  width: 120px;
  height: 120px;
  border: 4px solid #e5e7eb;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.profile-image-upload:hover {
  border-color: #3b82f6;
  transform: scale(1.02);
  box-shadow: 0 8px 20px rgba(59, 130, 246, 0.2);
}

.profile-preview {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.upload-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: white;
  opacity: 0;
  transition: opacity 0.3s ease;
  font-size: 0.875rem;
}

.profile-image-upload:hover .upload-overlay {
  opacity: 1;
}

.upload-hint {
  font-size: 0.75rem;
  color: #6b7280;
  text-align: center;
  font-weight: 500;
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
  
  .profile-image-upload {
    width: 100px;
    height: 100px;
  }
}

.filter-label {
  font-size: var(--font-size-xs);
  font-weight: 500;
  color: var(--gray-600);
  margin-bottom: var(--space-1);
}

.table-container {
  overflow-x: auto;
  border-radius: var(--radius-lg);
  border: 1px solid var(--gray-200);
}

.pet-info {
  display: flex;
  align-items: center;
  gap: var(--space-3);
}

.pet-avatar {
  width: 48px;
  height: 48px;
  border-radius: var(--radius-lg);
  overflow: hidden;
  flex-shrink: 0;
}

.pet-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.pet-placeholder {
  width: 100%;
  height: 100%;
  background: var(--gray-200);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--gray-500);
  font-size: 20px;
}

.pet-name {
  font-weight: 600;
  color: var(--gray-900);
  font-size: var(--font-size-sm);
}

.pet-id {
  font-size: var(--font-size-xs);
  color: var(--gray-500);
}

.owner-name {
  font-weight: 500;
  color: var(--gray-900);
  font-size: var(--font-size-sm);
}

.owner-email {
  font-size: var(--font-size-xs);
  color: var(--gray-500);
}

.species-name {
  font-weight: 500;
  color: var(--gray-900);
  font-size: var(--font-size-sm);
}

.breed-name {
  font-size: var(--font-size-xs);
  color: var(--gray-500);
}

.pet-age {
  font-size: var(--font-size-sm);
  color: var(--gray-700);
  margin-bottom: var(--space-1);
}

.weight-value {
  font-weight: 500;
  color: var(--gray-900);
  font-size: var(--font-size-sm);
}

.pet-image-preview {
  width: 80px;
  height: 80px;
  border-radius: var(--radius-lg);
  overflow: hidden;
  border: 2px solid var(--gray-200);
}

.pet-image-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
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
.md\:col-span-2 {
  grid-column: span 2 / span 2;
}

@media (max-width: 768px) {
  .md\:grid-cols-2 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
  }
  .md\:col-span-2 {
    grid-column: span 1 / span 1;
  }
  
  .filter-group {
    min-width: 120px;
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

function filterTable() {
  const petSearchInput = document.getElementById('petSearchInput').value.toLowerCase();
  const speciesFilter = document.getElementById('speciesFilter').value;
  const genderFilter = document.getElementById('genderFilter').value;
  const table = document.getElementById('petsTable');
  const rows = table.querySelectorAll('tbody tr');

  rows.forEach(row => {
    const cells = row.querySelectorAll('td');
    if (cells.length > 0) {
      const petName = cells[0] ? cells[0].textContent.toLowerCase() : '';
      const ownerName = cells[1] ? cells[1].textContent.toLowerCase() : '';
      const species = cells[2] ? cells[2].textContent.toLowerCase() : '';
      const gender = cells[3] ? cells[3].textContent.toLowerCase() : '';

      const matchesPetSearch = petName.includes(petSearchInput) || ownerName.includes(petSearchInput);
      const matchesSpecies = !speciesFilter || species.includes(speciesFilter.toLowerCase());
      const matchesGender = !genderFilter || gender.includes(genderFilter.toLowerCase());

      row.style.display = matchesPetSearch && matchesSpecies && matchesGender ? '' : 'none';
    }
  });
}

function resetFilters() {
  document.getElementById('petSearchInput').value = '';
  document.getElementById('ownerSearchInput').value = '';
  document.getElementById('speciesFilter').value = '';
  document.getElementById('genderFilter').value = '';
  filterTable();
}


function previewPetImage(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('petImagePreview').src = e.target.result;
    }
    reader.readAsDataURL(input.files[0]);
  }
}

function editPet(petData) {
  document.getElementById('petEditId').value = petData.id;
  document.getElementById('petName').value = petData.name;
  document.getElementById('petOwnerId').value = petData.owner_id;
  document.getElementById('petSpeciesId').value = petData.species_id;
  document.getElementById('petGender').value = petData.gender;
  document.getElementById('petBirthDate').value = petData.birth_date;
  document.getElementById('petWeight').value = petData.weight;
  document.getElementById('petClinicId').value = petData.clinic_id;
  
  // Load breeds for the selected species
  if (petData.species_id) {
    loadBreedsBySpecies(petData.species_id).then(() => {
      document.getElementById('petBreedId').value = petData.breed_id;
    });
  }
  
  document.getElementById('savePetButton').textContent = 'Update Pet';
  document.querySelector('#petModal .modal-title').textContent = 'Edit Pet';
  
  openModal('petModal');
}

async function deletePet(petId) {
  if (confirm('Are you sure you want to delete this pet? This action cannot be undone.')) {
    try {
      const response = await fetch(`/pets/${petId}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
      });
      
      if (response.ok) {
        window.location.reload();
      } else {
        alert('Failed to delete pet');
      }
    } catch (error) {
      console.error('Error deleting pet:', error);
      alert('Failed to delete pet');
    }
  }
}

async function viewPet(petId) {
  try {
    const response = await fetch(`/pets/${petId}`);
    const pet = await response.json();
    
    const content = `
      <div class="pet-details-view">
        <div class="pet-header">
          <div class="pet-image-large">
            <img src="${pet.image ? '{{ asset("storage/") }}/' + pet.image : '{{ asset("assets/img/default-pet.jpg") }}'}" alt="${pet.name}">
          </div>
          <div class="pet-basic-info">
            <h3>${pet.name}</h3>
            <p class="pet-owner">Owner: ${pet.pet_owner?.name || 'N/A'}</p>
            <p class="pet-id">Pet ID: #${pet.id}</p>
          </div>
        </div>
        
        <div class="pet-info-grid">
          <div class="info-item">
            <label>Species:</label>
            <span>${pet.species?.name || 'Unknown'}</span>
          </div>
          <div class="info-item">
            <label>Breed:</label>
            <span>${pet.breed?.name || 'Mixed'}</span>
          </div>
          <div class="info-item">
            <label>Gender:</label>
            <span>${pet.gender ? pet.gender.charAt(0).toUpperCase() + pet.gender.slice(1) : 'Unknown'}</span>
          </div>
          <div class="info-item">
            <label>Age:</label>
            <span>${pet.birth_date ? calculateAge(pet.birth_date) + ' years old' : 'Unknown'}</span>
          </div>
          <div class="info-item">
            <label>Weight:</label>
            <span>${pet.weight ? pet.weight + ' kg' : 'Not recorded'}</span>
          </div>
          <div class="info-item">
            <label>Color:</label>
            <span>${pet.color || 'Not specified'}</span>
          </div>
        </div>
        
        ${pet.medical_notes ? `
          <div class="medical-notes">
            <label>Medical Notes:</label>
            <p>${pet.medical_notes}</p>
          </div>
        ` : ''}
      </div>
    `;
    
    document.getElementById('petDetailsContent').innerHTML = content;
    openModal('petDetailsModal');
  } catch (error) {
    console.error('Error loading pet details:', error);
    alert('Failed to load pet details');
  }
}

function calculateAge(birthDate) {
  const today = new Date();
  const birth = new Date(birthDate);
  let age = today.getFullYear() - birth.getFullYear();
  const monthDiff = today.getMonth() - birth.getMonth();
  
  if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
    age--;
  }
  
  return age;
}

// Form submission
document.addEventListener('DOMContentLoaded', function() {
  // Handle sidebar form submission
  const petForm = document.getElementById('petForm');
  if (petForm) {
    petForm.addEventListener('submit', function(e) {
      console.log('Sidebar form submitting...');
      
      // Check if required fields are filled
      const formData = new FormData(petForm);
      const name = formData.get('name');
      const ownerId = formData.get('owner_id');
      
      if (!name || !ownerId) {
        e.preventDefault();
        alert('Please fill in all required fields (Name and Owner)');
        return;
      }
      
      // Let the form submit normally
    });
  }
});

// Load breeds by species
async function loadBreedsBySpecies(speciesId) {
  try {
    const response = await fetch(`/api/breeds-by-species/${speciesId}`);
    const breeds = await response.json();
    
    // Update sidebar breed select
    const breedSelects = ['petBreedSidebar'];
    breedSelects.forEach(selectId => {
      const breedSelect = document.getElementById(selectId);
      if (breedSelect) {
        breedSelect.innerHTML = '<option value="">Select Breed</option>';
        
        breeds.forEach(breed => {
          const option = document.createElement('option');
          option.value = breed.id;
          option.textContent = breed.name;
          breedSelect.appendChild(option);
        });
      }
    });
  } catch (error) {
    console.error('Error loading breeds:', error);
  }
}

// Reset form when modal closes
document.addEventListener('DOMContentLoaded', function() {
  const modal = document.getElementById('petModal');
  if (modal) {
    const observer = new MutationObserver(function(mutations) {
      mutations.forEach(function(mutation) {
        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
          if (modal.classList.contains('hidden')) {
            // Reset form when modal is hidden
            document.getElementById('petEditForm').reset();
            document.getElementById('petEditId').value = '';
            document.getElementById('savePetButton').textContent = 'Save Pet';
            const titleElement = document.querySelector('#petModal .modal-title');
            if (titleElement) titleElement.textContent = 'Add New Pet';
          }
        }
      });
    });
    observer.observe(modal, { attributes: true });
  }
});
</script>

<style>
.pet-details-view {
  padding: var(--space-4);
}

.pet-header {
  display: flex;
  gap: var(--space-4);
  margin-bottom: var(--space-6);
  align-items: center;
}

.pet-image-large {
  width: 120px;
  height: 120px;
  border-radius: var(--radius-lg);
  overflow: hidden;
  border: 2px solid var(--gray-200);
}

.pet-image-large img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.pet-basic-info h3 {
  font-size: var(--font-size-2xl);
  font-weight: 700;
  color: var(--gray-900);
  margin-bottom: var(--space-2);
}

.pet-owner, .pet-id {
  color: var(--gray-600);
  margin-bottom: var(--space-1);
}

.pet-info-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: var(--space-4);
  margin-bottom: var(--space-6);
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: var(--space-1);
}

.info-item label {
  font-weight: 600;
  color: var(--gray-700);
  font-size: var(--font-size-sm);
}

.info-item span {
  color: var(--gray-900);
}

.medical-notes {
  background: var(--gray-50);
  padding: var(--space-4);
  border-radius: var(--radius-lg);
}

.medical-notes label {
  font-weight: 600;
  color: var(--gray-700);
  display: block;
  margin-bottom: var(--space-2);
}

.medical-notes p {
  color: var(--gray-900);
  margin: 0;
  line-height: 1.6;
}

@media (max-width: 768px) {
  .pet-header {
    flex-direction: column;
    text-align: center;
  }
  
  .pet-info-grid {
    grid-template-columns: 1fr;
  }
}
</style>

<!-- Filter Modal -->
<x-ui.modal id="filterModal" title="Filter Pets" size="sm">
  <div class="filter-form">
    <div class="form-group">
      <label class="form-label">Species</label>
      <select id="speciesFilter" class="form-select" onchange="filterTable()">
        <option value="">All Species</option>
        <option value="dog">Dog</option>
        <option value="cat">Cat</option>
        <option value="bird">Bird</option>
        <option value="rabbit">Rabbit</option>
      </select>
    </div>
    
    <div class="form-group">
      <label class="form-label">Gender</label>
      <select id="genderFilter" class="form-select" onchange="filterTable()">
        <option value="">All Genders</option>
        <option value="male">Male</option>
        <option value="female">Female</option>
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

@endsection
