@extends('layouts.user_type.guest')

@section('content')
<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <div class="logo-container">
                <img src="{{ asset('assets/img/logo.png') }}" alt="PetVax Logo" class="logo">
            </div>
            <h1 class="login-title">Welcome Back</h1>
            <p class="login-subtitle">Sign in to your account</p>
        </div>
        <div class="login-form">
            <form role="form" method="POST" action="/session">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email"
                           class="form-input @error('email') error @enderror"
                           name="email"
                           id="email"
                           placeholder="Enter your email"
                           value="{{ old('email') }}">
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="password-input">
                        <input type="password"
                               class="form-input @error('password') error @enderror"
                               name="password"
                               id="password"
                               placeholder="Enter your password">
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <svg class="eye-icon" id="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" id="rememberMe">
                        <span class="checkmark"></span>
                        Remember me
                    </label>
                </div>
                
                <button type="submit" class="login-button">
                    Sign In
                </button>
            </form>
            
            @if(config('app.debug'))
            <div class="test-accounts">
                <p class="test-accounts-label">Quick Login (Test Accounts)</p>
                <select class="quick-login-dropdown" onchange="if(this.value) { const [e,p]=this.value.split('|'); fillLogin(e,p); this.selectedIndex=0; }">
                    <option value="">— Select an account —</option>
                    <option value="superadmin@petvax.test|password">Super Admin — superadmin@petvax.test</option>
                    <option value="admin@petvax.test|password">Admin — admin@petvax.test</option>
                    <option value="staff@petvax.test|password">Staff — staff@petvax.test</option>
                    <option value="vet@petvax.test|password">Veterinarian — vet@petvax.test</option>
                    <option value="client@petvax.test|password">Pet Owner — client@petvax.test</option>
                </select>
            </div>
            @endif

            <div class="login-footer">
                <a href="/register" class="register-link">
                    Don't have an account? Register here
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* Clean & Minimalist Login Styles */
.test-accounts {
    margin-bottom: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid #e5e7eb;
}

.test-accounts-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.75rem;
    text-align: center;
}

.quick-login-dropdown {
    width: 100%;
    padding: 0.75rem 1rem;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.8125rem;
    color: #374151;
    cursor: pointer;
    transition: all 0.2s ease;
    appearance: auto;
}

.quick-login-dropdown:hover {
    border-color: #93c5fd;
    background: #eff6ff;
}

.quick-login-dropdown:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
.login-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    padding: 2rem 1rem;
    font-family: 'Poppins', sans-serif;
}

.login-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e5e7eb;
    width: 100%;
    max-width: 400px;
    overflow: hidden;
}

.login-header {
    text-align: center;
    padding: 2rem 2rem 1rem;
}

.logo-container {
    margin-bottom: 1.5rem;
}

.logo {
    height: 60px;
    width: auto;
}

.login-title {
    font-size: 1.75rem;
    font-weight: 600;
    color: #111827;
    margin: 0 0 0.5rem;
}

.login-subtitle {
    color: #6b7280;
    font-size: 0.875rem;
    margin: 0;
}

.login-form {
    padding: 1rem 2rem 2rem;
}

.form-group {
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

.form-input.error {
    border-color: #ef4444;
}

.password-input {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: #9ca3af;
    padding: 0;
}

.eye-icon {
    width: 18px;
    height: 18px;
    stroke-width: 2;
}

.checkbox-label {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-size: 0.875rem;
    color: #374151;
}

.checkbox-label input {
    margin-right: 0.5rem;
    width: 16px;
    height: 16px;
}

.login-button {
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

.login-button:hover {
    background: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.login-footer {
    text-align: center;
}

.register-link {
    color: #3b82f6;
    text-decoration: none;
    font-size: 0.875rem;
    transition: color 0.2s ease;
}

.register-link:hover {
    color: #2563eb;
}

.error-message {
    color: #ef4444;
    font-size: 0.75rem;
    margin-top: 0.25rem;
    display: block;
}

@media (max-width: 480px) {
    .login-container {
        padding: 1rem;
    }
    
    .login-card {
        max-width: 100%;
    }
    
    .login-header {
        padding: 1.5rem 1.5rem 1rem;
    }
    
    .login-form {
        padding: 1rem 1.5rem 1.5rem;
    }
}
</style>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
    } else {
        passwordInput.type = 'password';
        eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
    }
}

function fillLogin(email, password) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = password;
    document.querySelector('.login-button').focus();
}
</script>
@endsection