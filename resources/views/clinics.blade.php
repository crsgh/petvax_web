@extends('layouts.user_type.auth')

@section('content')
<div class="clinics-page">
  <div class="page-header">
    <div class="header-content">
      <h1 class="page-title">Clinic Management</h1>
      <p class="page-subtitle">Manage your veterinary clinic locations and details</p>
    </div>
    <div class="header-actions">
      <div class="search-wrapper">
        <i class="fas fa-search search-icon"></i>
        <input 
          type="text" 
          id="clinicSearchInput"
          class="search-input"
          placeholder="Search clinics..." 
          onkeyup="filterClinics()"
        />
      </div>
      
      <x-ui.button 
        variant="primary" 
        size="default" 
        icon-name="add"
        onclick="openSidebar('clinicSidebar')"
      >
        Add New Clinic
      </x-ui.button>
    </div>
  </div>

  <!-- Stats Summary -->
  <div class="clinic-stats">
    <div class="clinic-stat-card">
      <div class="clinic-stat-icon total">
        <i class="fas fa-building"></i>
      </div>
      <div class="clinic-stat-info">
        <span class="clinic-stat-value">{{ $clinics->count() }}</span>
        <span class="clinic-stat-label">Total Clinics</span>
      </div>
    </div>
    <div class="clinic-stat-card">
      <div class="clinic-stat-icon active">
        <i class="fas fa-check-circle"></i>
      </div>
      <div class="clinic-stat-info">
        <span class="clinic-stat-value">{{ $clinics->where('status', 'active')->count() }}</span>
        <span class="clinic-stat-label">Active</span>
      </div>
    </div>
    <div class="clinic-stat-card">
      <div class="clinic-stat-icon inactive">
        <i class="fas fa-times-circle"></i>
      </div>
      <div class="clinic-stat-info">
        <span class="clinic-stat-value">{{ $clinics->where('status', 'inactive')->count() }}</span>
        <span class="clinic-stat-label">Inactive</span>
      </div>
    </div>
  </div>

  <!-- Splash Hero -->
  <div class="clinic-splash">
    <div class="splash-overlay"></div>
    <div class="splash-content">
      <div class="splash-icon">
        <i class="fas fa-clinic-medical"></i>
      </div>
      <div class="splash-text">
        <h2>Veterinary Clinics</h2>
        <p>Manage all your clinic locations, hours, and services in one place</p>
      </div>
    </div>
  </div>

  <div class="clinics-content">
    <div class="clinics-table-wrapper">
      <table class="clinics-table" id="clinicsTable">
        <thead>
          <tr>
            <th>Clinic</th>
            <th>Location</th>
            <th>Contact Info</th>
            <th>Operating Hours</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($clinics as $clinic)
          <tr data-search="{{ strtolower($clinic->name) }} {{ strtolower($clinic->address) }} {{ strtolower($clinic->status) }}" data-clinic-id="{{ $clinic->id }}" data-clinic='{{ json_encode($clinic->toArray()) }}'>
            <td>
              <div class="clinic-info">
                <div class="clinic-avatar">
                  @if($clinic->image)
                    <img src="{{ asset('storage/' . $clinic->image) }}" alt="{{ $clinic->name }}" class="clinic-image">
                  @else
                    <div class="clinic-placeholder">
                      <i class="fas fa-building" style="font-size:20px"></i>
                    </div>
                  @endif
                </div>
                <div class="clinic-details">
                  <div class="clinic-name">{{ $clinic->name }}</div>
                  <div class="clinic-id">ID: #{{ $clinic->id }}</div>
                </div>
              </div>
            </td>
            <td>
              <div class="location-info">
                <div class="address">{{ Str::limit($clinic->address, 50) }}</div>
                @if($clinic->latitude && $clinic->longitude)
                  <div class="coordinates">{{ number_format($clinic->latitude, 4) }}, {{ number_format($clinic->longitude, 4) }}</div>
                @endif
              </div>
            </td>
            <td>
              <div class="contact-info">
                <div class="contact-phone">
                  <i class="fas fa-phone" style="font-size:14px;color:#3b82f6"></i>
                  {{ $clinic->contact }}
                </div>
                <div class="contact-email">
                  <i class="fas fa-envelope" style="font-size:14px;color:#6b7280"></i>
                  {{ $clinic->email }}
                </div>
              </div>
            </td>
            <td>
              <div class="hours-info">
                <div class="operating-days">
                  @php
                    $days = json_decode($clinic->operation_days);
                    $shortDays = array_map(function($day) { return substr($day, 0, 3); }, $days);
                  @endphp
                  {{ implode(', ', $shortDays) }}
                </div>
                <div class="operating-hours">
                  {{ date('H:i', strtotime($clinic->opening_time)) }} - {{ date('H:i', strtotime($clinic->closing_time)) }}
                </div>
              </div>
            </td>
            <td>
              <x-ui.badge :variant="$clinic->status === 'active' ? 'success' : 'danger'">
                {{ ucfirst($clinic->status) }}
              </x-ui.badge>
            </td>
            <td>
              <div class="flex gap-2">
                <x-ui.button 
                  variant="secondary" 
                  size="xs" 
                  icon-name="eye"
                  onclick="viewClinic({{ $clinic->id }})"
                  title="View Details"
                >View</x-ui.button>
                <x-ui.button 
                  variant="primary" 
                  size="xs" 
                  icon-name="edit"
                  onclick="editClinic({{ $clinic->toJson() }})"
                  title="Edit Clinic"
                >Edit</x-ui.button>
                <button class="btn-clean btn-danger-clean btn-xs-clean" onclick="deleteClinic({{ $clinic->id }})" title="Delete Clinic">
                  <i class="fas fa-trash-alt" style="font-size:12px;margin-right:4px"></i> Delete
                </button>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Clinic Sidebar -->
