@extends('layouts.user_type.auth')

@section('content')

<div class="m-4">
  <div class="row">
    <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-capitalize font-weight-bold">Today's Income</p>
                <h5 class="font-weight-bolder mb-0">
                  {{ is_array($todayIncome) ? '0.00' : number_format((float)$todayIncome, 2) }}
                  <span class="text-success text-sm font-weight-bolder"></span>
                </h5>
              </div>
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-capitalize font-weight-bold">Total User's</p>
                <h5 class="font-weight-bolder mb-0">
                  {{ $todayStats['total_users']}}
                  <span class="text-success text-sm font-weight-bolder"></span>
                </h5>
              </div>
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                <i class="ni ni-world text-lg opacity-10" aria-hidden="true"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Bookings</p>
                <h5 class="font-weight-bolder mb-0">
                  {{ is_array($todayBookingCounts) ? '0' : number_format((float)$todayBookingCounts) }}
                  <span class="text-danger text-sm font-weight-bolder"></span>
                </h5>
              </div>
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                <i class="ni ni-paper-diploma text-lg opacity-10" aria-hidden="true"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
  </div>
  
  <div class="row mt-4">

    <div class="col-lg-7">
      <div class="card z-index-2">
        <div class="card-header pb-0">
          <h6>Income overview</h6>
          <p class="text-sm">
            <i class="fa fa-arrow-up text-success"></i>
            <span class="font-weight-bold">4% more</span> in 2021
          </p>
        </div>
        <div class="card-body p-3">
          <div class="chart">
            <canvas id="chart-line" class="chart-canvas" height="300"></canvas>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-5 mb-lg-0 mb-4">
      <div class="card z-index-2">
        <div class="card-body p-3">
    <div class="table-responsive">
      <h6 class="mb-3">Top Completed Bookings</h6>
      <table class="table align-items-center mb-0">
        <thead>
          <tr>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Employee</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Total Completion</th>
          </tr>
        </thead>
        <tbody>
          @foreach($topBookings as $booking)
        
          <tr>
            <td>
              <div class="d-flex px-2 py-1">
                <div class="avatar me-3">
                    <img src="{{ asset('storage/' . $booking['avatar']) }}" class="avatar-sm rounded-circle" alt="employee photo">
                </div>
                <div class="d-flex flex-column justify-content-center">
                  <h6 class="mb-0 text-sm">{{ $booking['name']}}</h6>
                </div>
              </div>
            </td>
            <td>
              <p class="text-sm font-weight-bold mb-0">{{ $booking['completed_count'] }} </p>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
        </div>
      </div>
    </div>
  </div>
</div>
 

@endsection
@push('dashboard')
  <script>
    window.onload = function() {
      var ctx2 = document.getElementById("chart-line").getContext("2d");

      var gradientStroke1 = ctx2.createLinearGradient(0, 230, 0, 50);
      gradientStroke1.addColorStop(1, 'rgba(0,128,255,0.2)');  // Changed to blue
      gradientStroke1.addColorStop(0.2, 'rgba(0,128,255,0.0)');
      gradientStroke1.addColorStop(0, 'rgba(0,128,255,0)');

      // Process the daily data for last 7 days
      var dailyData = {!! json_encode($dailyBookings) !!};
      var labels = [];
      var incomeData = [];
      
      // Get dates for last 7 days
      for(let i=6; i>=0; i--) {
        let date = new Date();
        date.setDate(date.getDate() - i);
        let dateStr = date.toISOString().split('T')[0];
        
        labels.push(date.toLocaleString('default', { weekday: 'short' }));
        incomeData.push(dailyData[dateStr] ? dailyData[dateStr].income : 0);
      }

      new Chart(ctx2, {
        type: "line",
        data: {
          labels: labels,
          datasets: [{
              label: "Daily Income",
              tension: 0.4,
              borderWidth: 3,
              pointRadius: 3,
              borderColor: "#0080ff",  // Changed to blue
              backgroundColor: gradientStroke1,
              fill: true,
              data: incomeData,
              maxBarThickness: 6
            }
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: true,
            }
          },
          interaction: {
            intersect: false,
            mode: 'index',
          },
          scales: {
            y: {
              grid: {
                drawBorder: false,
                display: true,
                drawOnChartArea: true,
                drawTicks: false,
                borderDash: [5, 5]
              },
              ticks: {
                display: true,
                padding: 10,
                color: '#b2b9bf',
                font: {
                  size: 11,
                  family: "Open Sans",
                  style: 'normal',
                  lineHeight: 2
                },
                callback: function(value) {
                  return '' + value;
                }
              }
            },
            x: {
              grid: {
                drawBorder: false,
                display: false,
                drawOnChartArea: false,
                drawTicks: false,
                borderDash: [5, 5]
              },
              ticks: {
                display: true,
                color: '#b2b9bf',
                padding: 20,
                font: {
                  size: 11,
                  family: "Open Sans",
                  style: 'normal',
                  lineHeight: 2
                },
              }
            },
          },
        },
      });
    }
  </script>
@endpush
