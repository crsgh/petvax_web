@extends('layouts.user_type.auth')

@section('content')

  <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
              <h6>Species Table</h6>
              <button class="btn btn-primary btn-sm mb-0" onclick="openAddSidebar()">
                <i class="fas fa-plus"></i>&nbsp;&nbsp;Add New Species
              </button>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Species Name</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($species as $specie)
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">{{ $specie->name }}</h6>
                          </div>
                        </div>
                      </td>
                      <td class="align-middle text-center">
                        <div class="d-flex gap-1 justify-content-center">
                          <button class="btn btn-icon-only btn-rounded btn-outline-primary mb-0 p-2 d-flex align-items-center justify-content-center" 
                                  onclick="openEditSidebar({{ json_encode($specie) }})"
                                  data-bs-toggle="tooltip" 
                                  data-bs-placement="top"
                                  title="Edit Species">
                            <i class="fas fa-edit"></i>
                          </button>
                          
                          <form action="/species/{{ $specie->id }}/delete" method="GET" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this species?')">
                            @csrf
                          
                            <button type="submit" class="btn btn-icon-only btn-rounded btn-outline-danger mb-0 p-2 d-flex align-items-center justify-content-center"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top" 
                                    title="Delete Species">
                              <i class="fas fa-trash"></i>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Add Species Sidebar -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="addSpeciesSidebar">
    <div class="offcanvas-header border-bottom">
      <h5 class="offcanvas-title">Add New Species</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <form id="addSpeciesForm" action="" method="POST">
        @csrf
        <div class="mb-3">
          <label for="speciesName" class="form-label">Name</label>
          <input type="text" class="form-control" id="speciesName" name="name" required>
        </div>
        @if(auth()->user()->role_id == 1)
        <div class="mb-3">
          <label for="clinic" class="form-label">Clinic</label>
          <select class="form-control" id="clinic" name="clinic_id" required>
            @foreach($clinics as $clinic)
              <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
            @endforeach
          </select>
        </div>
        @else
        <input type="hidden" name="clinic_id" value="{{ auth()->user()->clinic_id }}">
        @endif
        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary">Save Species</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Species Sidebar -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="editSpeciesSidebar">
    <div class="offcanvas-header border-bottom">
      <h5 class="offcanvas-title">Edit Species</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <form id="editSpeciesForm" action="" method="POST">
        @csrf

        <div class="mb-3">
          <label for="editSpeciesName" class="form-label">Name</label>
          <input type="text" class="form-control" id="editSpeciesName" name="name" required>
        </div>
        @if(auth()->user()->role_id == 1)
        <div class="mb-3">
          <label for="editClinic" class="form-label">Clinic</label>
          <select class="form-control" id="editClinic" name="clinic_id" required>
            @foreach($clinics as $clinic)
              <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
            @endforeach
          </select>
        </div>
        @else
        <input type="hidden" name="clinic_id" value="{{ auth()->user()->clinic_id }}">
        @endif
        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary">Update Species</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openAddSidebar() {
      var form = document.getElementById('addSpeciesForm');
      form.action = "";
      var sidebar = new bootstrap.Offcanvas(document.getElementById('addSpeciesSidebar'));
      sidebar.show();
    }

    function openEditSidebar(species) {
      const form = document.getElementById('editSpeciesForm');
      form.action = `species/${species.id}`;
      document.getElementById('editSpeciesName').value = species.name;
      
      @if(auth()->user()->role_id == 1)
      document.getElementById('editClinic').value = species.clinic_id;
      @endif

      var sidebar = new bootstrap.Offcanvas(document.getElementById('editSpeciesSidebar'));
      sidebar.show();
    }
  </script>
  
@endsection
