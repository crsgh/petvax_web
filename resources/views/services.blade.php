@extends('layouts.user_type.auth')

@section('content')

<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h6>Services Table</h6>
                <button class="btn btn-primary btn-sm mb-0" onclick="openAddServiceSidebar()">
                  <i class="fas fa-plus"></i>&nbsp;&nbsp;Add New Service
                </button>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search services..." onkeyup="filterServices()">
                  </div>
                </div>
                <div class="col-md-4">
                  <select class="form-select" id="categoryFilter" onchange="filterServices()">
                    <option value="">All Categories</option>
                    <option value="vaccination">Vaccination</option>
                    <option value="grooming">Grooming</option>
                    <option value="deworming">Deworming</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Service Info</th>
                      @if(Auth::user()->role_id == 1)
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Clinic</th>
                      @endif
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Description</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Price</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Home Service</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="servicesTableBody">
                    @foreach($services as $service)
                    <tr class="service-row" data-category="{{ $service->category }}" data-name="{{ strtolower($service->name) }}">
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div>
                            <img src="{{ $service->image == null ? '../assets/img/service-placeholder.jpg' : asset('storage/' . $service->image) }}" class="avatar avatar-sm me-3" alt="{{ $service->name }}">
                          </div>
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">{{ $service->name }}</h6>
                            <p class="text-xs text-secondary mb-0">{{ ucfirst($service->category) }}</p>
                          </div>
                        </div>
                      </td>
                      @if(Auth::user()->role_id == 1)
                      <td>
                        <p class="text-xs text-secondary mb-0">{{ $service->clinic->name }}</p>
                      </td>
                      @endif
                      <td>
                        <p class="text-xs text-secondary mb-0">{{ $service->description }}</p>
                      </td>
    
                      <td class="align-middle text-center text-sm">
                        <span class="text-secondary text-xs font-weight-bold">₱{{ number_format($service->price, 2) }}</span>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="badge badge-sm {{ $service->home_service ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                          {{ $service->home_service ? 'Available' : 'Not Available' }}
                        </span>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="badge badge-sm {{ $service->status === 'active' ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                          {{ ucfirst($service->status) }}
                        </span>
                      </td>
                      <td class="align-middle text-center">
                        <div class="d-flex gap-1 justify-content-center">
                          <button class="btn btn-icon-only btn-rounded btn-outline-primary mb-0 p-2 d-flex align-items-center justify-content-center" 
                                  onclick="openEditServiceSidebar({{ json_encode($service) }})"
                                  data-bs-toggle="tooltip" 
                                  data-bs-placement="top"
                                  title="Edit Service">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                              <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                            </svg>
                          </button>
                          
                          <form action="/services/{{ $service->id }}/delete" method="GET">
                          @csrf
                          <button type="submit" class="btn btn-icon-only btn-rounded btn-outline-danger mb-0 p-2 d-flex align-items-center justify-content-center"
                                  data-bs-toggle="tooltip"
                                  data-bs-placement="top" 
                                  title="Delete Service">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                              <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                              <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                            </svg>
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

  <!-- Add/Edit Service Sidebar -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="serviceSidebar">
    <div class="offcanvas-header">
      <h5 id="sidebarTitle">Add New Service</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <form id="serviceForm" method="POST" action="" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="serviceId" name="id">
        @if(Auth::user()->role_id == 1)
        <div class="mb-3">
          <label class="form-label">Clinic</label>
          <select class="form-select" id="serviceClinic" name="clinic_id" required>
            @foreach($clinics as $clinic)
              <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
            @endforeach
          </select>
        </div>
        @else
        <input type="hidden" name="clinic_id" value="{{ Auth::user()->clinic_id }}">
        @endif
        <div class="mb-3">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="homeServiceSwitch" name="home_service">
            <label class="form-check-label" for="homeServiceSwitch">Available for Home Service</label>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Service Name</label>
          <input type="text" class="form-control" id="serviceName" name="name" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Category</label>
          <select class="form-select" id="serviceCategory" name="category" required>
            <option value="vaccination">Vaccination</option>
            <option value="grooming">Grooming</option>
            <option value="deworming">Deworming</option>
          </select>
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Species</label>
            <select class="form-select" id="serviceSpecies" name="species" required>
              @foreach($species as $specie)
                <option value="{{ $specie->id }}">{{ $specie->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Pet Size</label>
            <select class="form-select" id="servicePetSize" name="pet_size" required>
              <option value="small">Small</option>
              <option value="medium">Medium</option>
              <option value="large">Large</option>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Price</label>
            <input type="number" step="0.01" class="form-control" id="servicePrice" name="price" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" id="serviceStatus" name="status" required>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Image</label>
          <input type="file" class="form-control" id="serviceImage" name="image" accept="image/*">
        </div>
        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea class="form-control" id="serviceDescription" name="description" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary w-100" id="submitBtn">Add Service</button>
      </form>
    </div>
  </div>

  <script>
    function filterServices() {
      const searchTerm = document.getElementById('searchInput').value.toLowerCase();
      const categoryFilter = document.getElementById('categoryFilter').value.toLowerCase();
      const rows = document.getElementsByClassName('service-row');

      Array.from(rows).forEach(row => {
        const name = row.getAttribute('data-name');
        const category = row.getAttribute('data-category');
        const matchesSearch = name.includes(searchTerm);
        const matchesCategory = !categoryFilter || category === categoryFilter;
        
        row.style.display = matchesSearch && matchesCategory ? '' : 'none';
      });
    }

    function openAddServiceSidebar() {
      document.getElementById('sidebarTitle').textContent = 'Add New Service';
      document.getElementById('serviceForm').reset();
      document.getElementById('serviceForm').action = '';
      document.getElementById('submitBtn').textContent = 'Add Service';
      new bootstrap.Offcanvas(document.getElementById('serviceSidebar')).show();
    }

    function openEditServiceSidebar(service) {
      document.getElementById('sidebarTitle').textContent = 'Edit Service';
      document.getElementById('serviceId').value = service.id;
      document.getElementById('serviceName').value = service.name;
      document.getElementById('serviceCategory').value = service.category;
      document.getElementById('serviceDescription').value = service.description;
      document.getElementById('servicePrice').value = service.price;
      document.getElementById('serviceStatus').value = service.status;
      document.getElementById('serviceForm').action = `/services/${service.id}`;
      document.getElementById('submitBtn').textContent = 'Update Service';
      new bootstrap.Offcanvas(document.getElementById('serviceSidebar')).show();
    }
  </script>
  
@endsection
