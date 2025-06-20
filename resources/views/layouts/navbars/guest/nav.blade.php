{{-- <!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top w-full md:w-[90%] lg:w-[85%] mx-auto left-0 right-0 top-2 md:top-4 z-50 backdrop-blur-sm bg-white/90 shadow-md rounded-lg">
  <div class="container-fluid px-4">
    <a class="navbar-brand font-weight-bold text-primary hover:opacity-80 transition-all duration-300 text-base" href="{{ url('dashboard') }}">
      <img src="{{ asset('img/logo.png') }}" alt="PetVax" class="h-8 me-2 inline-block">
      PetVax Dashboard
    </a>
    <button class="navbar-toggler shadow-none border-0 hover:bg-gray-100 rounded-lg p-2" type="button" data-bs-toggle="collapse" data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navigation">
      <ul class="navbar-nav mx-auto gap-2 text-base">
        @if (auth()->user())
            <li class="nav-item">
            <a class="nav-link rounded-lg px-3 py-2 hover:bg-gray-100 transition-all duration-200 {{ Request::is('dashboard') ? 'bg-primary text-white hover:bg-primary/90' : '' }}" href="{{ url('dashboard') }}">
                <i class="fa fa-chart-pie me-2"></i>
                Dashboard
            </a>
            </li>
            <li class="nav-item">
            <a class="nav-link rounded-lg px-3 py-2 hover:bg-gray-100 transition-all duration-200 {{ Request::is('profile') ? 'bg-primary text-white hover:bg-primary/90' : '' }}" href="{{ url('profile') }}">
                <i class="fa fa-user me-2"></i>
                Profile
            </a>
            </li>
        @endif
      </ul>
      <div class="flex items-center gap-3">
        @if (!auth()->user())
          <a href="{{ url('register') }}" class="btn btn-outline-primary rounded-lg px-4 py-2 text-sm font-medium hover:shadow-md transition-all duration-300 flex items-center gap-2">
            <i class="fas fa-user-plus"></i>
            Sign Up
          </a>
          <a href="{{ url('login') }}" class="btn btn-primary rounded-lg px-4 py-2 text-sm font-medium hover:shadow-md transition-all duration-300 flex items-center gap-2">
            <i class="fas fa-sign-in-alt"></i>
            Sign In
          </a>
        @endif
        <a href="" target="_blank" class="btn btn-primary rounded-lg px-4 py-2 text-sm font-medium hover:shadow-md transition-all duration-300 flex items-center gap-2">
          <i class="fas fa-download"></i>
          Download App
        </a>
        @if (auth()->user())
        <div class="relative group">
          <button class="w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 transition-all duration-200 flex items-center justify-center">
            <img src="{{ auth()->user()->avatar ?? asset('img/default-avatar.png') }}" class="w-full h-full rounded-full object-cover" alt="User avatar">
          </button>
          <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 border border-gray-100">
            <a href="{{ url('profile') }}" class="block px-4 py-2 text-sm hover:bg-gray-50 transition-colors duration-200">My Profile</a>
            <a href="{{ url('settings') }}" class="block px-4 py-2 text-sm hover:bg-gray-50 transition-colors duration-200">Settings</a>
            <hr class="my-1 border-gray-100">
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50 transition-colors duration-200">Logout</button>
            </form>
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>
</nav>
<!-- End Navbar --> --}}
