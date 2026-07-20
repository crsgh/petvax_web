@extends('layouts.user_type.auth')

@section('content')
<div class="users-page">
  <div class="page-header">
    <h1 class="page-title"></h1>
    <div class="header-actions">
      <div class="search-wrapper">
        <x-ui.icon name="search" class="search-icon w-4 h-4" />
        <input 
          type="text" 
          id="searchInput" 
          class="search-input"
          placeholder="Search users..." 
          onkeyup="filterTable()"
        />
      </div>
      
      <x-ui.button 
        variant="secondary" 
        size="default" 
        icon-name="filter"
        onclick="openModal('filterModal')"
      >
        Filters
      </x-ui.button>
      
      <x-ui.button 
        variant="primary" 
        size="default" 
        icon-name="add"
        onclick="openSidebar('userSidebar')"
      >
        Add New User
      </x-ui.button>
    </div>
  </div>

  <div class="users-content" style="background: white; border-radius: 16px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #f1f5f9; overflow: hidden;">
    
    <div class="users-table-wrapper">
      <table class="users-table" id="usersTable">
        <thead>
          <tr>
            <th>User</th>
            <th>Email</th>
            <th>Date Created</th>
            <th>Role</th>
            @if(auth()->user()->role_id == 1 || Route::currentRouteName() == 'owners')
            <th>Clinic</th>
            @endif
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @if($users && $users->count() > 0)
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
                <x-ui.button 
                  variant="danger" 
                  size="xs" 
                  icon-name="delete"
                  onclick="deleteUser({{ $user->id }})"
                  title="Delete User"
                >Delete</x-ui.button>
              </div>
            </td>
          </tr>
            @endforeach
          @else
            <tr>
              <td colspan="6" class="empty-state">
                <div class="empty-content">
                  <x-ui.icon name="users" class="w-12 h-12 empty-icon text-gray-400" />
                  <h3 class="empty-title">No Users Found</h3>
                  <p class="empty-text">There are no users to display. Add a new user to get started.</p>
                  <x-ui.button variant="primary" size="sm" onclick="openModal('userModal')">
                    Add First User
                  </x-ui.button>
                </div>
              </td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
    
    <!-- Pagination -->
    <x-smart-pagination :items="$users ?? null" />
  </div>
</div>

<!-- User Sidebar -->
<div id="userSidebar" class="sidebar-overlay hidden">
  <div class="sidebar-backdrop" onclick="closeSidebar('userSidebar')"></div>
  <div class="sidebar-content">
    <div class="sidebar-header">
      <h3 class="sidebar-title">Add New User</h3>
      <button type="button" class="sidebar-close" onclick="closeSidebar('userSidebar')">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M13 1L1 13M1 1L13 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
    </div>

    <div class="sidebar-body">
      <form id="userForm" enctype="multipart/form-data" method="POST">
        @csrf
        <input type="hidden" name="user_id" id="userId">
        
        <!-- Profile Image -->
        <div class="form-section">
          <label class="form-label">Profile Photo</label>
          <div class="profile-image-center">
            <div class="profile-image-upload" onclick="document.getElementById('avatarInput').click()">
              <img id="imagePreview" src="{{ asset('assets/img/team-2.jpg') }}" alt="Profile Preview" class="profile-preview">
              <div class="upload-overlay">
                <x-ui.icon name="image" class="w-5 h-5" />
                <span>Change Photo</span>
              </div>
              <input type="file" name="avatar" id="avatarInput" accept="image/*" onchange="previewImage(this)" style="display: none;">
            </div>
            <div class="upload-hint">Click to upload photo</div>
          </div>
        </div>

        <!-- Form Fields -->
        <div class="form-fields">
          <div class="form-group">
            <label class="form-label">Full Name *</label>
            <input type="text" name="name" id="userName" class="form-input" required placeholder="Enter full name">
          </div>
          
          <div class="form-group">
            <label class="form-label">Email Address *</label>
            <input type="email" name="email" id="userEmail" class="form-input" required placeholder="Enter email address">
          </div>

          <div class="form-group">
            <label class="form-label">Role *</label>
            <select name="role_id" id="userRole" class="form-select" required>
              <option value="">Select Role</option>
              @if(isset($roles))
                @foreach($roles as $role)
                  @if($role->id >= auth()->user()->role_id)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                  @endif
                @endforeach
              @endif
            </select>
          </div>

          @if(auth()->user()->role_id == 1)
          <div class="form-group">
            <label class="form-label">Clinic *</label>
            <select name="clinic_id" id="userClinic" class="form-select" required>
              <option value="">Select Clinic</option>
              @if(isset($clinics))
                @foreach($clinics as $clinic)
                  <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
                @endforeach
              @endif
            </select>
          </div>
          @else
            <input type="hidden" id="userClinic" name="clinic_id" value="{{ auth()->user()->clinic_id }}">
          @endif
        </div>

        <div class="sidebar-actions">
          <button type="button" class="btn-secondary" onclick="closeSidebar('userSidebar')">
            Cancel
          </button>
          <button type="submit" class="btn-primary" id="saveButton">
            Save User
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Filter Modal -->
<x-ui.modal id="filterModal" title="Filter Users" size="sm">
  <div class="filter-form">
    <div class="form-group">
      <label class="form-label">Role</label>
      <select id="roleFilter" class="form-select" onchange="filterTable()">
        <option value="">All Roles</option>
        @if(isset($roles))
          @foreach($roles as $role)
            @if($role->id != 1 && auth()->user()->role_id != 1)
              <option value="{{ $role->name }}">{{ $role->name }}</option>
            @endif
          @endforeach
        @endif
      </select>
    </div>
    
    <div class="form-group">
      <label class="form-label">Status</label>
      <select id="statusFilter" class="form-select" onchange="filterTable()">
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
      </select>
    </div>
  </div>
  
  <div class="modal-actions">
    <x-ui.button 
      type="button" 
      variant="secondary" 
      onclick="clearFilters()"
      class="modal-action-btn"
    >
      Clear Filters
    </x-ui.button>
    <x-ui.button 
      type="button" 
      variant="primary" 
      onclick="closeModal('filterModal')"
      class="modal-action-btn"
    >
      Apply Filters
    </x-ui.button>
  </div>
