@extends('layouts.user_type.guest')

@section('content')
<style>
/* Custom styles matching the preview design */
.min-vh-100 {
    min-height: 100vh;
}

.bg-gradient-custom {
    background: linear-gradient(135deg, #dbeafe 0%, #ffffff 50%, #d1fae5 100%);
}

.header-section {
    background: linear-gradient(135deg, #2563eb 0%, #059669 100%);
    color: white;
    padding: 4rem 0;
    position: relative;
}

.header-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.2);
}

.form-section {
    margin-top: -2rem;
    position: relative;
    z-index: 10;
    padding: 2rem 0;
}

.card-custom {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    border: none;
    border-radius: 1rem;
}

.card-header-custom {
    background: white;
    border-bottom: 1px solid #e5e7eb;
    border-radius: 1rem 1rem 0 0 !important;
    padding: 2rem;
}

.card-body-custom {
    padding: 2rem;
}

.form-label-custom {
    font-size: 1rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-control-custom {
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: all 0.2s ease;
}

.form-control-custom:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    outline: none;
}

.form-control-custom.is-invalid {
    border-color: #ef4444;
}

.btn-gradient-custom {
    background: linear-gradient(135deg, #2563eb 0%, #059669 100%);
    border: none;
    color: white;
    padding: 1rem 2rem;
    font-size: 1.125rem;
    font-weight: 600;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
    width: 100%;
}

.btn-gradient-custom:hover {
    background: linear-gradient(135deg, #1d4ed8 0%, #047857 100%);
    transform: translateY(-1px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    color: white;
}

.btn-gradient-custom:disabled {
    opacity: 0.7;
    transform: none;
}

.image-preview-container {
    position: relative;
    display: inline-block;
    margin: 1rem 0;
}

.image-preview {
    width: 128px;
    height: 128px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #e5e7eb;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.image-upload-overlay {
    position: absolute;
    bottom: 0;
    right: 0;
    background: #2563eb;
    color: white;
    padding: 0.5rem;
    border-radius: 50%;
    cursor: pointer;
    transition: background-color 0.2s;
    border: 2px solid white;
}

.image-upload-overlay:hover {
    background: #1d4ed8;
}

.search-container {
    position: relative;
    margin-bottom: 1rem;
}

.search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    max-height: 200px;
    overflow-y: auto;
}

.search-result-item {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f3f4f6;
    cursor: pointer;
    transition: background-color 0.2s;
}

.search-result-item:hover {
    background-color: #f9fafb;
}

.search-result-item:last-child {
    border-bottom: none;
}

.map-container {
    height: 320px;
    width: 100%;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
}

.map-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 320px;
    background: #f8f9fa;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    color: #6b7280;
}

.spinner {
    border: 2px solid #f3f4f6;
    border-top: 2px solid #2563eb;
    border-radius: 50%;
    width: 2rem;
    height: 2rem;
    animation: spin 1s linear infinite;
    margin-bottom: 0.5rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.day-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 0.75rem;
    margin-top: 0.5rem;
}

.day-checkbox-container {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    transition: all 0.2s;
    cursor: pointer;
}

.day-checkbox-container:hover {
    background-color: #f9fafb;
    border-color: #2563eb;
}

.day-checkbox-container input:checked + label {
    color: #2563eb;
    font-weight: 600;
}

.tag-container {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.75rem;
}

.tag-badge {
    background-color: #e5e7eb;
    color: #374151;
    padding: 0.375rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.tag-remove {
    cursor: pointer;
    color: #6b7280;
    font-weight: bold;
    width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s;
}

.tag-remove:hover {
    color: #dc2626;
    background-color: rgba(220, 38, 38, 0.1);
}

.input-group-custom {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
}

.input-group-custom input {
    flex: 1;
}

.btn-outline-custom {
    border: 1px solid #d1d5db;
    background: white;
    color: #374151;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    transition: all 0.2s;
}

.btn-outline-custom:hover {
    border-color: #2563eb;
    color: #2563eb;
    background-color: #f8fafc;
}

.section-spacing {
    margin-bottom: 2rem;
}

.text-error {
    color: #ef4444;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.text-helper {
    color: #6b7280;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.alert-custom {
    background-color: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
    padding: 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
}

.alert-custom ul {
    margin: 0;
    padding-left: 1.25rem;
}

.alert-custom li {
    margin-bottom: 0.25rem;
}

.alert-custom li:last-child {
    margin-bottom: 0;
}
</style>

<div class="min-vh-100 bg-gradient-custom">
    <!-- Header Section -->
    <div class="header-section">
        <div class="header-overlay"></div>
        <div class="container position-relative">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <i class="fas fa-hospital-alt" style="font-size: 4rem; margin-bottom: 1rem;"></i>
                    <h1 class="display-4 fw-bold mb-4">Register Your Clinic</h1>
                    <p class="fs-5 text-light opacity-75">Join our network of healthcare providers and help us serve the community better</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="container form-section">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10">
                <div class="card card-custom">
                    <div class="card-header card-header-custom text-center">
                        <h2 class="fw-bold text-dark mb-2">Clinic Registration Form</h2>
                        <p class="text-muted mb-0">Please fill out all required information to register your clinic</p>
                    </div>

                    <div class="card-body card-body-custom">
                        @if ($errors->any())
                        <div class="alert-custom">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form id="clinicForm" action="/clinics" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                            @csrf

                            <!-- Clinic Image -->
                            <div class="text-center section-spacing">
                                <label class="form-label-custom justify-content-center">Clinic Image</label>
                                <div class="image-preview-container">
                                    <img id="imagePreview" 
                                         src="{{ asset('assets/img/dog.png') }}" 
                                         alt="Clinic Preview" 
                                         class="image-preview">
                                    <label class="image-upload-overlay">
                                        <i class="fas fa-upload"></i>
                                        <input type="file" 
                                               class="d-none @error('clinic_image') is-invalid @enderror" 
                                               id="clinicImage" 
                                               name="clinic_image" 
                                               accept="image/*" 
                                               onchange="previewImage(this)">
                                    </label>
                                </div>
                                @error('clinic_image')
                                <div class="text-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Basic Information -->
                            <div class="row section-spacing">
                                <div class="col-md-6">
                                    <label for="clinicName" class="form-label-custom">
                                        <i class="fas fa-hospital"></i>Clinic Name *
                                    </label>
                                    <input type="text" 
                                           class="form-control form-control-custom @error('clinic_name') is-invalid @enderror" 
                                           id="clinicName" 
                                           name="clinic_name" 
                                           value="{{ old('clinic_name') }}" 
                                           required 
                                           maxlength="255"
                                           placeholder="Enter clinic name">
                                    @error('clinic_name')
                                    <div class="text-error">{{ $message }}</div>
                                    @enderror
                                </div>
                                <input type="hidden" 
                                       id="clinicStatus" 
                                       name="clinic_status" 
                                       value="inactive">
                                <input type="hidden" 
                                       id="signup" 
                                       name="signup" 
                                       value="true">
                            </div>

                            <!-- Contact Information -->
                            <div class="row section-spacing">
                                <div class="col-md-6">
                                    <label for="clinicPhone" class="form-label-custom">
                                        <i class="fas fa-phone"></i>Contact Number *
                                    </label>
                                    <input type="tel" 
                                           class="form-control form-control-custom @error('clinic_phone') is-invalid @enderror" 
                                           id="clinicPhone" 
                                           name="clinic_phone" 
                                           value="{{ old('clinic_phone') }}" 
                                           required 
                                           maxlength="11"
                                           placeholder="09123456789">
                                    @error('clinic_phone')
                                    <div class="text-error">{{ $message }}</div>
                                    @enderror
                                    <div class="text-helper">Format: 09XXXXXXXXX (11 digits)</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="clinicEmail" class="form-label-custom">
                                        <i class="fas fa-envelope"></i>Email Address *
                                    </label>
                                    <input type="email" 
                                           class="form-control form-control-custom @error('clinic_email') is-invalid @enderror" 
                                           id="clinicEmail" 
                                           name="clinic_email" 
                                           value="{{ old('clinic_email') }}" 
                                           required 
                                           maxlength="255"
                                           placeholder="clinic@example.com">
                                    @error('clinic_email')
                                    <div class="text-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Location -->
                            <div class="section-spacing">
                                <label class="form-label-custom">
                                    <i class="fas fa-map-marker-alt"></i>Location *
                                </label>
                                
                                <!-- Search Bar -->
                                <div class="input-group-custom">
                                    <div class="search-container" style="flex: 1;">
                                        <input type="text" 
                                               class="form-control form-control-custom" 
                                               id="locationSearch" 
                                               placeholder="Search for a location..."
                                               onkeypress="handleSearchKeyPress(event)">
                                        <div id="searchResults" class="search-results" style="display: none;"></div>
                                    </div>
                                    <button class="btn btn-outline-custom" type="button" onclick="searchLocation()">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <button class="btn btn-outline-custom" type="button" onclick="getCurrentLocation()" title="Use current location">
                                        <i class="fas fa-location-arrow"></i>
                                    </button>
                                </div>

                                <!-- Map -->
                                <div id="mapLoading" class="map-loading">
                                    <div class="text-center">
                                        <div class="spinner"></div>
                                        <p>Loading map...</p>
                                    </div>
                                </div>
                                <div id="map" class="map-container" style="display: none;"></div>

                                <!-- Address Display -->
                                <input type="text" 
                                       class="form-control form-control-custom @error('clinic_address') is-invalid @enderror" 
                                       id="clinicAddress" 
                                       name="clinic_address" 
                                       value="{{ old('clinic_address') }}" 
                                       required 
                                       maxlength="255" 
                                       readonly
                                       placeholder="Address will appear here when you select a location"
                                       style="background-color: #f9fafb;">
                                <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}" required>
                                <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}" required>
                                
                                @error('clinic_address')
                                <div class="text-error">{{ $message }}</div>
                                @enderror
                                
                                <div class="text-helper">Click on the map to set the exact location of your clinic</div>
                            </div>

                            <!-- Operating Schedule -->
                            <div class="section-spacing">
                                <label class="form-label-custom">
                                    <i class="fas fa-clock"></i>Operating Schedule *
                                </label>
                                
                                <!-- Operating Days -->
                                <div style="margin-bottom: 1.5rem;">
                                    <label class="form-label" style="font-weight: 500; color: #374151;">Operating Days</label>
                                    <div class="day-grid">
                                        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                        <div class="day-checkbox-container">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="{{ $day }}" 
                                                   name="operating_days[]" 
                                                   value="{{ $day }}" 
                                                   {{ (is_array(old('operating_days')) && in_array($day, old('operating_days'))) ? 'checked' : '' }}>
                                            <label class="form-check-label text-capitalize" for="{{ $day }}">
                                                {{ ucfirst($day) }}
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                    @error('operating_days')
                                    <div class="text-error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Operating Hours -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="openingTime" class="form-label" style="font-weight: 500; color: #374151;">Opening Time</label>
                                        <input type="time" 
                                               class="form-control form-control-custom @error('opening_time') is-invalid @enderror" 
                                               id="openingTime" 
                                               name="opening_time" 
                                               value="{{ old('opening_time') }}" 
                                               required>
                                        @error('opening_time')
                                        <div class="text-error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="closingTime" class="form-label" style="font-weight: 500; color: #374151;">Closing Time</label>
                                        <input type="time" 
                                               class="form-control form-control-custom @error('closing_time') is-invalid @enderror" 
                                               id="closingTime" 
                                               name="closing_time" 
                                               value="{{ old('closing_time') }}" 
                                               required>
                                        @error('closing_time')
                                        <div class="text-error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Tags -->
                            <div class="section-spacing">
                                <label class="form-label-custom">Tags</label>
                                <div class="input-group-custom">
                                    <input type="text" 
                                           class="form-control form-control-custom" 
                                           id="tagInput" 
                                           placeholder="Add tags (e.g., pediatrics, emergency, 24/7)"
                                           onkeypress="handleTagKeyPress(event)">
                                    <button class="btn btn-outline-custom" type="button" onclick="addTag()">
                                        <i class="fas fa-plus"></i> Add
                                    </button>
                                </div>
                                <div id="tagContainer" class="tag-container"></div>
                                <input type="hidden" id="clinicTags" name="tags" value="{{ old('tags') }}">
                                @error('tags')
                                <div class="text-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div style="padding-top: 1.5rem;">
                                <button type="submit" class="btn btn-gradient-custom" id="submitBtn">
                                    <i class="fas fa-save me-2"></i>Register Clinic
                                </button>
                            </div>

                            <!-- Already have an account -->
                            <div class="text-center mt-4">
                                <p class="mb-0">
                                    Already have an account? 
                                    <a href="{{ route('login') }}" class="text-primary fw-bold">Sign in</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Leaflet CSS and JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
      crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
        crossorigin=""></script>

