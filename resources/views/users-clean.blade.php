@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
  <div class="container-clean py-4">
    
    <x-ui.card title="Users Management" subtitle="Manage system users and their roles">
      <x-slot name="headerActions">
        <x-ui.button 
          variant="primary" 
          size="sm" 
          icon-name="add"
          onclick="openModal('userModal')"
        >
          Add New User
        </x-ui.button>
      </x-slot>
      
      <!-- Search and Filter Controls -->
      <div class="flex gap-4 mb-6">
        <div class="flex-1">
          <x-ui.input 
            type="text" 
            id="searchInput" 
            placeholder="Search users..." 
            onkeyup="filterTable()"
          />
        </div>
        <div style="min-width: 200px;">
          <x-ui.select id="roleFilter" onchange="filterTable()">
            <option value="">All Roles</option>
            @foreach($roles as $role)
              @if($role->id != 1 && auth()->user()->role_id != 1)
                <option value="{{ $role->name }}">{{ $role->name }}</option>
              @endif
            @endforeach
          </x-ui.select>
        </div>
      </div>

      <!-- Users Table -->
      <x-ui.table 
        :headers="['User', 'Email', 'Date Created', 'Role', auth()->user()->role_id == 1 || Route::currentRouteName() == 'owners' ? 'Clinic' : '', 'Actions']"
        class="users-table"
      >
        @foreach($users as $user)
        <tr>
          <td>
            <div class="flex items-center gap-3">
              <x-ui.avatar 
                :src="$user->avatar ? asset('storage/' . $user->avatar) : null"
                :alt="$user->name"
                size="sm"
              />
              <div>
                <div class="font-medium text-gray-800">{{ $user->name }}</div>
              </div>
            </div>
          </td>
          <td>
            <span class="text-sm text-gray-600">{{ $user->email }}</span>
          </td>
          <td>
            <span class="text-sm text-gray-500">{{ $user->created_at->format('M d, Y') }}</span>
          </td>
          <td>
            @php
              $badgeVariant = match($user->role->id) {
                1 => 'primary',
                2 => 'info',
                3 => 'success',
                4 => 'warning',
                default => 'secondary'
              };
            @endphp
            <x-ui.badge :variant="$badgeVariant">
              {{ $user->role->name }}
            </x-ui.badge>
          </td>
          @if(auth()->user()->role_id == 1 || Route::currentRouteName() == 'owners')
          <td>
            <span class="text-sm text-gray-600">
              {{ $user->clinic->id == 1 ? "N/A" : $user->clinic->name }}
            </span>
          </td>
          @endif
          <td>
            <div class="flex gap-2">
              <x-ui.button 
                  variant="secondary" 
                  size="xs" 
                  icon-name="edit"
                  onclick="editUser({{ $user }})"
                  title="Edit User"
                >Edit</x-ui.button>
                <button class="btn-clean btn-danger-clean btn-xs-clean" onclick="deleteUser({{ $user->id }})" title="Delete User">
                  <i class="fas fa-trash-alt" style="font-size:12px;margin-right:4px"></i> Delete
                </button>
            </div>
          </td>
        </tr>
        @endforeach
      </x-ui.table>

      <!-- Pagination -->
      @if($users->hasPages() && $users->total() > $users->perPage())
      <div class="flex justify-end mt-4">
        {{ $users->links() }}
      </div>
      @endif
    </x-ui.card>
  </div>
</main>

<!-- User Modal -->
<x-ui.modal id="userModal" title="Add New User" size="lg">
  <form id="userForm" enctype="multipart/form-data" method="POST">
    @csrf
    <input type="hidden" name="user_id" id="userId">
    
    <!-- Error Alert -->
    <div class="alert alert-danger hidden mb-4" id="validationErrors">
      <ul id="errorList"></ul>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Profile Image -->
      <div class="md:col-span-2">
        <label class="form-label-clean">Profile Image</label>
        <div class="flex items-center gap-4">
          <x-ui.avatar id="imagePreview" size="lg" />
          <div>
            <input type="file" name="avatar" id="userImage" accept="image/*" onchange="previewImage(this)" class="hidden"/>
            <x-ui.button 
              type="button" 
              variant="secondary" 
              size="sm"
              onclick="document.getElementById('userImage').click()"
            >
              Upload Photo
            </x-ui.button>
            <div class="text-xs text-gray-500 mt-1">JPG, PNG up to 2MB</div>
          </div>
        </div>
      </div>

      <!-- Name -->
      <x-ui.input 
        label="Full Name"
        name="name" 
        id="userName" 
        required 
        minlength="3" 
        maxlength="50"
        placeholder="Enter full name"
      />

      <!-- Email -->
      <x-ui.input 
        label="Email Address"
        type="email" 
        name="email" 
        id="userEmail" 
        required 
        pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$"
        placeholder="Enter email address"
      />

      <!-- Role -->
      <x-ui.select 
        label="Role"
        name="role_id" 
        id="userRole" 
        required
        placeholder="Select Role"
      >
        @foreach($roles as $role)
          @if($role->id >= auth()->user()->role_id)
            <option value="{{ $role->id }}">{{ $role->name }}</option>
          @endif
        @endforeach
      </x-ui.select>

      <!-- Clinic -->
      @if(auth()->user()->role_id == 1)
        <x-ui.select 
          label="Clinic"
          name="clinic_id" 
          id="userClinic" 
          required
          placeholder="Select Clinic"
        >
          @foreach($clinics as $clinic)
            <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
          @endforeach
        </x-ui.select>
      @else
        <input type="hidden" id="userClinic" name="clinic_id" value="{{ auth()->user()->clinic_id }}">
      @endif
    </div>

    <div class="flex justify-end gap-3 mt-6">
      <x-ui.button 
        type="button" 
        variant="secondary" 
        onclick="closeModal('userModal')"
      >
        Cancel
      </x-ui.button>
      <x-ui.button 
        type="submit" 
        variant="primary" 
        id="saveButton"
      >
        Save User
      </x-ui.button>
    </div>
  </form>
