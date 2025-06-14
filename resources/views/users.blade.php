@extends('layouts.user_type.auth')

@section('content')

  <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h6>Users table</h6>
                <button class="btn btn-primary btn-sm" onclick="openSidebar('add')">
                  <i class="fas fa-plus"></i>&nbsp;&nbsp;Add New User
                </button>
              </div>
              
              <!-- Search and Filter Controls -->
              <div class="d-flex gap-3 align-items-center">
                <div class="flex-grow-1">
                  <input type="text" class="form-control" id="searchInput" placeholder="Search users..." onkeyup="filterTable()">
                </div>
                <div class="w-25">
                  <select class="form-select" id="roleFilter" onchange="filterTable()">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                      
                      @if($role->id != 1 && auth()->user()->role_id != 1)
                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                      @endif
                    @endforeach
                  </select>
                </div>
                {{-- <div class="w-25">
                  <select class="form-select" id="clinicFilter" onchange="filterTable()">
                    <option value="">All Clinics</option>
                    @foreach($clinics as $clinic)
                      <option value="{{ $clinic->name }}">{{ $clinic->name }}</option>
                    @endforeach
                  </select>
                </div> --}}
              </div>
            </div>

            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0" id="usersTable">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Name</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Email</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date Created</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Role</th>
                      @if(auth()->user()->role_id == 1 || Route::currentRouteName() == 'owners')
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Clinic</th>
                      @endif
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($users as $user)
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div>
                            <img src="{{ $user->avatar == null ? asset('assets/img/team-2.jpg') : asset('storage/' . $user->avatar) }}" class="avatar avatar-sm me-3" alt="{{ $user->name }}">
                          </div>
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">{{ $user->name }}</h6>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-xs text-secondary mb-0">{{ $user->email }}</p>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ $user->created_at->format('d/m/y') }}</span>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="badge badge-sm {{ 
                          ($user->role->id === 1 ? 'bg-blue-500' : 
                          ($user->role->id === 2 ? 'bg-gradient-primary' :
                          ($user->role->id === 3 ? 'bg-gradient-info' :
                          ($user->role->id === 4 ? 'bg-gradient-success' : 
                          'bg-gradient-warning'))))
                          }}">
                          {{ $user->role->name }}
                        </span>
                      </td>
                      @if(auth()->user()->role_id == 1 || Route::currentRouteName() == 'owners')
                      <td class="align-middle text-center text-sm">
                        <span class="text-secondary text-xs font-weight-bold">
                          {{ $user->clinic->id == 1 ? "N/A" : $user->clinic->name }}
                        </span>
                      </td>
                      @endif
                      <td class="align-middle text-center">
                        <div class="d-flex gap-1 justify-content-center">
                          <button class="btn btn-icon-only btn-rounded btn-outline-primary mb-0 p-2 d-flex align-items-center justify-content-center" 
                                  onclick="editUser({{ $user }})"
                                  data-bs-toggle="tooltip" 
                                  data-bs-placement="top"
                                  title="Edit User">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                              <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                            </svg>
                          </button>
                          
                          <form action="{{ Route::currentRouteName() . "/" . $user->id }}/delete" method="GET">
                            @csrf
                            <button class="btn btn-icon-only btn-rounded btn-outline-danger mb-0 p-2 d-flex align-items-center justify-content-center"
                                  onclick="deleteUser({{ $user->id }})"
                                  data-bs-toggle="tooltip"
                                  data-bs-placement="top" 
                                  title="Delete User">
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

  <!-- User Sidebar -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="userSidebar">
    <div class="offcanvas-header border-bottom">
      <h5 class="offcanvas-title" id="sidebarTitle">Add New User</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close">
        <span aria-hidden="true" class="text-3xl">&times;</span>
      </button>
    </div>
    <div class="offcanvas-body">
      <form id="userForm" enctype="multipart/form-data" method="POST">
        @csrf
        <input type="hidden" name="user_id" id="userId">
        <div class="mb-3">
          <label for="userImage" class="form-label">Profile Image</label>
          <div class="d-flex align-items-center">
            <img id="imagePreview" src="../assets/img/team-2.jpg" class="avatar avatar-lg me-3" alt="Profile Preview">
            <div class="upload-btn-wrapper">
              <button class="btn btn-outline-primary btn-sm">Upload Photo</button>
              <input type="file" name="avatar" id="userImage" accept="image/*" onchange="previewImage(this)"/>
            </div>
          </div>
        </div>
        <div class="mb-3">
          <label for="userName" class="form-label">Name</label>
          <input type="text" class="form-control" name="name" id="userName" required>
        </div>
        <div class="mb-3">
          <label for="userEmail" class="form-label">Email</label>
          <input type="email" class="form-control" name="email" id="userEmail" required>
        </div>
        <div class="mb-3">
          <label for="userRole" class="form-label">Role</label>
          <select class="form-select" name="role_id" id="userRole" required>
            <option value="">Select Role</option>
            @foreach($roles as $role)
              @if($role->id >= auth()->user()->role_id)
                <option value="{{ $role->id }}">{{ $role->name }}</option>
              @endif
            @endforeach
          </select>
        </div>
       
        <div class="mb-3">
          @if(auth()->user()->role_id == 1)
          <label for="userClinic" class="form-label">Clinic</label>
            <select class="form-select" name="clinic_id" id="userClinic" required>
              <option value="">Select Clinic</option>
              @foreach($clinics as $clinic)
                <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
              @endforeach
            </select>
          @else
            <input type="hidden" id="userClinic" name="clinic_id" value="{{ auth()->user()->clinic_id }}">
            @endif
        </div>
       
        <div class="mb-3">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="use_default_password" id="defaultPasswordSwitch">
            <label class="form-check-label" for="defaultPasswordSwitch">Use Default Password</label>
          </div>
        </div>
        <div class="mb-3" id="passwordField">
          <label for="userPassword" class="form-label">Password</label>
          <input type="password" class="form-control" name="password" id="userPassword">
        </div>
        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary" id="saveButton">Save User</button>
        </div>
      </form>
    </div>
  </div>

  <style>
    .upload-btn-wrapper {
      position: relative;
      overflow: hidden;
      display: inline-block;
    }

    .upload-btn-wrapper input[type=file] {
      font-size: 100px;
      position: absolute;
      left: 0;
      top: 0;
      opacity: 0;
      cursor: pointer;
    }
  </style>

  <script>
    function filterTable() {
      const searchInput = document.getElementById('searchInput').value.toLowerCase();
      const roleFilter = document.getElementById('roleFilter').value;
      const clinicFilter = document.getElementById('clinicFilter').value;
      const table = document.getElementById('usersTable');
      const rows = table.getElementsByTagName('tr');

      for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const name = row.getElementsByTagName('td')[0].textContent.toLowerCase();
        const email = row.getElementsByTagName('td')[1].textContent.toLowerCase();
        const role = row.getElementsByTagName('td')[3].textContent.trim();
        const clinic = row.getElementsByTagName('td')[4].textContent.trim();

        const matchesSearch = name.includes(searchInput) || email.includes(searchInput);
        const matchesRole = !roleFilter || role === roleFilter;
        const matchesClinic = !clinicFilter || clinic === clinicFilter;

        row.style.display = matchesSearch && matchesRole && matchesClinic ? '' : 'none';
      }
    }

    function openSidebar(mode, userData = null) {
      const sidebar = new bootstrap.Offcanvas(document.getElementById('userSidebar'));
      const form = document.getElementById('userForm');
      const title = document.getElementById('sidebarTitle');
      const saveButton = document.getElementById('saveButton');

      if (mode === 'edit') {
        title.textContent = 'Edit User';
        saveButton.textContent = 'Update User';
        loadUserData(userData);
      } else {
        title.textContent = 'Add New User';
        saveButton.textContent = 'Save User';
        form.reset();
        document.getElementById('imagePreview').src = '../assets/img/team-2.jpg';
        document.getElementById('userId').value = '';
      }

      sidebar.show();
    }

    function closeSidebar() {
      const sidebar = bootstrap.Offcanvas.getInstance(document.getElementById('userSidebar'));
      sidebar.hide();
    }

    async function editUser(userData) {
      openSidebar('edit', userData);
    }

    async function deleteUser(userId) {
      const response = await fetch(`/users/userId/delete`, {
            method: 'DELETE',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
          })
    }

    async function loadUserData(userData) {
      try {
        document.getElementById('userId').value = userData.id;
        document.getElementById('userName').value = userData.name;
        document.getElementById('userEmail').value = userData.email;
        document.getElementById('userRole').value = userData.role.id;
        document.getElementById('userClinic').value = userData.clinic.id;
        
        if (userData.avatar) {
          document.getElementById('imagePreview').src = '{{ asset("storage/") }}/' + userData.avatar;
        }
        
        document.getElementById('passwordField').style.display = 'none';
        document.getElementById('defaultPasswordSwitch').parentElement.style.display = 'none';
      } catch (error) {
        console.error('Error loading user data:', error);
        alert('Failed to load user data');
      }
    }

    document.getElementById('defaultPasswordSwitch').addEventListener('change', function() {
      document.getElementById('passwordField').style.display = this.checked ? 'none' : 'block';
    });

    function previewImage(input) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('imagePreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
      }
    }

    document.getElementById('userForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const userId = document.getElementById('userId').value;
      const formData = new FormData(this);
      
      try {
        const url = userId ? `{{ Route::currentRouteName() }}${userId}` : '{{ Route::currentRouteName() }}';
        const method = 'POST';
        
        const response = await fetch(url, {
          method: method,
          body: formData
        });

        if (response.ok) {
          closeSidebar();
          window.location.reload();
        } else {
          const error = await response.json();
          alert(error.message || 'Failed to save user');
        }
      } catch (error) {
        console.error('Error saving user:', error);
        alert('Failed to save user');
      }
    });
  </script>
  
  @endsection
