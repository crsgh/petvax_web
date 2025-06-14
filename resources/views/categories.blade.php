@extends('layouts.user_type.auth')

@section('content')

  <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
              <h6>Categories Table</h6>
              <button class="btn btn-primary btn-sm mb-0" onclick="openSidebar()">
                <i class="fas fa-plus"></i>&nbsp;&nbsp;Add New Category
              </button>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Category Name</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Description</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
              
                    @foreach($categories as $category)
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">{{ $category->name }}</h6>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-xs text-secondary mb-0">{{ Str::limit($category->description, 100) }}</p>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="badge badge-sm {{ $category->status === 'active' ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                          {{ ucfirst($category->status) }}
                        </span>
                      </td>
                      <td class="align-middle text-center">
                        <div class="d-flex gap-1 justify-content-center">
                          <button onclick="editCategory({{ $category }})" 
                                  class="btn btn-icon-only btn-rounded btn-outline-primary mb-0 p-2 d-flex align-items-center justify-content-center"
                                  data-bs-toggle="tooltip"
                                  data-bs-placement="top"
                                  title="Edit Category">
                            <i class="fas fa-pencil-alt"></i>
                          </button>
                          
                          <form action="categories/{{ $category->id }}/delete" method="GET" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-icon-only btn-rounded btn-outline-danger mb-0 p-2 d-flex align-items-center justify-content-center"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Delete Category">
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

  <!-- Add/Edit Category Sidebar -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="categorySidebar">
    <div class="offcanvas-header border-bottom">
      <h5 class="offcanvas-title" id="sidebarTitle">Add New Category</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <form id="categoryForm" action="" method="POST">
        @csrf
        <input type="hidden" id="categoryId" name="category_id">
        @if(auth()->user()->role_id == 1)
        <div class="mb-3">
          <label for="clinicSelect" class="form-label">Select Clinic</label>
          <select class="form-select" id="clinicSelect" name="clinic_id" required>
            <option value="">Select a clinic</option>
            @foreach($clinics as $clinic)
              <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
            @endforeach
          </select>
        </div>
        @else
          <input type="hidden" name="clinic_id" value="{{ auth()->user()->clinic_id }}">
        @endif
        <div class="mb-3">
          <label for="categoryName" class="form-label">Category Name</label>
          <input type="text" class="form-control" id="categoryName" name="name" required>
        </div>
        <div class="mb-3">
          <label for="categoryDescription" class="form-label">Description</label>
          <textarea class="form-control" id="categoryDescription" name="description" rows="4" required></textarea>
        </div>
        <div class="mb-3">
          <label for="categoryStatus" class="form-label">Status</label>
          <select class="form-select" id="categoryStatus" name="status" required>
            <option value="active" selected>Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary" id="submitBtn">Save Category</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openSidebar() {
       document.getElementById('categoryForm').action = ""
      document.getElementById('sidebarTitle').textContent = 'Add New Category';
      document.getElementById('categoryId').value = '';
      document.getElementById('categoryForm').reset();
      var sidebar = new bootstrap.Offcanvas(document.getElementById('categorySidebar'));
      sidebar.show();
    }

    function editCategory(data) {
      document.getElementById('categoryForm').action = "/categories/" + data.id;
      
      document.getElementById('sidebarTitle').textContent = 'Edit Category';
      document.getElementById('categoryId').value = data.id;
      document.getElementById('categoryName').value = data.name;
      document.getElementById('categoryDescription').value = data.description;
      document.getElementById('categoryStatus').value = data.status;
      if (document.getElementById('clinicSelect')) {
        document.getElementById('clinicSelect').value = data.clinic_id;
      }
      
      var sidebar = new bootstrap.Offcanvas(document.getElementById('categorySidebar'));
      sidebar.show();
    }

    function handleSubmit(event) {
      event.preventDefault();
      
      const formData = new FormData(event.target);
      const id = document.getElementById('categoryId').value;
      const url = id ? `/categories/${id}` : '/categories';
      const method = 'POST';
      
      fetch(url, {
        method: method,
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          location.reload();
        }
      });
    }
  </script>
  
  @endsection
