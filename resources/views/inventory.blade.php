@extends('layouts.user_type.auth')

@section('content')

  <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h6>Inventory Items</h6>
                <button class="btn btn-primary btn-sm mb-0" onclick="openAddSidebar()">
                  <i class="fas fa-plus"></i>&nbsp;&nbsp;Add Item
                </button>
              </div>
              
              <!-- Search and Filter Section -->
              <div class="row g-3 mb-3">
                <div class="col-md-4">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search items..." onkeyup="filterTable()">
                  </div>
                </div>
                <div class="col-md-3">
                  <select class="form-select" id="categoryFilter" onchange="filterTable()">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                      <option value="{{ $category->name }}">{{ $category->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3">
                  <select class="form-select" id="stockFilter" onchange="filterTable()">
                    <option value="">All Stock Levels</option>
                    <option value="low">Low Stock (< 10)</option>
                    <option value="out">Out of Stock</option>
                    <option value="available">In Stock</option>
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
                <table class="table align-items-center mb-0" id="inventoryTable">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Item Info</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Category</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Quantity</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Expiry Date</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($inventoryItems as $item)
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">{{ $item->name }}</h6>
                            <p class="text-xs text-secondary mb-0">{{ $item->sku }}</p>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-xs text-secondary mb-0">{{ $item->category->name ?? '' }}</p>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ $item->quantity }}</span>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="badge badge-sm {{ $item->expiry_date ? 'bg-gradient-warning' : 'bg-gradient-secondary' }}">
                          {{ $item->expiry_date ?? 'No Expiry' }}
                        </span>
                      </td>
                      <td class="align-middle text-center">
                        <div class="d-flex gap-1 justify-content-center">
                          <button class="btn btn-icon-only btn-rounded btn-outline-primary mb-0 p-2 d-flex align-items-center justify-content-center" 
                                  onclick="openEditSidebar('{{ $item->id }}', '{{ $item->name }}', '{{ $item->category_id }}', {{ $item->quantity }}, '{{ $item->description }}', '{{ $item->clinic_id }}')"
                                  data-bs-toggle="tooltip" 
                                  data-bs-placement="top"
                                  title="Edit Item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                              <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                            </svg>
                          </button>
                          
                          <form action="/inventory/{{ $item->id }}/delete" method="GET" style="display: inline;">
                            @csrf
                            <button class="btn btn-icon-only btn-rounded btn-outline-danger mb-0 p-2 d-flex align-items-center justify-content-center"
                                  data-bs-toggle="tooltip"
                                  data-bs-placement="top" 
                                  title="Delete Item">
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

  <!-- Add Inventory Item Sidebar -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="addClinicSidebar">
    <div class="offcanvas-header border-bottom">
      <h5 class="offcanvas-title" id="sidebarTitle">Add Inventory Item</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close">
        <span aria-hidden="true" class="text-3xl">&times;</span>
      </button>
    </div>
    <div class="offcanvas-body">
      <form id="inventoryItemForm" method="POST" action="" class="needs-validation" novalidate onsubmit="return validateInventoryForm(this, event)">
        @csrf
        <input type="hidden" id="item_id" name="item_id">
        <div class="mb-3">
          <label for="name" class="form-label">Item Name</label>
          <input type="text" class="form-control" id="name" name="name" maxlength="255" required>
        </div>
        <div class="mb-3">
          <label for="category_id" class="form-label">Category</label>
          <select class="form-select" id="category_id" name="category_id" required>
            <option value="" disabled selected>Select category</option>
            @foreach($categories as $category)
              <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label for="quantity" class="form-label">Quantity</label>
          <input type="number" class="form-control" id="quantity" name="quantity" min="0" required>
        </div>
        <div class="mb-3">
          <label for="description" class="form-label">Description</label>
          <textarea class="form-control" id="description" name="description" rows="3"></textarea>
        </div>
        @if(Auth::user()->role_id == 1)
        <div class="mb-3">
          <label for="clinic_id" class="form-label">Clinic</label>
          <select class="form-select" id="clinic_id" name="clinic_id" required>
            <option value="" disabled selected>Select clinic</option>
            @foreach($clinics as $clinic)
              <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
            @endforeach
          </select>
        </div>
        @else
        <input type="hidden" id="clinic_id" name="clinic_id" value="{{ Auth::user()->clinic_id }}">
        @endif
        <input type="hidden" name="added_by" value="{{ Auth::id() }}">

        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary" id="submitBtn">Save Item</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openAddSidebar() {
      document.getElementById('sidebarTitle').textContent = 'Add Inventory Item';
      document.getElementById('inventoryItemForm').reset();
      document.getElementById('inventoryItemForm').action = '/inventory';
      document.getElementById('submitBtn').textContent = 'Save Item';
      var sidebar = new bootstrap.Offcanvas(document.getElementById('addClinicSidebar'));
      sidebar.show();
    }

    function openEditSidebar(id, name, category_id, quantity, description, clinic_id) {
      document.getElementById('sidebarTitle').textContent = 'Edit Inventory Item';
      document.getElementById('item_id').value = id;
      document.getElementById('name').value = name;
      document.getElementById('category_id').value = category_id;
      document.getElementById('quantity').value = quantity;
      document.getElementById('description').value = description;
      document.getElementById('clinic_id').value = clinic_id;
      document.getElementById('inventoryItemForm').action = `/inventory/${id}`;
      document.getElementById('submitBtn').textContent = 'Update Item';
      
      var sidebar = new bootstrap.Offcanvas(document.getElementById('addClinicSidebar'));
      sidebar.show();
    }

    function closeSidebar() {
      var sidebar = bootstrap.Offcanvas.getInstance(document.getElementById('addClinicSidebar'));
      sidebar.hide();
      
      // Reset validation states
      const form = document.getElementById('inventoryItemForm');
      const formElements = form.elements;
      for (let i = 0; i < formElements.length; i++) {
        formElements[i].classList.remove('is-invalid');
      }
    }

    function validateInventoryForm(form, event) {
      event.preventDefault();
      let isValid = true;
      const formElements = form.elements;
      
      // Reset all validation states
      for (let i = 0; i < formElements.length; i++) {
        const element = formElements[i];
        element.classList.remove('is-invalid');
      }

      // Validate item name
      const itemName = document.getElementById('name');
      if (!itemName.value || itemName.value.trim().length < 3) {
        itemName.classList.add('is-invalid');
        isValid = false;
      }

      // Validate category
      const category = document.getElementById('category_id');
      if (!category.value) {
        category.classList.add('is-invalid');
        isValid = false;
      }

      // Validate quantity
      const quantity = document.getElementById('quantity');
      if (!quantity.value || isNaN(quantity.value) || parseInt(quantity.value) < 0) {
        quantity.classList.add('is-invalid');
        isValid = false;
      }

      // Validate clinic if applicable
      const clinic = document.getElementById('clinic_id');
      if (clinic && !clinic.value) {
        clinic.classList.add('is-invalid');
        isValid = false;
      }

      // If form is valid, submit it
      if (isValid) {
        form.submit();
        return true;
      } else {
        // Scroll to first invalid field
        const firstInvalid = form.querySelector('.is-invalid');
        if (firstInvalid) {
          firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
          firstInvalid.focus();
        }
        return false;
      }
    }

    function deleteItem(id) {
      if(confirm('Are you sure you want to delete this item?')) {
        fetch(`/inventory/${id}/delete`)
        .then(response => {
          if (response.ok) {
            location.reload();
          } else {
            alert(`Failed to delete item (HTTP ${response.status})`);
          }
        })
        .catch(() => alert('Failed to delete item: could not reach the server.'));
      }
    }

    function filterTable() {
      const searchInput = document.getElementById('searchInput').value.toLowerCase();
      const categoryFilter = document.getElementById('categoryFilter').value.toLowerCase();
      const stockFilter = document.getElementById('stockFilter').value;
      const table = document.getElementById('inventoryTable');
      const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

      for (let row of rows) {
        const itemName = row.getElementsByTagName('td')[0].textContent.toLowerCase();
        const category = row.getElementsByTagName('td')[1].textContent.toLowerCase();
        const quantity = parseInt(row.getElementsByTagName('td')[2].textContent);
        
        let showRow = true;

        // Search filter
        if (!itemName.includes(searchInput)) {
          showRow = false;
        }

        // Category filter
        if (categoryFilter && !category.includes(categoryFilter)) {
          showRow = false;
        }

        // Stock level filter
        if (stockFilter) {
          switch(stockFilter) {
            case 'low':
              if (quantity >= 10) showRow = false;
              break;
            case 'out':
              if (quantity > 0) showRow = false;
              break;
            case 'available':
              if (quantity <= 0) showRow = false;
              break;
          }
        }

        row.style.display = showRow ? '' : 'none';
      }
    }

    function resetFilters() {
      document.getElementById('searchInput').value = '';
      document.getElementById('categoryFilter').value = '';
      document.getElementById('stockFilter').value = '';
      filterTable();
    }
  </script>
  
  @endsection
