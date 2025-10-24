@extends('layouts.user_type.auth')

@section('content')
<div class="owners-page">
  <div class="page-header">
    <div class="header-content">
      <h1 class="page-title"></h1>
      <p class="page-subtitle">Manage pet owners and their information</p>
    </div>
    <div class="header-actions">
      <x-ui.button 
        variant="primary" 
        size="default" 
        icon="fas fa-plus"
        onclick="openModal('ownerModal')"
      >
        Add New Owner
      </x-ui.button>
    </div>
  </div>

  <!-- Search and Filters -->
  <div class="filters-section">
    <div class="search-box">
      <x-ui.input 
        type="text" 
        id="searchOwner" 
        placeholder="Search pet owners..."
        class="search-input"
      />
    </div>
  </div>

  <div class="owners-content">
    <div class="owners-grid">
      @foreach($clinics as $clinic)
        <div class="clinic-card">
          <div class="clinic-header">
            <div class="clinic-image">
              @if($clinic->image)
                <img src="{{ asset('storage/' . $clinic->image) }}" alt="{{ $clinic->name }}">
              @else
                <div class="clinic-placeholder">
                  <i class="fas fa-hospital"></i>
                </div>
              @endif
            </div>
            <div class="clinic-info">
              <h3 class="clinic-name">{{ $clinic->name }}</h3>
              <p class="clinic-address">{{ Str::limit($clinic->address, 60) }}</p>
            </div>
          </div>
          
          <div class="clinic-details">
            <div class="detail-item">
              <i class="fas fa-phone text-primary"></i>
              <span>{{ $clinic->contact }}</span>
            </div>
            <div class="detail-item">
              <i class="fas fa-envelope text-gray-500"></i>
              <span>{{ $clinic->email }}</span>
            </div>
            <div class="detail-item">
              <i class="fas fa-clock text-info"></i>
              <span>{{ date('H:i', strtotime($clinic->opening_time)) }} - {{ date('H:i', strtotime($clinic->closing_time)) }}</span>
            </div>
            <div class="detail-item">
              <i class="fas fa-calendar text-warning"></i>
              <span>
                @php
                  $days = json_decode($clinic->operation_days);
                  $shortDays = array_map(function($day) { return substr($day, 0, 3); }, $days);
                @endphp
                {{ implode(', ', $shortDays) }}
              </span>
            </div>
          </div>

          <div class="clinic-actions">
            <x-ui.button 
              variant="primary" 
              size="sm" 
              class="w-full"
              onclick="selectClinic({{ $clinic->id }})"
            >
              Select Clinic
            </x-ui.button>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>

<!-- Owner Modal -->
<x-ui.modal id="ownerModal" title="Add New Pet Owner" size="lg">
  <form id="ownerForm" method="POST" action="/pet-owners">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <x-ui.input 
        label="Full Name"
        name="name" 
        id="ownerName" 
        required 
        placeholder="Enter full name"
      />
      
      <x-ui.input 
        label="Email Address"
        type="email" 
        name="email" 
        id="ownerEmail" 
        required 
        placeholder="Enter email address"
      />
      
      <x-ui.input 
        label="Phone Number"
        type="tel" 
        name="phone" 
        id="ownerPhone" 
        required 
        placeholder="Enter phone number"
      />
      
      <x-ui.input 
        label="Address"
        name="address" 
        id="ownerAddress" 
        placeholder="Enter address"
      />
    </div>

    <div class="flex justify-end gap-3 mt-6">
      <x-ui.button 
        type="button" 
        variant="secondary" 
        onclick="closeModal('ownerModal')"
      >
        Cancel
      </x-ui.button>
      <x-ui.button 
        type="submit" 
        variant="primary"
      >
        Save Owner
      </x-ui.button>
    </div>
  </form>
</x-ui.modal>

<style>
/* Owners Page Layout */
.owners-page {
  padding: 2rem;
  background: #fafbfc;
  min-height: 100vh;
  font-family: 'Poppins', sans-serif;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 2rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.header-content {
  flex: 1;
}

.page-title {
  font-size: 2rem;
  font-weight: 700;
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
  flex-shrink: 0;
}

.filters-section {
  margin-bottom: 2rem;
}

.search-box {
  max-width: 400px;
}

.owners-content {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.owners-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1.5rem;
}

.clinic-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 1.5rem;
  transition: all 0.2s ease;
}

.clinic-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  border-color: #d1d5db;
}

.clinic-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.clinic-image {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  overflow: hidden;
  flex-shrink: 0;
}

.clinic-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.clinic-placeholder {
  width: 100%;
  height: 100%;
  background: #f3f4f6;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #9ca3af;
  font-size: 24px;
}

.clinic-info {
  flex: 1;
}

.clinic-name {
  font-size: 1.125rem;
  font-weight: 600;
  color: #111827;
  margin: 0 0 0.25rem 0;
  font-family: 'Poppins', sans-serif;
}

.clinic-address {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
  line-height: 1.4;
}

.clinic-details {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
}

.detail-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 0.875rem;
  color: #374151;
}

.detail-item i {
  width: 16px;
  flex-shrink: 0;
}

.clinic-actions .w-full {
  width: 100%;
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
  .owners-page {
    padding: 1rem;
  }
  
  .page-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .owners-grid {
    grid-template-columns: 1fr;
  }
  
  .md\:grid-cols-2 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
  }
  
  .owners-content {
    padding: 1rem;
  }
}
</style>

<script>
function selectClinic(clinicId) {
  // Handle clinic selection logic
  console.log('Selected clinic:', clinicId);
  // You can add booking logic here
}

// Search functionality
document.getElementById('searchOwner').addEventListener('keyup', function() {
  const searchTerm = this.value.toLowerCase();
  const clinicCards = document.querySelectorAll('.clinic-card');
  
  clinicCards.forEach(card => {
    const clinicName = card.querySelector('.clinic-name').textContent.toLowerCase();
    const clinicAddress = card.querySelector('.clinic-address').textContent.toLowerCase();
    
    if (clinicName.includes(searchTerm) || clinicAddress.includes(searchTerm)) {
      card.style.display = 'block';
    } else {
      card.style.display = 'none';
    }
  });
});

// Form submission
document.getElementById('ownerForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  
  try {
    const response = await fetch('/pet-owners', {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    });

    if (response.ok) {
      closeModal('ownerModal');
      window.location.reload();
    } else {
      const data = await response.json();
      alert(data.message || 'Failed to save owner');
    }
  } catch (error) {
    console.error('Error saving owner:', error);
    alert('Failed to save owner');
  }
});
</script>
@endsection