</x-ui.modal>

<script>
function filterTable() {
  const searchInput = document.getElementById('searchInput').value.toLowerCase();
  const roleFilter = document.getElementById('roleFilter').value;
  const table = document.querySelector('.users-table table');
  const rows = table.querySelectorAll('tbody tr');

  rows.forEach(row => {
    const cells = row.querySelectorAll('td');
    const name = cells[0].textContent.toLowerCase();
    const email = cells[1].textContent.toLowerCase();
    const role = cells[3].textContent.trim();

    const matchesSearch = name.includes(searchInput) || email.includes(searchInput);
    const matchesRole = !roleFilter || role === roleFilter;

    row.style.display = matchesSearch && matchesRole ? '' : 'none';
  });
}

async function editUser(userData) {
  document.getElementById('userId').value = userData.id;
  document.getElementById('userName').value = userData.name;
  document.getElementById('userEmail').value = userData.email;
  document.getElementById('userRole').value = userData.role.id;
  document.getElementById('userClinic').value = userData.clinic.id;
  
  if (userData.avatar) {
    document.getElementById('imagePreview').src = '{{ asset("storage/") }}/' + userData.avatar;
  }
  
  document.getElementById('saveButton').textContent = 'Update User';
  document.querySelector('#userModal h3').textContent = 'Edit User';
  
  openModal('userModal');
}

async function deleteUser(userId) {
  if (confirm('Are you sure you want to delete this user?')) {
    try {
      const response = await fetch(`/owners/${userId}/delete`, {
        method: 'GET',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json',
        },
      });
      const data = await response.json();
      if (data.success) {
        window.location.reload();
      } else {
        alert(data.message || 'Failed to delete user');
      }
    } catch (error) {
      console.error('Error deleting user:', error);
      alert('Failed to delete user');
    }
  }
}

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
    const url = userId ? `{{ Route::currentRouteName() }}/${userId}` : '{{ Route::currentRouteName() }}';
    const response = await fetch(url, {
      method: 'POST',
      body: formData
    });

    if (response.ok) {
      closeModal('userModal');
      window.location.reload();
    }
  } catch (error) {
    console.error('Error saving user:', error);
    alert('Failed to save user');
  }
});

// Reset form when modal closes
document.addEventListener('DOMContentLoaded', function() {
  const modal = document.getElementById('userModal');
  const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
      if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
        if (modal.classList.contains('hidden')) {
          // Reset form when modal is hidden
          document.getElementById('userForm').reset();
          document.getElementById('userId').value = '';
          document.getElementById('imagePreview').src = '{{ asset("assets/img/team-2.jpg") }}';
          document.getElementById('saveButton').textContent = 'Save User';
          document.querySelector('#userModal h3').textContent = 'Add New User';
        }
      }
    });
  });
  observer.observe(modal, { attributes: true });
});
</script>

<style>
.grid {
  display: grid;
}
.grid-cols-1 {
  grid-template-columns: repeat(1, minmax(0, 1fr));
}
.grid-cols-2 {
  grid-template-columns: repeat(2, minmax(0, 1fr));
}
.md\:grid-cols-2 {
  grid-template-columns: repeat(2, minmax(0, 1fr));
}
.md\:col-span-2 {
  grid-column: span 2 / span 2;
}
.flex-1 {
  flex: 1 1 0%;
}
@media (max-width: 768px) {
  .md\:grid-cols-2 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
  }
  .md\:col-span-2 {
    grid-column: span 1 / span 1;
  }
}
</style>
@endsection