<div id="clinicSidebar" class="sidebar-overlay hidden">
  <div class="sidebar-backdrop" onclick="closeSidebar('clinicSidebar')"></div>
  <div class="sidebar-content">
    <div class="sidebar-header">
      <h3 class="sidebar-title" id="sidebarTitle">Add New Clinic</h3>
      <button type="button" class="sidebar-close" onclick="closeSidebar('clinicSidebar')">
        <i class="fas fa-times"></i>
      </button>
    </div>
    
    <div class="sidebar-body">
      <form id="clinicForm" enctype="multipart/form-data" method="POST" action="/clinics">
        @csrf
        <input type="hidden" name="clinic_id" id="clinicId">
        
        <!-- Clinic Image -->
        <div class="form-group">
          <label class="form-label">Clinic Photo</label>
          <div class="image-upload-section">
            <div class="clinic-image-preview">
              <img id="clinicImagePreview" src="{{ asset('assets/img/default-clinic.jpg') }}" alt="Clinic Preview">
            </div>
            <div class="upload-controls">
              <input type="file" name="clinic_image" id="clinicImage" accept="image/*" onchange="previewClinicImage(this)" class="hidden"/>
              <x-ui.button 
                type="button" 
                variant="secondary" 
                size="sm"
                onclick="document.getElementById('clinicImage').click()"
              >
                Upload Photo
              </x-ui.button>
              <div class="upload-hint">JPG, PNG up to 2MB</div>
            </div>
          </div>
        </div>

        <!-- Clinic Name -->
        <div class="form-group">
          <label class="form-label">Clinic Name *</label>
          <input type="text" 
                 class="form-input" 
                 name="clinic_name" 
                 id="clinicName" 
                 required 
                 placeholder="Enter clinic name">
        </div>

        <!-- Contact Information -->
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Phone Number *</label>
            <input type="tel" 
                   class="form-input" 
                   name="clinic_phone" 
                   id="clinicPhone" 
                   required 
                   placeholder="Contact number">
          </div>
          <div class="form-group">
            <label class="form-label">Email Address *</label>
            <input type="email" 
                   class="form-input" 
                   name="clinic_email" 
                   id="clinicEmail" 
                   required 
                   placeholder="Email address">
          </div>
        </div>

        <!-- Operating Hours -->
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Opening Time *</label>
            <input type="time" 
                   class="form-input" 
                   name="opening_time" 
                   id="openingTime" 
                   required>
          </div>
          <div class="form-group">
            <label class="form-label">Closing Time *</label>
            <input type="time" 
                   class="form-input" 
                   name="closing_time" 
                   id="closingTime" 
                   required>
          </div>
        </div>

        <!-- Status -->
        <div class="form-group">
          <label class="form-label">Status *</label>
          <select class="form-input" name="clinic_status" id="clinicStatus" required>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>

        <!-- Address -->
        <div class="form-group">
          <label class="form-label">Clinic Address *</label>
          <div class="location-section">
            <div class="location-search">
              <input type="text" 
                     class="form-input" 
                     id="locationSearch" 
                     placeholder="Search for location...">
              <div class="search-buttons">
                <button type="button" class="search-btn" onclick="searchLocation()" title="Search">
                  <i class="fas fa-search"></i>
                </button>
                <button type="button" class="location-btn" onclick="getCurrentLocation()" title="Use current location">
                  <i class="fas fa-map-pin"></i>
                </button>
              </div>
            </div>
            
            <div id="searchResults" class="search-results hidden"></div>
            
            <div class="map-container">
              <div id="map" class="clinic-map"></div>
            </div>
            
            <input type="text" 
                   class="form-input" 
                   name="clinic_address" 
                   id="clinicAddress" 
                   required 
                   readonly
                   placeholder="Address will appear here">
            
            <input type="hidden" id="latitude" name="latitude">
            <input type="hidden" id="longitude" name="longitude">
          </div>
        </div>

        <!-- Operating Days -->
        <div class="form-group">
          <label class="form-label">Operating Days *</label>
          <div class="days-grid">
            @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
              <label class="day-checkbox">
                <input type="checkbox" name="operating_days[]" value="{{ $day }}" id="{{ $day }}">
                <span class="checkmark"></span>
                <span class="day-label">{{ ucfirst(substr($day, 0, 3)) }}</span>
              </label>
            @endforeach
          </div>
        </div>
      </form>
    </div>
    
    <div class="sidebar-footer">
      <x-ui.button 
        type="button" 
        variant="secondary" 
        onclick="closeSidebar('clinicSidebar')"
      >
        Cancel
      </x-ui.button>
      <x-ui.button 
        type="submit" 
        variant="primary" 
        id="saveClinicButton"
        form="clinicForm"
      >
        Save Clinic
      </x-ui.button>
    </div>
  </div>
