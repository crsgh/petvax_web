@extends('layouts.user_type.guest')

@section('content')
<main class="main-content mt-0">
    <section class="min-h-screen bg-gray-50 flex items-center">
        <div class="container mx-auto px-4 h-full">
            <div class="flex content-center items-center justify-center h-full">
                <div class="w-full md:w-5/12 px-4">
                    <div class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded-lg bg-white border-0">
                        <div class="rounded-t mb-0 px-6 py-6">
                            <div class="flex justify-center mb-4">
                                <img src="{{ asset('assets/img/logo.png') }}" alt="PetVax Logo" class="w-25">
                            </div>
                            <div class="text-center mb-0">
                                <h4 class="text-2xl font-bold text-gray-700">PetVax Techies!</h4>
                                <p class="text-gray-500 text-sm">Sign in to continue to Clinic Management.</p>
                            </div>
                        </div>
                        <div class="flex-auto px-8 py-6 pt-0">
                            <form role="form" method="POST" action="/session">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                                        Email Address
                                    </label>
                                    <div class="relative">
                                        <input type="email"
                                               class="border-2 border-gray-300 px-4 py-3 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('email') border-red-400 @enderror"
                                               name="email"
                                               id="email"
                                               placeholder="Enter your email"
                                               value="{{ old('email') }}">
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    @error('email')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                                        Password
                                    </label>
                                    <div class="relative">
                                        <input type="password"
                                               class="border-2 border-gray-300 px-4 py-3 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('password') border-red-400 @enderror"
                                               name="password"
                                               id="password"
                                               placeholder="Enter your password">
                                        <button type="button" 
                                                class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                                onclick="togglePassword()">
                                            <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" id="eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    @error('password')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="mb-4">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox"
                                               class="form-checkbox rounded border-gray-300 text-blue-500 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                               name="remember"
                                               id="rememberMe">
                                        <span class="ml-2 text-sm text-gray-700">Remember me</span>
                                    </label>
                                </div>
                                
                                <div class="text-center">
                                    <button type="submit"
                                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold uppercase text-sm px-6 py-3 rounded-lg shadow hover:shadow-lg outline-none focus:outline-none w-full ease-linear transition-all duration-150">
                                        Sign In
                                    </button>
                                </div>
                            </form>
                            
                            <div class="text-center mt-4">
                                <a href="#" class="text-sm text-blue-500 hover:text-blue-700">
                                    Forgot your password?
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>';
    } else {
        passwordInput.type = 'password';
        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
    }
}
</script>
@endsection