</x-ui.modal>

<!-- Settings Modal (Admin Only) -->
@if(auth()->user()->role_id == 1)
<x-ui.modal id="settingsModal" title="Site Settings" size="md">
  <form id="settingsForm">
    @csrf
    <div class="settings-form">
      <div class="form-group">
        <label class="form-label">Site Primary Color</label>
        <div class="color-picker-wrapper">
          <input 
            type="color" 
            id="siteColor" 
            class="color-picker"
            value="#3b82f6"
            onchange="updateSiteColor(this.value)"
          />
          <input 
            type="text" 
            id="siteColorHex" 
            class="color-input"
            value="#3b82f6"
            placeholder="#3b82f6"
            onchange="updateSiteColorFromHex(this.value)"
          />
        </div>
        <small class="form-hint">Choose the primary color for buttons, links, and accents</small>
      </div>
      
      <div class="form-group">
        <label class="form-label">Site Font</label>
        <select id="siteFont" class="form-select" onchange="updateSiteFont(this.value)">
          <option value="Poppins">Poppins (Default)</option>
          <option value="Inter">Inter</option>
          <option value="Roboto">Roboto</option>
          <option value="Open Sans">Open Sans</option>
          <option value="Lato">Lato</option>
          <option value="Montserrat">Montserrat</option>
        </select>
        <small class="form-hint">Choose the font family for the entire site</small>
      </div>
      
      <div class="preview-section">
        <h4>Preview</h4>
        <div class="preview-content">
          <button class="preview-button" style="background: var(--primary-color, #3b82f6); font-family: var(--site-font, 'Poppins');">
            Sample Button
          </button>
          <p style="font-family: var(--site-font, 'Poppins');">
            This is how your text will look with the selected font.
          </p>
        </div>
      </div>
    </div>
    
    <div class="modal-actions">
      <x-ui.button 
        type="button" 
        variant="secondary" 
        onclick="resetSettings()"
        class="modal-action-btn"
      >
        Reset to Default
      </x-ui.button>
      <x-ui.button 
        type="submit" 
        variant="primary"
        class="modal-action-btn"
      >
        Save Settings
      </x-ui.button>
    </div>
  </form>
</x-ui.modal>
@endif