</div>

<!-- Clinic Details Modal -->
<x-ui.modal id="clinicDetailsModal" title="Clinic Details" size="lg">
  <div id="clinicDetailsContent">
    <!-- Content will be populated by JavaScript -->
  </div>
</x-ui.modal>

<style>
/* Clinics Page Layout */
.clinics-page {
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
}

.header-content {
  flex: 1;
}

.page-title {
  font-size: 1.5rem;
  font-weight: 600;
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
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.search-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 1rem;
  color: #9ca3af;
  pointer-events: none;
}

.search-input {
  padding: 0.625rem 1rem 0.625rem 2.5rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.875rem;
  width: 260px;
  background: white;
  color: #374151;
  transition: all 0.2s ease;
  font-family: 'Poppins', sans-serif;
}

.search-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.search-input::placeholder {
  color: #9ca3af;
}

/* Stats Summary */
.clinic-stats {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.25rem;
  margin-bottom: 1.5rem;
}

.clinic-stat-card {
  background: white;
  border-radius: 12px;
  padding: 1.25rem 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  border: 1px solid #f3f4f6;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
  transition: all 0.3s ease;
}

.clinic-stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.clinic-stat-icon {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  font-size: 18px;
}

.clinic-stat-icon.total { background: #eff6ff; color: #3b82f6; }
.clinic-stat-icon.active { background: #ecfdf5; color: #10b981; }
.clinic-stat-icon.inactive { background: #fef2f2; color: #ef4444; }

.clinic-stat-info {
  display: flex;
  flex-direction: column;
}

.clinic-stat-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: #111827;
  line-height: 1.2;
}

.clinic-stat-label {
  font-size: 0.8rem;
  color: #6b7280;
  font-weight: 500;
}

/* Splash Hero */
.clinic-splash {
  position: relative;
  background: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #60a5fa 100%);
  border-radius: 16px;
  overflow: hidden;
  margin-bottom: 1.5rem;
  min-height: 140px;
  display: flex;
  align-items: center;
}

.splash-overlay {
  position: absolute;
  inset: 0;
  background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
  opacity: 0.4;
}

.splash-content {
  position: relative;
  display: flex;
  align-items: center;
  gap: 1.5rem;
  padding: 2rem 2.5rem;
  z-index: 1;
}

.splash-icon {
  width: 64px;
  height: 64px;
  background: rgba(255, 255, 255, 0.15);
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  color: white;
  flex-shrink: 0;
  backdrop-filter: blur(4px);
}

.splash-text h2 {
  font-size: 1.5rem;
  font-weight: 700;
  color: white;
  margin: 0 0 0.25rem 0;
}

.splash-text p {
  font-size: 0.9rem;
  color: rgba(255, 255, 255, 0.85);
  margin: 0;
}

@media (max-width: 640px) {
  .splash-content {
    flex-direction: column;
    text-align: center;
    padding: 1.5rem;
  }
}

.clinics-content {
  background: white;
  border-radius: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.clinics-table-wrapper {
  overflow-x: auto;
}

.clinics-table {
  width: 100%;
  border-collapse: collapse;
  font-family: 'Poppins', sans-serif;
}

.clinics-table thead {
  background: #f8fafc;
  border-bottom: 1px solid #e5e7eb;
}

.clinics-table th {
  padding: 1rem 1.5rem;
  text-align: left;
  font-weight: 600;
  font-size: 0.875rem;
  color: #374151;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-family: 'Poppins', sans-serif;
}

.clinics-table td {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}

.clinics-table tbody tr:hover {
  background: #f8fafc;
}

.clinic-info {
  display: flex;
  align-items: center;
  gap: var(--space-3);
}

.clinic-avatar {
  width: 48px;
  height: 48px;
  border-radius: var(--radius-lg);
  overflow: hidden;
  flex-shrink: 0;
}

.clinic-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.clinic-placeholder {
  width: 100%;
  height: 100%;
  background: var(--gray-200);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--gray-500);
  font-size: 20px;
}

.clinic-name {
  font-weight: 600;
  color: var(--gray-900);
  font-size: var(--font-size-sm);
}

.clinic-id {
  font-size: var(--font-size-xs);
  color: var(--gray-500);
}

.location-info .address {
  font-weight: 500;
  color: var(--gray-900);
  font-size: var(--font-size-sm);
  margin-bottom: var(--space-1);
}

.coordinates {
  font-size: var(--font-size-xs);
  color: var(--gray-500);
  font-family: monospace;
}

.contact-info {
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
}

.contact-phone, .contact-email {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  font-size: var(--font-size-sm);
}

.hours-info .operating-days {
  font-weight: 500;
  color: var(--gray-900);
  font-size: var(--font-size-sm);
  margin-bottom: var(--space-1);
}

.operating-hours {
  font-size: var(--font-size-xs);
  color: var(--gray-500);
  font-family: monospace;
}

.clinic-image-preview {
  width: 80px;
  height: 80px;
  border-radius: var(--radius-lg);
  overflow: hidden;
  border: 2px solid var(--gray-200);
}

.clinic-image-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.map-container {
  border-radius: var(--radius-lg);
  overflow: hidden;
  border: 1px solid var(--gray-200);
}

.clinic-map {
  height: 200px;
  width: 100%;
}

.search-results {
  max-height: 200px;
  overflow-y: auto;
  border: 1px solid var(--gray-200);
  border-radius: var(--radius);
  background: white;
}

.search-result-item {
  padding: var(--space-3);
  border-bottom: 1px solid var(--gray-100);
  cursor: pointer;
  transition: var(--transition);
}

.search-result-item:hover {
  background: var(--gray-50);
}

.search-result-item:last-child {
  border-bottom: none;
}

.operating-days-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
  gap: var(--space-3);
}

.day-checkbox {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--space-2);
  padding: var(--space-3);
  border: 1px solid var(--gray-200);
  border-radius: var(--radius);
  cursor: pointer;
  transition: var(--transition);
  text-align: center;
}

