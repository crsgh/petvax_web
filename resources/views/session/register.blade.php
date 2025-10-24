@extends('layouts.user_type.guest')

@section('content')
<div class="register-container">
    <div class="register-card">
        <div class="register-header">
            <div class="logo-container">
                <img src="{{ asset('assets/img/logo.png') }}" alt="PetVax Logo" class="logo">
            </div>
            <h1 class="register-title">Register Your Clinic</h1>
            <p class="register-subtitle">Join our network of healthcare providers</p>
        </div>
        
        <div class="register-form">
            @if ($errors->any())
            <div class="error-alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="/clinics" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="form-group">
                    <label class="form-label">Clinic Name *</label>
                    <input type="text" 
                           class="form-input @error('clinic_name') error @enderror" 
                           name="clinic_name" 
                           value="{{ old('clinic_name') }}" 
                           required 
                           placeholder="Enter clinic name">
                    @error('clinic_name')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Contact Number *</label>
                        <input type="tel" 
                               class="form-input @error('clinic_phone') error @enderror" 
                               name="clinic_phone" 
                               value="{{ old('clinic_phone') }}" 
                               required 
                               placeholder="09123456789">
                        @error('clinic_phone')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email Address *</label>
                        <input type="email" 
                               class="form-input @error('clinic_email') error @enderror" 
                               name="clinic_email" 
                               value="{{ old('clinic_email') }}" 
                               required 
                               placeholder="clinic@example.com">
                        @error('clinic_email')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Address *</label>
                    <input type="text" 
                           class="form-input @error('clinic_address') error @enderror" 
                           name="clinic_address" 
                           value="{{ old('clinic_address') }}" 
                           required 
                           placeholder="Enter clinic address">
                    @error('clinic_address')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Opening Time *</label>
                        <input type="time" 
                               class="form-input @error('opening_time') error @enderror" 
                               name="opening_time" 
                               value="{{ old('opening_time') }}" 
                               required>
                        @error('opening_time')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Closing Time *</label>
                        <input type="time" 
                               class="form-input @error('closing_time') error @enderror" 
                               name="closing_time" 
                               value="{{ old('closing_time') }}" 
                               required>
                        @error('closing_time')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Operating Days *</label>
                    <div class="days-grid">
                        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                        <label class="day-checkbox">
                            <input type="checkbox" 
                                   name="operating_days[]" 
                                   value="{{ $day }}" 
                                   {{ (is_array(old('operating_days')) && in_array($day, old('operating_days'))) ? 'checked' : '' }}>
                            <span class="checkmark"></span>
                            {{ ucfirst($day) }}
                        </label>
                        @endforeach
                    </div>
                    @error('operating_days')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Hidden fields -->
                <input type="hidden" name="clinic_status" value="inactive">
                <input type="hidden" name="signup" value="true">
                <input type="hidden" name="latitude" value="14.5995">
                <input type="hidden" name="longitude" value="120.9842">

                <button type="submit" class="register-button">
                    Register Clinic
                </button>
            </form>
            
            <div class="register-footer">
                <a href="{{ route('login') }}" class="login-link">
                    Already have an account? Sign in here
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* Clean & Minimalist Register Styles */
.register-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    padding: 2rem 1rem;
    font-family: 'Poppins', sans-serif;
}

.register-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e5e7eb;
    width: 100%;
    max-width: 600px;
    overflow: hidden;
}

.register-header {
    text-align: center;
    padding: 2rem 2rem 1rem;
    border-bottom: 1px solid #f1f5f9;
}

.logo-container {
    margin-bottom: 1.5rem;
}

.logo {
    height: 60px;
    width: auto;
}

.register-title {
    font-size: 1.75rem;
    font-weight: 600;
    color: #111827;
    margin: 0 0 0.5rem;
}

.register-subtitle {
    color: #6b7280;
    font-size: 0.875rem;
    margin: 0;
}

.register-form {
    padding: 2rem;
}

.error-alert {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}

.error-alert ul {
    margin: 0;
    padding-left: 1.25rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
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

.form-input.error {
    border-color: #ef4444;
}

.days-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 0.75rem;
    margin-top: 0.5rem;
}

.day-checkbox {
    display: flex;
    align-items: center;
    padding: 0.5rem 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.875rem;
}

.day-checkbox:hover {
    background: #f9fafb;
    border-color: #3b82f6;
}

.day-checkbox input {
    margin-right: 0.5rem;
    width: 16px;
    height: 16px;
}

.register-button {
    width: 100%;
    background: #3b82f6;
    color: white;
    border: none;
    padding: 0.875rem 1rem;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-bottom: 1.5rem;
}

.register-button:hover {
    background: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.register-footer {
    text-align: center;
}

.login-link {
    color: #3b82f6;
    text-decoration: none;
    font-size: 0.875rem;
    transition: color 0.2s ease;
}

.login-link:hover {
    color: #2563eb;
}

.error-message {
    color: #ef4444;
    font-size: 0.75rem;
    margin-top: 0.25rem;
    display: block;
}

@media (max-width: 768px) {
    .register-container {
        padding: 1rem;
    }
    
    .register-card {
        max-width: 100%;
    }
    
    .register-header {
        padding: 1.5rem 1.5rem 1rem;
    }
    
    .register-form {
        padding: 1rem 1.5rem 1.5rem;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .days-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

@endsection