<style>
/* Users Page Layout */
.users-page {
  padding: 1.5rem;
  background: #fff;
  min-height: 100vh;
  font-family: 'Poppins', sans-serif;
 
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.page-title {
  font-size: 1.75rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
  letter-spacing: -0.025em;
  font-family: 'Poppins', sans-serif;
}

.header-actions {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.users-content {
  background: white;
  border-radius: 16px;
  overflow: hidden;
}

.users-table-wrapper {
  overflow-x: auto;
  padding-bottom: 0;
}

.filters-section {
  padding: 1.25rem 1.5rem;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  border-bottom: 1px solid #e2e8f0;
}

.search-filter-container {
  display: flex;
  gap: 1rem;
  align-items: center;
  max-width: 800px;
}

.search-wrapper {
  position: relative;
  flex: 1;
}

.search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #9ca3af;
  pointer-events: none;
}

.search-input {
  width: 100%;
  padding: 0.875rem 1rem 0.875rem 2.5rem;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  font-size: 0.875rem;
  background: white;
  transition: all 0.2s ease;
  font-family: 'Poppins', sans-serif;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.search-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1), 0 1px 3px rgba(0, 0, 0, 0.1);
  transform: translateY(-1px);
}

.search-input:focus + .search-icon {
  color: #3b82f6;
}

.filter-select {
  min-width: 160px;
  padding: 0.875rem 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  font-size: 0.875rem;
  background: white;
  transition: all 0.2s ease;
  font-family: 'Poppins', sans-serif;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  cursor: pointer;
}

.filter-select:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1), 0 1px 3px rgba(0, 0, 0, 0.1);
  transform: translateY(-1px);
}

.filter-select:hover {
  border-color: #d1d5db;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Header Search Styles */
.header-actions .search-wrapper {
  position: relative;
  width: 300px;
  padding: 1px;
}

.header-actions .search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #9ca3af;
  pointer-events: none;
  z-index: 1;
}

.header-actions .search-input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 0.875rem;
  background: white;
  transition: all 0.2s ease;
  font-family: 'Poppins', sans-serif;
}