.day-checkbox:hover {
  background: var(--gray-50);
  border-color: var(--primary);
}

.day-checkbox input[type="checkbox"] {
  display: none;
}

.checkmark {
  width: 20px;
  height: 20px;
  border: 2px solid var(--gray-300);
  border-radius: 4px;
  position: relative;
  transition: var(--transition);
}

.day-checkbox input[type="checkbox"]:checked + .checkmark {
  background: var(--primary);
  border-color: var(--primary);
}

.day-checkbox input[type="checkbox"]:checked + .checkmark::after {
  content: '✓';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: white;
  font-size: 12px;
  font-weight: bold;
}

.day-checkbox input[type="checkbox"]:checked ~ .day-label {
  color: var(--primary);
  font-weight: 600;
}

.day-label {
  font-size: var(--font-size-xs);
  font-weight: 500;
  color: var(--gray-700);
  transition: var(--transition);
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
  width: 500px;
  max-width: 90vw;
  background: white;
  box-shadow: -4px 0 24px rgba(0, 0, 0, 0.15);
  display: flex;
  flex-direction: column;
  height: 100vh;
  transform: translateX(100%);
  transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.sidebar-overlay:not(.hidden) .sidebar-content {
  transform: translateX(0);
}

.sidebar-header {
  padding: 1.5rem 2rem;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #f8fafc;
}

.sidebar-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

.sidebar-close {
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.sidebar-close:hover {
  background: #e5e7eb;
  color: #374151;
}

.sidebar-body {
  flex: 1;
  overflow-y: auto;
  padding: 2rem;
}

.sidebar-footer {
  padding: 1.5rem 2rem;
  border-top: 1px solid #e5e7eb;
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  background: #f8fafc;
}

/* Form Styles */
.form-group {
  margin-bottom: 1.5rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.5rem;
}

.form-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.875rem;
  transition: all 0.2s ease;
  background: white;
}

.form-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.image-upload-section {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.clinic-image-preview {
  width: 80px;
  height: 80px;
  border-radius: 8px;
  overflow: hidden;
  border: 2px solid #e5e7eb;
  flex-shrink: 0;
}

.clinic-image-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.upload-controls {
  flex: 1;
}

.upload-hint {
  font-size: 0.75rem;
  color: #6b7280;
  margin-top: 0.5rem;
}

.location-section {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.location-search {
  display: flex;
  gap: 0.5rem;
}

.search-buttons {
  display: flex;
  gap: 0.5rem;
}

.search-btn, .location-btn {
  background: #f3f4f6;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  padding: 0.75rem;
  cursor: pointer;
  transition: all 0.2s ease;
  color: #374151;
}

.search-btn:hover, .location-btn:hover {
  background: #e5e7eb;
  border-color: #9ca3af;
}

.clinic-map {
  height: 200px;
  width: 100%;
  border-radius: 8px;
}

.days-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 0.75rem;
}

.day-checkbox {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
  text-align: center;
}

.day-checkbox:hover {
  background: #f9fafb;
  border-color: #3b82f6;
}

.day-checkbox input[type="checkbox"] {
  display: none;
}

.checkmark {
  width: 18px;
  height: 18px;
  border: 2px solid #d1d5db;
  border-radius: 4px;
  position: relative;
  transition: all 0.2s ease;
}

.day-checkbox input[type="checkbox"]:checked + .checkmark {
  background: #3b82f6;
  border-color: #3b82f6;
}

.day-checkbox input[type="checkbox"]:checked + .checkmark::after {
  content: '✓';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: white;
  font-size: 12px;
  font-weight: bold;
}

.day-checkbox input[type="checkbox"]:checked ~ .day-label {
  color: #3b82f6;
  font-weight: 600;
}

.day-label {
  font-size: 0.75rem;
  font-weight: 500;
  color: #6b7280;
  transition: all 0.2s ease;
}

@media (max-width: 1024px) {
  .lg\:grid-cols-2 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
  }
}

@media (max-width: 768px) {
  .grid-cols-2 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
  }
  
  .operating-days-grid {
    grid-template-columns: repeat(4, 1fr);
  }

  .clinic-stats {
    grid-template-columns: 1fr;
  }

  .search-input {
    width: 180px;
  }

  .header-actions {
    flex-wrap: wrap;
  }
}
</style>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
let map;
let marker;

