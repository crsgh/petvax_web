@extends('layouts.user_type.auth')

@section('content')
<div class="staffs-page">
  <div class="page-header">
    <div class="header-content">
      <h1 class="page-title"></h1>
      <p class="page-subtitle">Manage clinic staff and their roles</p>
    </div>
    <div class="header-actions">
      <x-ui.button 
        variant="primary" 
        size="default" 
        icon-name="add"
        onclick="openModal('staffModal')"
      >
        Add New Staff
      </x-ui.button>
    </div>
  </div>

  <!-- Search and Filters -->
  <div class="filters-section">
    <div class="filters-grid">
      <div class="filter-group">
        <x-ui.input 
          type="text" 
          id="searchStaff" 
          placeholder="Search staff members..."
          onkeyup="filterStaff()"
        />
      </div>
      
      <div class="filter-group">
        <x-ui.select id="roleFilter" onchange="filterStaff()">
          <option value="">All Roles</option>
          <option value="veterinarian">Veterinarian</option>
          <option value="assistant">Assistant</option>
          <option value="receptionist">Receptionist</option>
          <option value="admin">Admin</option>
        </x-ui.select>
      </div>
      
      <div class="filter-group">
        <x-ui.button variant="secondary" size="sm" onclick="resetFilters()">
          Reset Filters
        </x-ui.button>
      </div>
    </div>
  </div>

  <div class="staffs-content">
    <div class="staffs-table-wrapper">
      <table class="staffs-table" id="staffsTable">
        <thead>
          <tr>
            <th>Staff Member</th>
            <th>Email</th>
            <th>Role</th>
            <th>Clinic</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @php
            // Sample staff data - replace with actual data from controller
            $staffMembers = [
              (object)[
                'id' => 1,
                'name' => 'Dr. Sarah Johnson',
                'email' => 'sarah.johnson@petvax.com',
                'role' => 'veterinarian',
                'clinic' => 'Main Clinic',
                'status' => 'active',
                'avatar' => null,
                'created_at' => now()
              ],
              (object)[
                'id' => 2,
                'name' => 'Mike Chen',
                'email' => 'mike.chen@petvax.com',
                'role' => 'assistant',
                'clinic' => 'Main Clinic',
                'status' => 'active',
                'avatar' => null,
                'created_at' => now()
              ]
            ];
          @endphp
          
          @foreach($staffMembers as $staff)
          <tr data-name="{{ strtolower($staff->name) }}" data-role="{{ $staff->role }}">
            <td>
              <div class="staff-info">
                <x-ui.avatar 
                  :src="$staff->avatar ? asset('storage/' . $staff->avatar) : null"
                  :alt="$staff->name"
                  size="sm"
                />
                <div class="staff-details">
                  <div class="staff-name">{{ $staff->name }}</div>
                  <div class="staff-id">ID: #{{ $staff->id }}</div>
                </div>
              </div>
            </td>
            <td>
              <span class="staff-email">{{ $staff->email }}</span>
            </td>
            <td>
              @php
                $roleVariant = match($staff->role) {
                  'veterinarian' => 'primary',
                  'assistant' => 'info',
                  'receptionist' => 'success',
                  'admin' => 'warning',
                  default => 'secondary'
                };
              @endphp
              <x-ui.badge :variant="$roleVariant">
                {{ ucfirst($staff->role) }}
              </x-ui.badge>
            </td>
            <td>
              <span class="clinic-name">{{ $staff->clinic }}</span>
            </td>
            <td>
              <x-ui.badge :variant="$staff->status === 'active' ? 'success' : 'danger'">
                {{ ucfirst($staff->status) }}
              </x-ui.badge>
            </td>
            <td>
              <div class="actions-group">
                <x-ui.button 
                  variant="secondary" 
                  size="xs" 
                  icon-name="eye"
                  onclick="viewStaff({{ $staff->id }})"
                  title="View Details"
                >View</x-ui.button>
                <x-ui.button 
                  variant="primary" 
                  size="xs" 
                  icon-name="edit"
                  onclick="editStaff({{ json_encode($staff) }})"
                  title="Edit Staff"
                >Edit</x-ui.button>
                <x-ui.button 
                  variant="danger" 
                  size="xs" 
                  icon-name="delete"
                  onclick="deleteStaff({{ $staff->id }})"
                  title="Delete Staff"
                >Delete</x-ui.button>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Staff Modal -->