.header-actions .search-input:focus {
  outline: none;
  border: 1px solid #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Settings Modal Styles */
.settings-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.color-picker-wrapper {
  display: flex;
  gap: 0.75rem;
  align-items: center;
}

.color-picker {
  width: 60px;
  height: 40px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  cursor: pointer;
  background: none;
}

.color-input {
  flex: 1;
  padding: 0.75rem 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 0.875rem;
  font-family: 'Poppins', sans-serif;
}

.form-hint {
  color: #6b7280;
  font-size: 0.75rem;
  margin-top: 0.25rem;
}

.preview-section {
  padding: 1rem;
  background: #f8fafc;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
}

.preview-section h4 {
  margin: 0 0 1rem 0;
  font-size: 1rem;
  font-weight: 600;
  color: #374151;
}

.preview-content {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.preview-button {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 8px;
  color: white;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.preview-button:hover {
  opacity: 0.9;
}

/* Modal Actions */
.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 1.5rem;
}

.modal-action-btn {
  flex: 1;
  min-width: 120px;
}

/* CSS Variables for Dynamic Theming */
:root {
  --primary-color: #3b82f6;
  --site-font: 'Poppins', sans-serif;
}

/* Apply dynamic theming */
.btn-primary,
.sidebar-actions .btn-primary {
  background: var(--primary-color) !important;
}

body, 
.users-page,
.form-label,
.page-title {
  font-family: var(--site-font) !important;
}

.users-table-wrapper {
  overflow-x: auto;
}

.users-table {
  width: 100%;
  border-collapse: collapse;
  font-family: 'Poppins', sans-serif;
  margin-bottom: 0;
}

.users-table thead {
  background: #f8fafc;
  border-bottom: 1px solid #e5e7eb;
}

.users-table th {
  padding: 1rem 1.5rem;
  text-align: left;
  font-weight: 600;
  font-size: 0.875rem;
  color: #374151;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-family: 'Poppins', sans-serif;
}

.users-table td {
  padding: 0.75rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}

.users-table tbody tr:hover {
  background: #f8fafc;
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

.empty-state {
  padding: 3rem 2rem;
  text-align: center;
}

.empty-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.empty-icon {
  font-size: 3rem;
  color: #9ca3af;
}

.empty-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #374151;
  margin: 0;
}

.empty-text {
  color: #6b7280;
  margin: 0;
  max-width: 400px;
}

.pagination-wrapper {
  padding: 1.5rem;
  border-top: 1px solid #f1f5f9;
  display: flex;
  justify-content: center;
}

/* Sidebar Styles */
.sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 1000;
  display: flex;
  justify-content: flex-end;
  visibility: hidden;
  transition: visibility 0.3s ease;
}

.sidebar-overlay:not(.hidden) {
  visibility: visible;
}

.sidebar-backdrop {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.sidebar-overlay:not(.hidden) .sidebar-backdrop {
  opacity: 1;
}

.sidebar-content {
  position: relative;
  width: 480px;
  height: 100vh;
  background: white;
  box-shadow: -4px 0 20px rgba(0, 0, 0, 0.15);
  display: flex;
  flex-direction: column;
  transform: translateX(100%);
  transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.sidebar-overlay:not(.hidden) .sidebar-content {
  transform: translateX(0);
}

.sidebar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 1.5rem 1rem 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.sidebar-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
  font-family: 'Poppins', sans-serif;
}

.sidebar-close {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border: none;
  background: #f8fafc;
  color: #64748b;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.sidebar-close:hover {
  background: #e2e8f0;
  color: #475569;
}

.sidebar-body {
  flex: 1;
  padding: 1.5rem;
  overflow-y: auto;
}

.form-section {
  margin-bottom: 2rem;
}

.profile-image-center {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.profile-image-upload {
  position: relative;
  cursor: pointer;
  border-radius: 50%;
  overflow: hidden;
  width: 120px;
  height: 120px;
  border: 4px solid #e5e7eb;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.profile-image-upload:hover {
  border-color: #3b82f6;
  transform: scale(1.02);
  box-shadow: 0 8px 20px rgba(59, 130, 246, 0.2);
}

.profile-preview {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.upload-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: white;
  opacity: 0;
  transition: opacity 0.3s ease;
  font-size: 0.875rem;
}

.profile-image-upload:hover .upload-overlay {
  opacity: 1;
}

.upload-hint {
  font-size: 0.75rem;
  color: #6b7280;
  text-align: center;
  font-weight: 500;
}

.form-fields {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-label {
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.5rem;
  font-family: 'Poppins', sans-serif;
}

.form-input, .form-select {
  padding: 1rem 1.25rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 0.875rem;
  transition: all 0.2s ease;
  font-family: 'Poppins', sans-serif;
  background: #fafbfc;
  width: 100%;
}

.form-input:focus, .form-select:focus {
  outline: none;
  border-color: #3b82f6;
  background: white;
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
  transform: translateY(-1px);
}

.form-input::placeholder {
  color: #9ca3af;
}

.sidebar-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  padding-top: 2rem;
  margin-top: 2rem;
  border-top: 1px solid #e5e7eb;
}

.btn-primary, .btn-secondary {
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 600;
  font-family: 'Poppins', sans-serif;
  transition: all 0.2s ease;
  border: none;
  cursor: pointer;
}

.btn-primary {
  background: #3b82f6;
  color: white;
}

.btn-primary:hover {
  background: #2563eb;
}

.btn-secondary {
  background: #f3f4f6;
  color: #374151;
}

.btn-secondary:hover {
  background: #e5e7eb;
}

@media (max-width: 768px) {
  .users-page {
    padding: 1rem;
  }
  
  .page-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .header-actions {
    flex-direction: column;
    width: 100%;
    gap: 0.75rem;
  }
  
  .header-actions .search-wrapper {
    width: 100%;
  }
  
  .header-actions .search-input {
    border: 1px solid #e2e8f0;
  }
  
  .header-actions .search-input:focus {
    border: 1px solid #3b82f6;
  }
  
  .actions-group {
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .users-table th,
  .users-table td {
    padding: 0.75rem 0.5rem;
    font-size: 0.75rem;
  }
  
  .sidebar-content {
    width: 100vw;
  }
  
  .sidebar-header {
    padding: 1.5rem;
  }
  
  .sidebar-body {
    padding: 1.5rem;
  }
  
  .profile-image-upload {
    width: 100px;
    height: 100px;
  }
  
  .color-picker-wrapper {
    flex-direction: column;
    align-items: stretch;
  }
  
  .color-picker {
    width: 100%;
  }
}
</style>

<script>
// Sidebar Functions
function openSidebar(sidebarId) {
  document.getElementById(sidebarId).classList.remove('hidden');
  document.body.style.overflow = 'hidden';
}

function closeSidebar(sidebarId) {
  document.getElementById(sidebarId).classList.add('hidden');
  document.body.style.overflow = 'auto';
}

function filterTable() {
  const searchInput = document.getElementById('searchInput').value.toLowerCase();
  const roleFilter = document.getElementById('roleFilter').value;
  const table = document.getElementById('usersTable');
  const rows = table.querySelectorAll('tbody tr');

  rows.forEach(row => {
    const cells = row.querySelectorAll('td');
    if (cells.length > 0) {
      const name = cells[0].textContent.toLowerCase();
      const email = cells[1].textContent.toLowerCase();
      const role = cells[3] ? cells[3].textContent.trim() : '';

      const matchesSearch = name.includes(searchInput) || email.includes(searchInput);
      const matchesRole = !roleFilter || role === roleFilter;

      row.style.display = matchesSearch && matchesRole ? '' : 'none';
    }
  });
}

function resetFilters() {
  document.getElementById('searchInput').value = '';
  document.getElementById('roleFilter').value = '';
  filterTable();
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
  document.querySelector('#userSidebar .sidebar-title').textContent = 'Edit User';
  
  openSidebar('userSidebar');
}

async function deleteUser(userId) {
  if (confirm('Are you sure you want to delete this user?')) {
    try {
      const response = await fetch(`/owners/${userId}/delete`, {
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
      closeSidebar('userSidebar');
      window.location.reload();
    }
  } catch (error) {
    console.error('Error saving user:', error);
    alert('Failed to save user');
  }
});

// Settings Functions
function updateSiteColor(color) {
  document.documentElement.style.setProperty('--primary-color', color);
  document.getElementById('siteColorHex').value = color;
  
  // Update preview button
  const previewButton = document.querySelector('.preview-button');
  if (previewButton) {
    previewButton.style.background = color;
  }
  
  // Save to localStorage
  localStorage.setItem('siteColor', color);
}

function updateSiteColorFromHex(color) {
  if (color.match(/^#[0-9A-F]{6}$/i)) {
    document.getElementById('siteColor').value = color;
    updateSiteColor(color);
  }
}

function updateSiteFont(font) {
  document.documentElement.style.setProperty('--site-font', `'${font}', sans-serif`);
  
  // Update preview text
  const previewElements = document.querySelectorAll('.preview-content *');
  previewElements.forEach(el => {
    el.style.fontFamily = `'${font}', sans-serif`;
  });
  
  // Save to localStorage
  localStorage.setItem('siteFont', font);
}

function resetSettings() {
  updateSiteColor('#3b82f6');
  updateSiteFont('Poppins');
  document.getElementById('siteColor').value = '#3b82f6';
  document.getElementById('siteColorHex').value = '#3b82f6';
  document.getElementById('siteFont').value = 'Poppins';
}

// Filter Functions
function clearFilters() {
  document.getElementById('roleFilter').value = '';
  document.getElementById('statusFilter').value = '';
  filterTable();
}

// Load saved settings on page load
function loadSavedSettings() {
  const savedColor = localStorage.getItem('siteColor');
  const savedFont = localStorage.getItem('siteFont');
  
  if (savedColor) {
    updateSiteColor(savedColor);
    document.getElementById('siteColor').value = savedColor;
    document.getElementById('siteColorHex').value = savedColor;
  }
  
  if (savedFont) {
    updateSiteFont(savedFont);
    document.getElementById('siteFont').value = savedFont;
  }
}

// Settings form submission
const settingsForm = document.getElementById('settingsForm');
if (settingsForm) {
  settingsForm.addEventListener('submit', function(e) {
  e.preventDefault();
  
  const color = document.getElementById('siteColor').value;
  const font = document.getElementById('siteFont').value;
  
  // Save settings (you can add API call here)
  updateSiteColor(color);
  updateSiteFont(font);
  
  // Close modal
  closeModal('settingsModal');
  
  // Show success message
  alert('Settings saved successfully!');
  });
}

// Reset form when sidebar closes
document.addEventListener('DOMContentLoaded', function() {
  const sidebar = document.getElementById('userSidebar');
  if (sidebar) {
    const observer = new MutationObserver(function(mutations) {
      mutations.forEach(function(mutation) {
        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
          if (sidebar.classList.contains('hidden')) {
            // Reset form when sidebar is hidden
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
            document.getElementById('imagePreview').src = '{{ asset("assets/img/team-2.jpg") }}';
            document.getElementById('saveButton').textContent = 'Save User';
            const titleElement = document.querySelector('#userSidebar .sidebar-title');
            if (titleElement) titleElement.textContent = 'Add New User';
          }
        }
      });
    });
    observer.observe(sidebar, { attributes: true });
  }
  
  // Load saved settings
  loadSavedSettings();
});
</script>
@endsection
