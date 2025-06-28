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
                                    <input type="email" 
                                           class="border-2 px-3 py-3 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200"
                                           name="email"
                                           id="email"
                                           placeholder="Enter your email"
                                           value="">
                                    @error('email')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                                        Password
                                    </label>
                                    <input type="password"
                                           class="border-2 px-3 py-3 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200"
                                           name="password"
                                           id="password"
                                           placeholder="Enter your password"
                                           value="">
                                    @error('password')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" 
                                               class="form-checkbox rounded border-gray-300 text-blue-500 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                               id="rememberMe">
                                        <span class="ml-2 text-sm text-gray-700">Remember me</span>
                                    </label>
                                </div>

                                <div class="text-center">
                                    <button type="submit" 
                                            class="bg-blue-500 text-white active:bg-blue-600 font-bold uppercase text-sm px-6 py-3 rounded-lg shadow hover:shadow-lg outline-none focus:outline-none mr-1 mb-1 w-full ease-linear transition-all duration-150">
                                        Sign In
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
