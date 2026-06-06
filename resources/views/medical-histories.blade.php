@extends('layouts.user_type.auth')

@section('content')
<div class="medical-histories-page">
  <div class="page-header">
    <h1 class="page-title"></h1>
    <div class="header-actions">
      <div class="search-wrapper">
        <x-ui.icon name="search" class="search-icon w-4 h-4" />
        <input 
          type="text" 
          id="medicalSearchInput"
          class="search-input"
          placeholder="Search medical records..." 
          onkeyup="filterMedicalHistories()"
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
    </div>
  </div>

  <div class="medical-content" style="background: white; border-radius: 16px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #f1f5f9; overflow: hidden;">

    <div class="medical-table-wrapper">
      <table class="medical-table" id="medicalTable">
        <thead>
          <tr>
            <th>Pet & Owner</th>
            <th>Species & Breed</th>
            <th>Records Count</th>
            <th>Last Visit</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @if(isset($medicalHistories))
            @php
              $petHistories = [];
              foreach($medicalHistories as $history) {
                  if ($history->pet) {
                      $petId = $history->pet->id;
                      $petName = $history->pet->name;
                      $ownerName = $history->pet->owner ? $history->pet->owner->name : 'Unknown Owner';
                      $petOwnerKey = $petName . '-' . $ownerName;
                      
                      if (!isset($petHistories[$petOwnerKey])) {
                          $petHistories[$petOwnerKey] = [
                              'pet' => $history->pet,
                              'count' => 0,
                              'lastVisit' => $history->created_at
                          ];
                      }
                      
                      $petHistories[$petOwnerKey]['count']++;
                      
                      if ($history->created_at > $petHistories[$petOwnerKey]['lastVisit']) {
                          $petHistories[$petOwnerKey]['lastVisit'] = $history->created_at;
                      }
                  }
              }
            @endphp
            
            @forelse($petHistories as $key => $data)
            <tr data-pet="{{ strtolower($data['pet']->name) }}" data-owner="{{ strtolower($data['pet']->owner->name ?? '') }}">
              <td>
                <div class="pet-owner-info">
                  <div class="pet-name">{{ $data['pet']->name }}</div>
                  <div class="owner-name">{{ $data['pet']->owner->name ?? 'Unknown Owner' }}</div>
                </div>
              </td>
              <td>
                <div class="species-breed">
                  <div class="species">{{ $data['pet']->species ?? 'N/A' }}</div>
                  <div class="breed">{{ $data['pet']->breed ?? 'N/A' }}</div>
                </div>
              </td>
              <td>
                <x-ui.badge variant="info">
                  {{ $data['count'] }} {{ $data['count'] == 1 ? 'Record' : 'Records' }}
                </x-ui.badge>
              </td>
              <td>
                <div class="last-visit">
                  {{ \Carbon\Carbon::parse($data['lastVisit'])->format('M d, Y') }}
                </div>
              </td>
              <td>
                <div class="actions-group">
                  <x-ui.button 
                    variant="primary" 
                    size="xs" 
                    icon-name="eye"
                    onclick="viewMedicalHistory({{ $data['pet']->id }})"
                    title="View Medical History"
                  >View</x-ui.button>
                  <x-ui.button 
                    variant="secondary" 
                    size="xs" 
                    icon-name="download"
                    onclick="downloadMedicalHistory({{ $data['pet']->id }})"
                    title="Download Records"
                  >Download</x-ui.button>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="empty-state">
                <div class="empty-state-content">
                  <div class="empty-state-icon">
                    <x-ui.icon name="file" class="w-16 h-16 text-gray-300" />
                  </div>
                  <h3 class="empty-state-title">No Medical Records Found</h3>
                  <p class="empty-state-description">There are no medical histories recorded yet. Medical records will appear here once pets have visits.</p>
                </div>
              </td>
            </tr>
            @endforelse
          @else
          <tr>
            <td colspan="5" class="empty-state">
              <div class="empty-state-content">
                <div class="empty-state-icon">
                  <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14,2 14,8 20,8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10,9 9,9 8,9"/>
                  </svg>
                </div>
                <h3 class="empty-state-title">No Medical Records Found</h3>
                <p class="empty-state-description">There are no medical histories recorded yet. Medical records will appear here once pets have visits.</p>
              </div>
            </td>
          </tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>

<style>
/* Medical Histories Page Layout */
.medical-histories-page {
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

.header-actions .search-wrapper {
  position: relative;
  width: 300px;
  padding: 1px;
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

.medical-table-wrapper {
  overflow-x: auto;
}

.medical-table {
  width: 100%;
  border-collapse: collapse;
  font-family: 'Poppins', sans-serif;
}

.medical-table thead {
  background: #f8fafc;
  border-bottom: 1px solid #e5e7eb;
}

.medical-table th {
  padding: 1rem 1.5rem;
  text-align: left;
  font-weight: 600;
  font-size: 0.875rem;
  color: #374151;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-family: 'Poppins', sans-serif;
}

.medical-table td {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}

.medical-table tbody tr:hover {
  background: #f8fafc;
}

.pet-owner-info {
  display: flex;
  flex-direction: column;
}

.pet-name {
  font-weight: 600;
  color: #111827;
  font-size: 0.875rem;
  margin-bottom: 0.25rem;
}

.owner-name {
  font-size: 0.75rem;
  color: #6b7280;
}

.species-breed {
  display: flex;
  flex-direction: column;
}

.species {
  font-weight: 500;
  color: #111827;
  font-size: 0.875rem;
  margin-bottom: 0.25rem;
}

.breed {
  font-size: 0.75rem;
  color: #6b7280;
}

.last-visit {
  font-size: 0.875rem;
  color: #374151;
}

.actions-group {
  display: flex;
  gap: 0.5rem;
}

@media (max-width: 768px) {
  .medical-histories-page {
    padding: 1rem;
  }
  
  .actions-group {
    flex-direction: column;
    gap: 0.25rem;
  }
}
</style>

<script>
function filterMedicalHistories() {
  const searchTerm = document.getElementById('searchInput').value.toLowerCase();
  const rows = document.querySelectorAll('#medicalTable tbody tr');

  rows.forEach(row => {
    const petName = row.dataset.pet;
    const ownerName = row.dataset.owner;

    const matchesSearch = petName.includes(searchTerm) || ownerName.includes(searchTerm);

    row.style.display = matchesSearch ? '' : 'none';
  });
}

function viewMedicalHistory(petId) {
  window.location.href = `/pets/${petId}/medical-history`;
}

function downloadMedicalHistory(petId) {
  window.location.href = `/pets/${petId}/medical-history/download`;
}

function clearFilters() {
  document.getElementById('speciesFilter').value = '';
  filterMedicalHistories();
}
</script>

<!-- Filter Modal -->
<x-ui.modal id="filterModal" title="Filter Medical Records" size="sm">
  <div class="filter-form">
    <div class="form-group">
      <label class="form-label">Species</label>
      <select id="speciesFilter" class="form-select" onchange="filterMedicalHistories()">
        <option value="">All Species</option>
        <option value="dog">Dog</option>
        <option value="cat">Cat</option>
        <option value="bird">Bird</option>
        <option value="rabbit">Rabbit</option>
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
</style>

@endsection