// Initialize map
function initMap() {
  const defaultLocation = [14.5995, 120.9842]; // Philippines coordinates
  map = L.map('map').setView(defaultLocation, 13);
  
  L.tileLayer('https://mt1.google.com/vt/lyrs=r&x={x}&y={y}&z={z}', {
    maxZoom: 20,
    attribution: '© Google Maps'
  }).addTo(map);
  
  map.on('click', function(e) {
    const latlng = e.latlng;
    setMarkerAndAddress(latlng.lat, latlng.lng);
  });
}

function setMarkerAndAddress(lat, lng) {
  if (marker) {
    marker.setLatLng([lat, lng]);
  } else {
    marker = L.marker([lat, lng]).addTo(map);
  }
  
  fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
    .then(response => response.json())
    .then(data => {
      document.getElementById('clinicAddress').value = data.display_name;
      document.getElementById('latitude').value = lat;
      document.getElementById('longitude').value = lng;
    })
    .catch(error => {
      console.error('Error getting address:', error);
      document.getElementById('clinicAddress').value = `${lat}, ${lng}`;
      document.getElementById('latitude').value = lat;
      document.getElementById('longitude').value = lng;
    });
}

function searchLocation() {
  const query = document.getElementById('locationSearch').value.trim();
  if (!query) return;
  
  const searchResults = document.getElementById('searchResults');
  searchResults.innerHTML = '<div class="search-result-item">Searching...</div>';
  searchResults.classList.remove('hidden');
  
  fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&countrycodes=ph`)
    .then(response => response.json())
    .then(data => {
      if (data.length === 0) {
        searchResults.innerHTML = '<div class="search-result-item">No results found</div>';
        return;
      }
      
      searchResults.innerHTML = data.map(result => `
        <div class="search-result-item" onclick="selectSearchResult(${result.lat}, ${result.lon}, '${result.display_name.replace(/'/g, "\\'")}')">
          <div class="font-semibold">${result.display_name.split(',')[0]}</div>
          <div class="text-sm text-gray-500">${result.display_name}</div>
        </div>
      `).join('');
    })
    .catch(error => {
      console.error('Search error:', error);
      searchResults.innerHTML = '<div class="search-result-item text-danger">Error searching location</div>';
    });
}

function selectSearchResult(lat, lon, address) {
  map.setView([lat, lon], 16);
  setMarkerAndAddress(lat, lon);
  document.getElementById('locationSearch').value = address.split(',')[0];
  document.getElementById('searchResults').classList.add('hidden');
}

function getCurrentLocation() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      function(position) {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        map.setView([lat, lng], 16);
        setMarkerAndAddress(lat, lng);
        document.getElementById('locationSearch').value = 'Current Location';
      },
      function(error) {
        alert('Error getting current location: ' + error.message);
      }
    );
  } else {
    alert('Geolocation is not supported by this browser.');
  }
}

function previewClinicImage(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('clinicImagePreview').src = e.target.result;
    }
    reader.readAsDataURL(input.files[0]);
  }
}

function editClinic(data) {
  document.getElementById('clinicId').value = data.id;
  document.getElementById('clinicName').value = data.name;
  document.getElementById('clinicAddress').value = data.address;
  document.getElementById('clinicPhone').value = data.contact;
  document.getElementById('clinicEmail').value = data.email;
  document.getElementById('openingTime').value = data.opening_time;
  document.getElementById('closingTime').value = data.closing_time;
  document.getElementById('clinicStatus').value = data.status;
  
  if (data.image) {
    document.getElementById('clinicImagePreview').src = `{{ asset('storage/') }}/${data.image}`;
  }
  
  if (data.latitude && data.longitude) {
    document.getElementById('latitude').value = data.latitude;
    document.getElementById('longitude').value = data.longitude;
  }
  
  const operatingDays = JSON.parse(data.operation_days);
  document.querySelectorAll('input[name="operating_days[]"]').forEach(checkbox => {
    checkbox.checked = operatingDays.includes(checkbox.value);
  });
  
  document.getElementById('saveClinicButton').textContent = 'Update Clinic';
  document.getElementById('sidebarTitle').textContent = 'Edit Clinic';
  
  openSidebar('clinicSidebar');
  setTimeout(initMap, 500);
}

function filterClinics() {
  const input = document.getElementById('clinicSearchInput');
  const filter = input.value.toLowerCase();
  const table = document.getElementById('clinicsTable');
  const rows = table.getElementsByTagName('tr');

  for (let i = 1; i < rows.length; i++) {
    const row = rows[i];
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(filter) ? '' : 'none';
  }
}

// Sidebar functions
function openSidebar(sidebarId) {
  document.getElementById(sidebarId).classList.remove('hidden');
  document.body.style.overflow = 'hidden';
}

function closeSidebar(sidebarId) {
  document.getElementById(sidebarId).classList.add('hidden');
  document.body.style.overflow = 'auto';
  
  // Reset form when closing
  document.getElementById('clinicForm').reset();
  document.getElementById('clinicId').value = '';
  document.getElementById('clinicImagePreview').src = '{{ asset("assets/img/default-clinic.jpg") }}';
  document.getElementById('saveClinicButton').textContent = 'Save Clinic';
  document.getElementById('sidebarTitle').textContent = 'Add New Clinic';
}

async function deleteClinic(clinicId) {
  if (confirm('Are you sure you want to delete this clinic?')) {
    try {
      const response = await fetch(`/clinics/${clinicId}/delete`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
      });
      
      if (response.ok) {
        window.location.reload();
      }
    } catch (error) {
      console.error('Error deleting clinic:', error);
      alert('Failed to delete clinic');
    }
  }
}

function viewClinic(clinicId) {
  const row = document.querySelector(`[data-clinic-id="${clinicId}"]`);
  if (row) {
    const clinicData = JSON.parse(row.dataset.clinic);
    document.getElementById('clinicDetailsContent').innerHTML = `
      <div class="space-y-4">
        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl">
          <div class="w-20 h-20 rounded-xl overflow-hidden border-2 border-gray-200">
            <img src="${clinicData.image ? '/storage/' + clinicData.image : '/assets/img/default-clinic.jpg'}" class="w-full h-full object-cover" alt="${clinicData.name}">
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-900">${clinicData.name}</h3>
            <p class="text-sm text-gray-500">ID: #${clinicData.id}</p>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div class="p-3 bg-gray-50 rounded-lg">
            <p class="text-xs text-gray-500">Phone</p>
            <p class="text-sm font-medium text-gray-900">${clinicData.contact}</p>
          </div>
          <div class="p-3 bg-gray-50 rounded-lg">
            <p class="text-xs text-gray-500">Email</p>
            <p class="text-sm font-medium text-gray-900">${clinicData.email}</p>
          </div>
          <div class="p-3 bg-gray-50 rounded-lg">
            <p class="text-xs text-gray-500">Status</p>
            <p class="text-sm font-medium ${clinicData.status === 'active' ? 'text-green-600' : 'text-red-600'}">${clinicData.status}</p>
          </div>
          <div class="p-3 bg-gray-50 rounded-lg">
            <p class="text-xs text-gray-500">Address</p>
            <p class="text-sm font-medium text-gray-900">${clinicData.address || 'N/A'}</p>
          </div>
        </div>
      </div>
    `;
    openModal('clinicDetailsModal');
  }
}

// Form submission
document.getElementById('clinicForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  const clinicId = document.getElementById('clinicId').value;
  
  try {
    const url = clinicId ? `/clinics/${clinicId}` : '/clinics';
    const response = await fetch(url, {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    });

    if (response.ok) {
      closeSidebar('clinicSidebar');
      window.location.reload();
    } else {
      const data = await response.json();
      alert(data.message || 'Failed to save clinic');
    }
  } catch (error) {
    console.error('Error saving clinic:', error);
    alert('Failed to save clinic');
  }
});

// Initialize map when sidebar opens
document.addEventListener('DOMContentLoaded', function() {
  // Initialize map immediately if map container exists
  const mapContainer = document.getElementById('map');
  if (mapContainer && !map) {
    setTimeout(initMap, 100); // Small delay to ensure DOM is ready
  }
  
  const sidebar = document.getElementById('clinicSidebar');
  if (sidebar) {
    const observer = new MutationObserver(function(mutations) {
      mutations.forEach(function(mutation) {
        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
          if (!sidebar.classList.contains('hidden')) {
            setTimeout(initMap, 300);
          }
        }
      });
    });
    observer.observe(sidebar, { attributes: true });
  }
});

// Hide search results when clicking outside
document.addEventListener('click', function(event) {
  const searchResults = document.getElementById('searchResults');
  const locationSearch = document.getElementById('locationSearch');
  
  if (!searchResults.contains(event.target) && event.target !== locationSearch) {
    searchResults.classList.add('hidden');
  }
});
</script>
@endsection