<script>
let map, marker;
let tags = [];

// Initialize map with Google Maps tiles
function initMap() {
    const defaultLocation = [14.5995, 120.9842]; // Manila, Philippines
    
    // Hide loading and show map
    document.getElementById('mapLoading').style.display = 'none';
    document.getElementById('map').style.display = 'block';
    
    // Initialize map
    map = L.map('map').setView(defaultLocation, 13);
    
    // Add Google Maps tile layer
    L.tileLayer('https://mt1.google.com/vt/lyrs=r&x={x}&y={y}&z={z}', {
        attribution: '© Google Maps',
        maxZoom: 20
    }).addTo(map);
    
    // Add marker
    marker = L.marker(defaultLocation, {
        draggable: true
    }).addTo(map);
    
    // Set initial location
    document.getElementById('latitude').value = defaultLocation[0];
    document.getElementById('longitude').value = defaultLocation[1];
    reverseGeocode(defaultLocation[0], defaultLocation[1]);
    
    // Map click event
    map.on('click', function(e) {
        const { lat, lng } = e.latlng;
        marker.setLatLng([lat, lng]);
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
        reverseGeocode(lat, lng);
    });
    
    // Marker drag event
    marker.on('dragend', function(e) {
        const { lat, lng } = e.target.getLatLng();
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
        reverseGeocode(lat, lng);
    });
    
    // Force map resize
    setTimeout(() => {
        map.invalidateSize();
    }, 100);
}

