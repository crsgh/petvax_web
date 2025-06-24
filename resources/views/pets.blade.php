@extends('layouts.user_type.auth')

@section('content')

  <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h6>Pets Table</h6>
                @if(auth()->user()->role_id != 4)
                <button class="btn btn-primary btn-sm" onclick="openAddSidebar()">
                  <i class="fas fa-plus"></i>&nbsp;&nbsp;Add New Pet
                </button>
                @endif
              </div>
              
              <!-- Search and Filter Section -->
              <div class="row g-3 mb-3">
                <div class="col-md-3">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-paw"></i></span>
                    <input type="text" class="form-control" id="petSearchInput" placeholder="Search pets..." onkeyup="filterTable()">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="ownerSearchInput" placeholder="Search owners..." onkeyup="filterTable()">
                  </div>
                </div>
                <div class="col-md-2">
                  <select class="form-select" id="speciesFilter" onchange="filterTable()">
                    <option value="">All Species</option>
                    <option value="feline">Feline</option>
                    <option value="canine">Canine</option>
                  </select>
                </div>
                <div class="col-md-2">
                  <select class="form-select" id="genderFilter" onchange="filterTable()">
                    <option value="">All Genders</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                  </select>
                </div>
                <div class="col-md-2">
                  <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                    Reset Filters
                  </button>
                </div>
              </div>
            </div>

            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0" id="petsTable">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Pet Info</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Species</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Birth Date</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Gender</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Weight</th>
                    
                      <!-- @if(auth()->user()->role_id != 4)
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                      @endif -->
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($pets as $pet)
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div>
                            <img src="{{ $pet->image != null ? asset('storage/' . $pet->image) : asset('assets/img/dog.png') }}" class="avatar avatar-sm me-3" alt="{{ $pet->name }}">
                          </div>
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">{{ $pet->name }}</h6>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-xs text-secondary mb-0">{{ $pet->species }}</p>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ \Carbon\Carbon::parse($pet->birth_date)->format('d/m/y') }}</span>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="badge badge-sm {{ $pet->gender === 'male' ? 'bg-gradient-info' : 'bg-gradient-primary' }}">
                          {{ ucfirst($pet->gender) }}
                        </span>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="text-secondary text-xs font-weight-bold">
                          {{ $pet->weight ? $pet->weight . ' kg' : 'N/A' }}
                        </span>
                      </td>
                      
                    
                    </tr>
                    @endforeach
                  </tbody>
                </table>
                <div class="d-flex justify-content-end mt-1 mr-10 mb-2">
                    {{ $pets->links() }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Add Pet Sidebar -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="addPetSidebar">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title">Add New Pet</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <form id="addPetForm" action="" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
          <label for="petImage" class="form-label">Pet Image</label>
          <input type="file" class="form-control" id="petImage" name="image" onchange="previewImage(this)">
          <div class="mt-2">
            <img id="imagePreview" src="{{ asset('assets/img/dog.png') }}" alt="Preview" style="max-width: 200px;">
          </div>
        </div>
        <div class="mb-3">
          <label for="petName" class="form-label">Pet Name</label>
          <input type="text" class="form-control" id="petName" name="name" required>
        </div>
        <div class="mb-3">
          <label for="species" class="form-label">Species</label>
          <select class="form-select" id="species" name="species" required>
            <option value="canine">Canine</option>
            <option value="feline">Feline</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="breed" class="form-label">Breed</label>
          <select class="form-select" id="breed" name="breed" required>
            @foreach($breeds as $breed)
              <option value="{{ $breed->id }}">{{ $breed->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label for="birthDate" class="form-label">Birth Date</label>
          <input type="date" class="form-control" id="birthDate" name="birth_date" max="<?php echo date('Y-m-d'); ?>" required>
        </div>
        <div class="mb-3">
          <label for="gender" class="form-label">Gender</label>
          <select class="form-select" id="gender" name="gender" required>
            <option value="male">Male</option>
            <option value="female">Female</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="weight" class="form-label">Weight (kg)</label>
          <input type="number" step="0.1" class="form-control" id="weight" name="weight">
        </div>
        <div class="mb-3">
          <label for="ownersName" class="form-label">Owner's Name</label>
          <select class="form-select" id="ownersName" name="owner_id" required>
            @foreach($owners as $owner)
              <option value="{{ $owner->id }}">{{ $owner->name }}</option>
            @endforeach
          </select>
        </div>
        @if(auth()->user()->role_id == 1)
        <div class="mb-3">
          <label for="clinic" class="form-label">Clinic</label>
          <select class="form-select" id="clinic" name="clinic_id" required>
            @foreach($clinics as $clinic)
              <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
            @endforeach
          </select>
        </div>
        @else
          <input type="hidden" name="clinic_id" value="{{ auth()->user()->clinic_id }}">
        @endif
        <button type="submit" class="btn btn-primary">Add Pet</button>
      </form>
    </div>
  </div>
  <!-- Edit Pet Sidebar -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="editPetSidebar">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title">Edit Pet</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <form id="editPetForm" method="POST" enctype="multipart/form-data">
        @csrf
       
        <div class="mb-3">
          <label for="editPetImage" class="form-label">Pet Image</label>
          <input type="file" class="form-control" id="editPetImage" name="image" onchange="previewEditImage(this)">
          <div class="mt-2">
            <img id="editImagePreview" src="" alt="Preview" style="max-width: 200px;">
          </div>
        </div>
        <div class="mb-3">
          <label for="editPetName" class="form-label">Pet Name</label>
          <input type="text" class="form-control" id="editPetName" name="name" required>
        </div>
        <div class="mb-3">
          <label for="editSpecies" class="form-label">Species</label>
          <select class="form-select" id="editSpecies" name="species" required>
            <option value="canine">Canine</option>
            <option value="feline">Feline</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="editBreed" class="form-label">Breed</label>
          <select class="form-select" id="editBreed" name="breed" required>
            @foreach($breeds as $breed)
              <option value="{{ $breed->id }}">{{ $breed->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label for="editBirthDate" class="form-label">Birth Date</label>
          <input type="date" class="form-control" id="editBirthDate" name="birth_date" required>
        </div>
        <div class="mb-3">
          <label for="editGender" class="form-label">Gender</label>
          <select class="form-select" id="editGender" name="gender" required>
            <option value="male">Male</option>
            <option value="female">Female</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="editWeight" class="form-label">Weight (kg)</label>
          <input type="number" step="0.1" class="form-control" id="editWeight" name="weight">
        </div>
        <div class="mb-3">
          <label for="editOwnersName" class="form-label">Owner's Name</label>
          <select class="form-select" id="editOwnersName" name="owner_id" required>
            @foreach($owners as $owner)
              <option value="{{ $owner->id }}">{{ $owner->name }}</option>
            @endforeach
          </select>
        </div>
        @if(auth()->user()->role_id == 1)
        <div class="mb-3">
          <label for="editClinic" class="form-label">Clinic</label>
          <select class="form-select" id="editClinic" name="clinic_id" required>
            @foreach($clinics as $clinic)
              <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
            @endforeach
          </select>
        </div>
        @else
          <input type="hidden" name="clinic_id" value="{{ auth()->user()->clinic_id }}">
        @endif
        <button type="submit" class="btn btn-primary">Update Pet</button>
      </form>
    </div>
  </div>

  <script>
    function previewImage(input) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('imagePreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
      }
    }

    function previewEditImage(input) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('editImagePreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
      }
    }

    function openAddSidebar() {
      const addSidebar = new bootstrap.Offcanvas(document.getElementById('addPetSidebar'));
      addSidebar.show();
    }

    function openEditSidebar(pet) {
      const editSidebar = new bootstrap.Offcanvas(document.getElementById('editPetSidebar'));
      document.getElementById('editPetForm').action = `/pets/${pet.id}`;
      document.getElementById('editImagePreview').src = pet.image ? `/storage/${pet.image}` : '/assets/img/dog.png';
      document.getElementById('editPetName').value = pet.name;
      document.getElementById('editSpecies').value = pet.species;
      document.getElementById('editBirthDate').value = pet.birth_date;
      document.getElementById('editGender').value = pet.gender;
      document.getElementById('editWeight').value = pet.weight;
      document.getElementById('editOwnersName').value = pet.owners_name;
      editSidebar.show();
    }

    function deletePet(petId) {
      if (confirm('Are you sure you want to delete this pet?')) {
        fetch(`/pets/${petId}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            location.reload();
          } else {
            alert('Error deleting pet');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error deleting pet');
        });
      }
    }

    function filterTable() {
      const petSearchText = document.getElementById('petSearchInput').value.toLowerCase();
      const ownerSearchText = document.getElementById('ownerSearchInput').value.toLowerCase();
      const speciesFilter = document.getElementById('speciesFilter').value.toLowerCase();
      const genderFilter = document.getElementById('genderFilter').value.toLowerCase();
      
      const table = document.getElementById('petsTable');
      const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

      for (let row of rows) {
        const petName = row.querySelector('h6').textContent.toLowerCase();
        const ownerName = row.getElementsByTagName('td')[5].textContent.trim().toLowerCase();
        const species = row.getElementsByTagName('td')[1].textContent.toLowerCase();
        const gender = row.getElementsByTagName('td')[3].textContent.toLowerCase();
        
        const matchesPetSearch = petName.includes(petSearchText);
        const matchesOwnerSearch = ownerName.includes(ownerSearchText);
        const matchesSpecies = !speciesFilter || species.includes(speciesFilter);
        const matchesGender = !genderFilter || gender.includes(genderFilter);

        row.style.display = (matchesPetSearch && matchesOwnerSearch && matchesSpecies && matchesGender) ? '' : 'none';
      }
    }

    function resetFilters() {
      document.getElementById('petSearchInput').value = '';
      document.getElementById('ownerSearchInput').value = '';
      document.getElementById('speciesFilter').value = '';
      document.getElementById('genderFilter').value = '';
      filterTable();
    }
  </script>
  
@endsection
