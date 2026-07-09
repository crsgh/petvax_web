@extends('layouts.user_type.auth')

@section('content')

  <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
              <h6>Breeds Table</h6>
              <button class="btn btn-primary btn-sm mb-0" onclick="openAddSidebar()">
                <i class="fas fa-plus"></i>&nbsp;&nbsp;Add New Breed
              </button>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0" id="breedsTable">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Breed Name</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Species</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($breeds as $breed)
                    <tr id="breed-row-{{ $breed->id }}">
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">{{ $breed->name }}</h6>
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">{{ $breed->species->name }}</h6>
                          </div>
                        </div>
                      </td>
                      <td class="align-middle text-center">
                        <div class="d-flex gap-1 justify-content-center">
                          <button class="btn btn-icon-only btn-rounded btn-outline-primary mb-0 p-2 d-flex align-items-center justify-content-center" 
                                  onclick="openEditSidebar({{ json_encode($breed) }})"
                                  data-bs-toggle="tooltip" 
                                  data-bs-placement="top"
                                  title="Edit Breed">
                            <i class="fas fa-edit"></i>
                          </button>
                          
                          <button class="btn btn-icon-only btn-rounded btn-outline-danger mb-0 p-2 d-flex align-items-center justify-content-center"
                                  onclick="deleteBreed('{{ $breed->id }}')"
                                  data-bs-toggle="tooltip"
                                  data-bs-placement="top" 
                                  title="Delete Breed">
                            <i class="fas fa-trash"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
                <div class="d-flex justify-content-end mt-1 mr-10 mb-2">
                    {{ $breeds->links() }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Add Breed Sidebar -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="addBreedSidebar">
    <div class="offcanvas-header border-bottom">
      <h5 class="offcanvas-title">Add New Breed</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close">
        <span aria-hidden="true" class="text-3xl">&times;</span>
      </button>
    </div>
    <div class="offcanvas-body">
      <form id="addBreedForm" action="" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="mb-3">
          <label for="breedName" class="form-label">Name</label>
          <input type="text" class="form-control @error('name') is-invalid @enderror" 
                 id="breedName" 
                 name="name" 
                 required
                 minlength="2"
                 maxlength="50"
                 pattern="^[A-Za-z\s]+$"

                 value="{{ old('name') }}">
          <div class="invalid-feedback">
            @error('name')
              {{ $message }}
            @else
              Please enter a valid breed name (2-50 characters, letters and spaces only, no numbers allowed)
            @enderror
          </div>
        </div>
        <div class="mb-3">
          <label for="speciesId" class="form-label">Species</label>
          <select class="form-control @error('species_id') is-invalid @enderror" 
                  id="speciesId" 
                  name="species_id" 
                  required>
            <option value="">Select Species</option>
            @foreach($species as $specie)
              <option value="{{ $specie->id }}" {{ old('species_id') == $specie->id ? 'selected' : '' }}>
                {{ $specie->name }}
              </option>
            @endforeach
          </select>
          <div class="invalid-feedback">
            @error('species_id')
              {{ $message }}
            @else
              Please select a species
            @enderror
          </div>
        </div>
        @if(auth()->user()->role_id == 1)
        <div class="mb-3">
          <label for="clinicId" class="form-label">Clinic</label>
          <select class="form-control @error('clinic_id') is-invalid @enderror" 
                  id="clinicId" 
                  name="clinic_id" 
                  required>
            <option value="">Select Clinic</option>
            @foreach($clinics as $clinic)
              <option value="{{ $clinic->id }}" {{ old('clinic_id') == $clinic->id ? 'selected' : '' }}>
                {{ $clinic->name }}
              </option>
            @endforeach
          </select>
          <div class="invalid-feedback">
            @error('clinic_id')
              {{ $message }}
            @else
              Please select a clinic
            @enderror
          </div>
        </div>
        @else
          <input type="hidden" name="clinic_id" value="{{ auth()->user()->clinic_id }}">
        @endif
        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary">Save Breed</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Form validation script
    (function () {
      'use strict'
      var forms = document.querySelectorAll('.needs-validation')
      Array.prototype.slice.call(forms)
        .forEach(function (form) {
          form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
              event.preventDefault()
              event.stopPropagation()
            }
            form.classList.add('was-validated')
          }, false)
        })
    })()
  </script>
  <!-- Edit Breed Sidebar -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="editBreedSidebar">
    <div class="offcanvas-header border-bottom">
      <h5 class="offcanvas-title">Edit Breed</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <form id="editBreedForm" method="POST">
        @csrf
        <div class="mb-3">
          <label for="editBreedName" class="form-label">Name</label>
          <input type="text" class="form-control" id="editBreedName" name="name" required>
        </div>
        <div class="mb-3">
          <label for="editSpeciesId" class="form-label">Species</label>
          <select class="form-control" id="editSpeciesId" name="species_id" required>
            <option value="">Select Species</option>
            @foreach($species as $specie)
              <option value="{{ $specie->id }}">{{ $specie->name }}</option>
            @endforeach
          </select>
        </div>
        @if(auth()->user()->role_id == 1)
        <div class="mb-3">
          <label for="editClinicId" class="form-label">Clinic</label>
          <select class="form-control" id="editClinicId" name="clinic_id" required>
            <option value="">Select Clinic</option>
            @foreach($clinics as $clinic)
              <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
            @endforeach
          </select>
        </div>
        @else
          <input type="hidden" name="clinic_id" value="{{ auth()->user()->clinic_id }}">
        @endif
        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary">Update Breed</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openAddSidebar() {
      var sidebar = new bootstrap.Offcanvas(document.getElementById('addBreedSidebar'));
      sidebar.show();
    }

    function openEditSidebar(breed) {
      const form = document.getElementById('editBreedForm');
      form.action = `/breeds/${breed.id}`;
      document.getElementById('editBreedName').value = breed.name;
      document.getElementById('editSpeciesId').value = breed.species_id;
      if (document.getElementById('editClinicId')) {
        document.getElementById('editClinicId').value = breed.clinic_id;
      }

      var sidebar = new bootstrap.Offcanvas(document.getElementById('editBreedSidebar'));
      sidebar.show();
    }

    function deleteBreed(breedId) {
      if (confirm('Are you sure you want to delete this breed?')) {
        fetch(`/breeds/${breedId}/delete`, {
          method: 'GET',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          }
        })
        .then(response => {
          if (response.ok) {
            document.getElementById(`breed-row-${breedId}`).remove();
          } else {
            alert('Error deleting breed');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error deleting breed');
        });
      }
    }
  </script>
  
@endsection