// Reverse geocoding using Nominatim
async function reverseGeocode(lat, lng) {
    try {
        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
        const data = await response.json();
        document.getElementById('clinicAddress').value = data.display_name;
    } catch (error) {
        console.error('Reverse geocoding error:', error);
        document.getElementById('clinicAddress').value = `${lat}, ${lng}`;
    }
}

// Search location using Nominatim
async function searchLocation() {
    const query = document.getElementById('locationSearch').value.trim();
    if (!query) return;
    
    const searchResults = document.getElementById('searchResults');
    searchResults.innerHTML = '<div class="search-result-item">Searching...</div>';
    searchResults.style.display = 'block';
    
    try {
        const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&countrycodes=ph`);
        const data = await response.json();
        displaySearchResults(data);
    } catch (error) {
        console.error('Search error:', error);
        searchResults.innerHTML = '<div class="search-result-item text-danger">Error searching location</div>';
    }
}

// Display search results
function displaySearchResults(results) {
    const searchResults = document.getElementById('searchResults');
    
    if (results.length === 0) {
        searchResults.innerHTML = '<div class="search-result-item">No results found</div>';
        return;
    }
    
    searchResults.innerHTML = results.map(result => `
        <div class="search-result-item" onclick="selectSearchResult(${result.lat}, ${result.lon}, '${result.display_name.replace(/'/g, "\\'")}')">
            <div style="font-weight: 600;">${result.display_name.split(',')[0]}</div>
            <small style="color: #6b7280;">${result.display_name}</small>
        </div>
    `).join('');
}

// Select search result
function selectSearchResult(lat, lon, address) {
    map.setView([lat, lon], 16);
    marker.setLatLng([lat, lon]);
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lon;
    document.getElementById('clinicAddress').value = address;
    document.getElementById('locationSearch').value = address.split(',')[0];
    document.getElementById('searchResults').style.display = 'none';
}

// Handle search key press
function handleSearchKeyPress(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        searchLocation();
    }
}

// Get current location
function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                map.setView([lat, lng], 16);
                marker.setLatLng([lat, lng]);
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
                reverseGeocode(lat, lng);
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

// Hide search results when clicking outside
document.addEventListener('click', function(event) {
    const searchResults = document.getElementById('searchResults');
    const locationSearch = document.getElementById('locationSearch');
    
    if (!searchResults.contains(event.target) && event.target !== locationSearch) {
        searchResults.style.display = 'none';
    }
});

// Image preview
function previewImage(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.size > 5 * 1024 * 1024) {
            alert('Image size must be less than 5MB');
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

// Tag management
function addTag() {
    const tagInput = document.getElementById('tagInput');
    const tagValue = tagInput.value.trim();
    if (tagValue && !tags.includes(tagValue)) {
        tags.push(tagValue);
        updateTagsDisplay();
        tagInput.value = '';
    }
}

function removeTag(tagToRemove) {
    tags = tags.filter(tag => tag !== tagToRemove);
    updateTagsDisplay();
}

function updateTagsDisplay() {
    const tagContainer = document.getElementById('tagContainer');
    const hiddenInput = document.getElementById('clinicTags');
    
    tagContainer.innerHTML = tags.map(tag => `
        <span class="tag-badge">
            ${tag}
            <span class="tag-remove" onclick="removeTag('${tag}')">&times;</span>
        </span>
    `).join('');
    
    hiddenInput.value = tags.join(',');
}

function handleTagKeyPress(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        addTag();
    }
}

// Form validation
function validateForm() {
    const errors = [];
    
    // Basic validation
    const clinicName = document.getElementById('clinicName').value.trim();
    if (!clinicName) {
        errors.push('Clinic name is required');
    } else if (clinicName.length < 2) {
        errors.push('Clinic name must be at least 2 characters');
    }
    
    // Phone validation
    const phone = document.getElementById('clinicPhone').value.trim();
    if (!phone) {
        errors.push('Phone number is required');
    } else if (!/^09\d{9}$/.test(phone.replace(/\s+/g, ''))) {
        errors.push('Phone number must be in format 09XXXXXXXXX');
    }
    
    // Email validation
    const email = document.getElementById('clinicEmail').value.trim();
    if (!email) {
        errors.push('Email is required');
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        errors.push('Please enter a valid email address');
    }
    
    // Time validation
    const openingTime = document.getElementById('openingTime').value;
    const closingTime = document.getElementById('closingTime').value;
    if (!openingTime) {
        errors.push('Opening time is required');
    }
    if (!closingTime) {
        errors.push('Closing time is required');
    }
    if (openingTime && closingTime && openingTime >= closingTime) {
        errors.push('Closing time must be after opening time');
    }
    
    // Operating days validation
    const operatingDays = document.querySelectorAll('input[name="operating_days[]"]:checked');
    if (operatingDays.length === 0) {
        errors.push('Please select at least one operating day');
    }
    
    // Location validation
    const latitude = document.getElementById('latitude').value;
    const longitude = document.getElementById('longitude').value;
    if (!latitude || !longitude) {
        errors.push('Please select a location on the map');
    }
    
    if (errors.length > 0) {
        alert('Please fix the following errors:\n\n' + errors.join('\n'));
        return false;
    }
    
    // Show loading state
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Registering Clinic...';
    submitBtn.disabled = true;
    
    return true;
}

// Initialize map when page loads
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(initMap, 500);
    
    // Initialize tags from old input if available
    const oldTags = document.getElementById('clinicTags').value;
    if (oldTags) {
        tags = oldTags.split(',').filter(tag => tag.trim());
        updateTagsDisplay();
    }
});
</script>
@endsection