<x-ui.modal id="staffModal" title="Add New Staff" size="lg">
  <form id="staffForm" enctype="multipart/form-data" method="POST" action="/staffs">
    @csrf
    <input type="hidden" name="staff_id" id="staffId">
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Profile Image -->
      <div class="md:col-span-2">
        <label class="form-label-clean">Profile Photo</label>
        <div class="profile-upload">
          <x-ui.avatar id="staffImagePreview" size="lg" />
          <div class="upload-controls">
            <input type="file" name="avatar" id="staffImage" accept="image/*" onchange="previewStaffImage(this)" class="hidden"/>
            <x-ui.button 
              type="button" 
              variant="secondary" 
              size="sm"
              onclick="document.getElementById('staffImage').click()"
            >
              Upload Photo
            </x-ui.button>
            <div class="upload-hint">JPG, PNG up to 2MB</div>
          </div>
        </div>
      </div>

      <!-- Full Name -->
      <x-ui.input 
        label="Full Name"
        name="name" 
        id="staffName" 
        required 
        placeholder="Enter full name"
      />

      <!-- Email -->
      <x-ui.input 
        label="Email Address"
        type="email" 
        name="email" 
        id="staffEmail" 
        required 
        placeholder="Enter email address"
      />

      <!-- Role -->
      <x-ui.select 
        label="Role"
        name="role" 
        id="staffRole" 
        required
        placeholder="Select Role"
      >
        <option value="veterinarian">Veterinarian</option>
        <option value="assistant">Assistant</option>
        <option value="receptionist">Receptionist</option>
        <option value="admin">Admin</option>
      </x-ui.select>

      <!-- Clinic -->
      <x-ui.select 
        label="Clinic"
        name="clinic_id" 
        id="staffClinic" 
        required
        placeholder="Select Clinic"
      >
        <option value="1">Main Clinic</option>
        <option value="2">Branch Clinic</option>
      </x-ui.select>

      <!-- Phone -->
      <x-ui.input 
        label="Phone Number"
        type="tel" 
        name="phone" 
        id="staffPhone" 
        placeholder="Enter phone number"
      />

      <!-- Status -->
      <x-ui.select 
        label="Status"
        name="status" 
        id="staffStatus" 
        required
      >
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
      </x-ui.select>
    </div>

    <div class="form-actions">
      <x-ui.button 
        type="button" 
        variant="secondary" 
        onclick="closeModal('staffModal')"
      >
        Cancel
      </x-ui.button>
      <x-ui.button 
        type="submit" 
        variant="primary" 
        id="saveStaffButton"
      >
        Save Staff
      </x-ui.button>
    </div>
  </form>
</x-ui.modal>

