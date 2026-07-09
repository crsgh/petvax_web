<!DOCTYPE html>

@if (\Request::is('rtl'))
  <html dir="rtl" lang="ar">
@else
  <html lang="en" >
@endif

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  @if (env('IS_DEMO'))
      <x-demo-metas></x-demo-metas>
  @endif

  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>
    {{ ucfirst(Request::path()) }} | PetVax
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  {{-- <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script> --}}
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- CSS Files -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link id="pagestyle" href="../assets/css/soft-ui-dashboard.css?v=1.0.3" rel="stylesheet" />
</head>

<body class="g-sidenav-show  bg-gray-100 {{ (\Request::is('rtl') ? 'rtl' : (Request::is('virtual-reality') ? 'virtual-reality' : '')) }} ">
  @auth
    @yield('auth')
  @endauth
  @guest
    @yield('guest')
  @endguest

  @if(session()->has('success'))
    <div x-data="{ show: true}"
        x-init="setTimeout(() => show = false, 4000)"
        x-show="show"
        class="position-fixed bottom-3 end-3 z-index-3 bg-success rounded-3 shadow-sm text-white py-3 px-4">
      <div class="d-flex align-items-center">
        <i class="fas fa-check-circle me-2"></i>
        <p class="m-0 font-weight-bold">{{ session('success')}}</p>
      </div>
    </div>
  @endif

  @if(session()->has('error'))
    <div x-data="{ show: true}"
        x-init="setTimeout(() => show = false, 5000)"
        x-show="show"
        class="position-fixed bottom-3 end-3 z-index-3 bg-danger rounded-3 shadow-sm text-white py-3 px-4">
      <div class="d-flex align-items-center">
        <i class="fas fa-exclamation-circle me-2"></i>
        <p class="m-0 font-weight-bold">{{ session('error')}}</p>
      </div>
    </div>
  @endif

  @if(session()->has('warning'))
    <div x-data="{ show: true}"
        x-init="setTimeout(() => show = false, 6000)"
        x-show="show"
        class="position-fixed bottom-3 end-3 z-index-3 bg-warning rounded-3 shadow-sm text-dark py-3 px-4">
      <div class="d-flex align-items-center">
        <i class="fas fa-info-circle me-2"></i>
        <p class="m-0 font-weight-bold">{{ session('warning')}}</p>
      </div>
    </div>
  @endif
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

  <!-- Global responsive tables: stack rows into labeled cards on small screens -->
  <style>
    @media (max-width: 991.98px) {
      /* Stop the horizontal scrollbar; let the table flow vertically */
      .table-responsive { overflow-x: visible !important; }

      table.table { border: 0; }
      table.table thead { display: none; }
      table.table tbody,
      table.table tr,
      table.table td { display: block; width: 100%; }

      table.table tr {
        margin: 0 0.75rem 1rem;
        padding: 0.5rem 0.9rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
      }

      table.table td {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        text-align: right !important;
        padding: 0.55rem 0;
        border: 0;
        border-bottom: 1px solid #f3f4f6;
        white-space: normal;
        min-width: 0;
      }
      table.table tr td:last-child { border-bottom: 0; }

      /* Pull the column header text in as a bold label on the left */
      table.table td::before {
        content: attr(data-label);
        flex: 0 0 42%;
        text-align: left;
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        color: #6b7280;
      }
      /* Rows without a label (e.g. action buttons) get full width */
      table.table td[data-label=""]::before,
      table.table td:not([data-label])::before { content: ""; flex: 0; }

      /* Keep inner flex cells (avatars/buttons) from overflowing */
      table.table td > * { min-width: 0; }
    }
  </style>
  <script>
    // Auto-assign each cell a data-label from its column header so the
    // responsive card layout above can show "Header: value" on mobile.
    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('table.table').forEach(function (table) {
        var headers = Array.prototype.map.call(
          table.querySelectorAll('thead th'),
          function (th) { return th.textContent.trim(); }
        );
        if (!headers.length) return;
        table.querySelectorAll('tbody tr').forEach(function (row) {
          Array.prototype.forEach.call(row.children, function (cell, i) {
            if (cell.tagName === 'TD' && !cell.hasAttribute('data-label')) {
              cell.setAttribute('data-label', headers[i] || '');
            }
          });
        });
      });
    });
  </script>
</body>

</html>
