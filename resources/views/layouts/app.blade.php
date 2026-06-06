<!DOCTYPE html>

@if (\Request::is('rtl'))
  <html dir="rtl" lang="ar">
@else
  <html lang="en" >
@endif

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @if (env('IS_DEMO'))
      <x-demo-metas></x-demo-metas>
  @endif

  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>
    {{ ucfirst(Request::path()) }} | PetVax
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <!-- Alpine.js -->
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <!-- Original CSS (for compatibility) -->
  <link id="pagestyle" href="../assets/css/soft-ui-dashboard.css?v=1.0.3" rel="stylesheet" />
  <!-- Design System CSS (loaded last to override) -->
  <link href="{{ asset('css/design-system.css') }}" rel="stylesheet" />
  
  <!-- Settings-based CSS Variables -->
  <style>
    @php
      use App\Helpers\SettingsHelper;
      echo SettingsHelper::generateCss();
    @endphp
    
    /* Alert Stacking Styles */
    .alert-container {
      position: fixed;
      bottom: 1rem;
      right: 1rem;
      z-index: 9999;
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
      max-width: 400px;
    }
    
    .alert-item {
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .alert-item .btn-close {
      font-size: 0.75rem;
      opacity: 0.8;
    }
    
    .alert-item .btn-close:hover {
      opacity: 1;
    }
  </style>
</head>

<body class="g-sidenav-show  bg-gray-100 {{ (\Request::is('rtl') ? 'rtl' : (Request::is('virtual-reality') ? 'virtual-reality' : '')) }} ">
  @auth
    @yield('auth')
  @endauth
  @guest
    @yield('guest')
  @endguest

  <!-- Alert Container -->
  <div class="alert-container">
    <!-- Success Alert -->
    @if(session()->has('success'))
      <div x-data="{ show: true }"
           x-init="setTimeout(() => show = false, 4000)"
           x-show="show"
           x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="opacity-0 transform translate-y-2"
           x-transition:enter-end="opacity-100 transform translate-y-0"
           x-transition:leave="transition ease-in duration-300"
           x-transition:leave-start="opacity-100 transform translate-y-0"
           x-transition:leave-end="opacity-0 transform translate-y-2"
           class="alert-item bg-success rounded-3 shadow-lg text-white py-3 px-4">
        <div class="d-flex align-items-center">
          <x-ui.icon name="check-circle" class="w-4 h-4 me-2" />
          <p class="m-0 font-weight-bold flex-grow-1">{{ session('success') }}</p>
          <button @click="show = false" class="btn-close btn-close-white ms-3" aria-label="Close"></button>
        </div>
      </div>
    @endif

    <!-- Error Alert -->
    @if(session()->has('error'))
      <div x-data="{ show: true }"
           x-init="setTimeout(() => show = false, 6000)"
           x-show="show"
           x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="opacity-0 transform translate-y-2"
           x-transition:enter-end="opacity-100 transform translate-y-0"
           x-transition:leave="transition ease-in duration-300"
           x-transition:leave-start="opacity-100 transform translate-y-0"
           x-transition:leave-end="opacity-0 transform translate-y-2"
           class="alert-item bg-danger rounded-3 shadow-lg text-white py-3 px-4">
        <div class="d-flex align-items-center">
          <x-ui.icon name="x-circle" class="w-4 h-4 me-2" />
          <p class="m-0 font-weight-bold flex-grow-1">{{ session('error') }}</p>
          <button @click="show = false" class="btn-close btn-close-white ms-3" aria-label="Close"></button>
        </div>
      </div>
    @endif

    <!-- Validation Errors Alert -->
    @if($errors->any())
      <div x-data="{ show: true }"
           x-init="setTimeout(() => show = false, 8000)"
           x-show="show"
           x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="opacity-0 transform translate-y-2"
           x-transition:enter-end="opacity-100 transform translate-y-0"
           x-transition:leave="transition ease-in duration-300"
           x-transition:leave-start="opacity-100 transform translate-y-0"
           x-transition:leave-end="opacity-0 transform translate-y-2"
           class="alert-item bg-warning rounded-3 shadow-lg text-dark py-3 px-4">
        <div class="d-flex align-items-start">
          <x-ui.icon name="alert" class="w-4 h-4 me-2 mt-1" />
          <div class="flex-grow-1">
            <p class="m-0 font-weight-bold mb-1">Validation Errors:</p>
            <ul class="m-0 ps-3">
              @foreach($errors->all() as $error)
                <li class="small">{{ $error }}</li>
              @endforeach
            </ul>
          </div>
          <button @click="show = false" class="btn-close ms-3" aria-label="Close"></button>
        </div>
      </div>
    @endif
  </div>
    <!--   Core JS Files   -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/fullcalendar.min.js"></script>
  <script src="../assets/js/plugins/chartjs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
  {{-- @stack('rtl') --}}
  @stack('dashboard')
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>

  {{-- <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/soft-ui-dashboard.min.js?v=1.0.3"></script> --}}
</body>

</html>