<style>
/* Staffs Page Layout */
.staffs-page {
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

.filters-grid {
  display: grid;
  grid-template-columns: 1fr auto auto;
  gap: 1rem;
  align-items: end;
}

.filter-group {
  display: flex;
  flex-direction: column;
}

.staffs-content {
  background: white;
  border-radius: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.staffs-table-wrapper {
  overflow-x: auto;
}

.staffs-table {
  width: 100%;
  border-collapse: collapse;
  font-family: 'Poppins', sans-serif;
}

.staffs-table thead {
  background: #f8fafc;
  border-bottom: 1px solid #e5e7eb;
}

.staffs-table th {
  padding: 1rem 1.5rem;
  text-align: left;
  font-weight: 600;
  font-size: 0.875rem;
  color: #374151;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-family: 'Poppins', sans-serif;
}

.staffs-table td {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}

.staffs-table tbody tr:hover {
  background: #f8fafc;
}

.staff-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.staff-details {
  flex: 1;
}

.staff-name {
  font-weight: 600;
  color: #111827;
  font-size: 0.875rem;
  margin-bottom: 0.25rem;
}

.staff-id {
  font-size: 0.75rem;
  color: #6b7280;
}

.staff-email {
  font-size: 0.875rem;
  color: #374151;
}

.clinic-name {
  font-size: 0.875rem;
  color: #374151;
}

.actions-group {
  display: flex;
  gap: 0.5rem;
}

.profile-upload {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.upload-controls {
  flex: 1;
}

.upload-hint {
  font-size: 0.75rem;
  color: #6b7280;
  margin-top: 0.25rem;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e5e7eb;
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
.md\:col-span-2 {
  grid-column: span 2 / span 2;
}

@media (max-width: 768px) {
  .staffs-page {
    padding: 1rem;
  }
  
  .page-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .filters-grid {
    grid-template-columns: 1fr;
    gap: 0.75rem;
  }
  
  .md\:grid-cols-2 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
  }
  
  .md\:col-span-2 {
    grid-column: span 1 / span 1;
  }
  
  .actions-group {
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .profile-upload {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }
}
</style>

<script>
function filterStaff() {
  const searchTerm = document.getElementById('searchStaff').value.toLowerCase();
  const roleFilter = document.getElementById('roleFilter').value;
  const rows = document.querySelectorAll('#staffsTable tbody tr');

  rows.forEach(row => {
    const name = row.dataset.name;
    const role = row.dataset.role;

    const matchesSearch = name.includes(searchTerm);
    const matchesRole = !roleFilter || role === roleFilter;

    row.style.display = matchesSearch && matchesRole ? '' : 'none';
  });
}

function resetFilters() {
  document.getElementById('searchStaff').value = '';
  document.getElementById('roleFilter').value = '';
  filterStaff();
}

function previewStaffImage(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('staffImagePreview').src = e.target.result;
    }
    reader.readAsDataURL(input.files[0]);
  }
}

function editStaff(staffData) {
  document.getElementById('staffId').value = staffData.id;
  document.getElementById('staffName').value = staffData.name;
  document.getElementById('staffEmail').value = staffData.email;
  document.getElementById('staffRole').value = staffData.role;
  document.getElementById('staffClinic').value = staffData.clinic_id || '1';
  document.getElementById('staffPhone').value = staffData.phone || '';
  document.getElementById('staffStatus').value = staffData.status;
  
  if (staffData.avatar) {
    document.getElementById('staffImagePreview').src = `{{ asset('storage/') }}/${staffData.avatar}`;
  }
  
  document.getElementById('saveStaffButton').textContent = 'Update Staff';
  document.querySelector('#staffModal .modal-content h3').textContent = 'Edit Staff';
  
  openModal('staffModal');
}

async function deleteStaff(staffId) {
  if (confirm('Are you sure you want to delete this staff member?')) {
    try {
      const response = await fetch(`/staffs/${staffId}/delete`, {
        method: 'GET',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
        },
      });
      const data = await response.json();
      if (data.success) {
        window.location.reload();
      } else {
        alert(data.message || 'Failed to delete staff member');
      }
    } catch (error) {
      console.error('Error deleting staff:', error);
      alert('Failed to delete staff member');
    }
  }
}

function viewStaff(staffId) {
  // Implementation for viewing staff details
  console.log('View staff:', staffId);
}

// Form submission
document.getElementById('staffForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  const staffId = document.getElementById('staffId').value;
  
  try {
    const url = staffId ? `/staffs/${staffId}` : '/staffs';
    const response = await fetch(url, {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    });

    if (response.ok) {
      closeModal('staffModal');
      window.location.reload();
    } else {
      const data = await response.json();
      alert(data.message || 'Failed to save staff member');
    }
  } catch (error) {
    console.error('Error saving staff:', error);
    alert('Failed to save staff member');
  }
});

// Reset form when modal closes
document.addEventListener('DOMContentLoaded', function() {
  const modal = document.getElementById('staffModal');
  if (modal) {
    const observer = new MutationObserver(function(mutations) {
      mutations.forEach(function(mutation) {
        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
          if (modal.classList.contains('hidden')) {
            // Reset form when modal is hidden
            document.getElementById('staffForm').reset();
            document.getElementById('staffId').value = '';
            document.getElementById('staffImagePreview').src = '{{ asset("assets/img/team-2.jpg") }}';
            document.getElementById('saveStaffButton').textContent = 'Save Staff';
            const titleElement = document.querySelector('#staffModal .modal-content h3');
            if (titleElement) titleElement.textContent = 'Add New Staff';
          }
        }
      });
    });
    observer.observe(modal, { attributes: true });
  }
});
</script>
@endsection
