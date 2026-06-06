@extends('layouts.user_type.auth')

@section('content')
<div class="dashboard-container">

  <!-- Stats Grid -->
  <div class="stats-grid">
    <!-- Today's Income Card -->
    <div class="stat-card income-card">
      <div class="stat-header">
        <div class="stat-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 1V3"/><path d="M12 21V23"/><path d="M4 12H2"/><path d="M22 12H20"/><path d="M16.95 7.05L18.36 5.64"/><path d="M5.64 18.36L7.05 16.95"/><path d="M5.64 5.64L7.05 7.05"/><path d="M16.95 16.95L18.36 18.36"/><path d="M12 16C14.2091 16 16 14.2091 16 12C16 9.79086 14.2091 8 12 8C9.79086 8 8 9.79086 8 12C8 14.2091 9.79086 16 12 16Z"/>
          </svg>
        </div>
        <div class="stat-trend positive">
          <x-ui.icon name="arrow-up" class="w-4 h-4" />
          <span>+12%</span>
        </div>
      </div>
      <div class="stat-content">
        <div class="stat-value">₱{{ is_array($todayIncome) ? '0.00' : number_format((float)$todayIncome, 2) }}</div>
        <div class="stat-label">Today's Revenue</div>
      </div>
    </div>

    <!-- Total Users Card -->
    <div class="stat-card users-card">
      <div class="stat-header">
        <div class="stat-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21V19C17 16.7909 15.2091 15 13 15H5C2.79086 15 1 16.7909 1 19V21"/><path d="M9 11C11.2091 11 13 9.20914 13 7C13 4.79086 11.2091 3 9 3C6.79086 3 5 4.79086 5 7C5 9.20914 6.79086 11 9 11Z"/><path d="M23 21V19C22.7353 17.4697 21.922 16.1272 20.74 15.17"/><path d="M16 3.13C17.188 4.07978 18.0026 5.4187 18.2678 6.90901C18.533 8.39933 18.2302 9.93183 17.41 11.21"/>
          </svg>
        </div>
        <div class="stat-trend neutral">
          <span class="text-gray-400">—</span>
          <span>0%</span>
        </div>
      </div>
      <div class="stat-content">
        <div class="stat-value">{{ $todayStats['total_users'] ?? 0 }}</div>
        <div class="stat-label">Total Users</div>
      </div>
      <div class="stat-actions">
        <a href="/sales-report" class="stat-action-btn">
          <x-ui.icon name="chart" class="w-4 h-4" />
          View Report
        </a>
      </div>
    </div>

    <!-- Appointments Card -->
    <div class="stat-card appointments-card">
      <div class="stat-header">
        <div class="stat-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 9H21"/><path d="M7 3V5"/><path d="M17 3V5"/><path d="M5 5H19C20.1046 5 21 5.89543 21 7V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V7C3 5.89543 3.89543 5 5 5Z"/><path d="M8 13H10"/><path d="M14 13H16"/><path d="M8 17H10"/><path d="M14 17H16"/>
          </svg>
        </div>
        <div class="stat-trend positive">
          <x-ui.icon name="arrow-up" class="w-4 h-4" />
          <span>+8%</span>
        </div>
      </div>
      <div class="stat-content">
        <div class="stat-value">{{ is_array($todayBookingCounts) ? '0' : number_format((float)$todayBookingCounts) }}</div>
        <div class="stat-label">Appointments</div>
      </div>
    </div>

    <!-- Completion Rate Card -->
    <div class="stat-card completion-card">
      <div class="stat-header">
        <div class="stat-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 12H18L15 21L9 3L6 12H2"/>
          </svg>
        </div>
        <div class="stat-trend positive">
          <x-ui.icon name="arrow-up" class="w-4 h-4" />
          <span>+5%</span>
        </div>
      </div>
      <div class="stat-content">
        <div class="stat-value">94%</div>
        <div class="stat-label">Completion Rate</div>
      </div>
    </div>
  </div>

  <!-- Main Content Grid -->
  <div class="main-grid">
    <!-- Revenue Chart -->
    <div class="chart-section">
      <div class="section-header">
        <h2 class="section-title">Revenue Overview</h2>
        <div class="section-subtitle">Last 7 days performance</div>
      </div>
      <div class="chart-container">
        <canvas id="revenueChart"></canvas>
      </div>
    </div>

    <!-- Top Performers -->
    <div class="performers-section">
      <div class="section-header">
        <h2 class="section-title">Top Performers</h2>
        <div class="section-subtitle">This month's leaders</div>
      </div>
      <div class="performers-list">
        @forelse($topBookings as $index => $booking)
          <div class="performer-item {{ $index === 0 ? 'top-performer' : '' }}">
            <div class="performer-rank">{{ $index + 1 }}</div>
            <div class="performer-avatar">
              <img src="{{ $booking['avatar'] ? asset('storage/' . $booking['avatar']) : asset('assets/img/team-2.jpg') }}" 
                   alt="{{ $booking['name'] }}"
                   onerror="this.src='{{ asset('assets/img/team-2.jpg') }}'">
              @if($index === 0)
                <div class="crown-badge">
                  <x-ui.icon name="star" class="w-4 h-4 text-yellow-500" />
                </div>
              @endif
            </div>
            <div class="performer-info">
              <div class="performer-name">{{ $booking['name'] }}</div>
              <div class="performer-count">{{ $booking['completed_count'] }} completed</div>
            </div>
            <div class="performer-score">
              <div class="score-circle">
                <span>{{ $booking['completed_count'] }}</span>
              </div>
            </div>
          </div>
        @empty
          <div class="empty-state">
            <x-ui.icon name="chart" class="w-8 h-8 text-gray-400" />
            <p>No performance data available</p>
          </div>
        @endforelse
      </div>
    </div>
  </div>

  <!-- Quick Actions -->
  <div class="quick-actions-section">
    <div class="section-header">
      <h2 class="section-title">Quick Actions</h2>
      <div class="section-subtitle">Frequently used features</div>
    </div>
    <div class="quick-actions-grid">
      <a href="/bookings" class="quick-action">
        <div class="action-icon new-booking">
          <x-ui.icon name="add" class="w-5 h-5" />
        </div>
        <div class="action-content">
          <div class="action-title">New Appointment</div>
          <div class="action-subtitle">Schedule a visit</div>
        </div>
      </a>
      
      <a href="/pets" class="quick-action">
        <div class="action-icon add-pet">
          <x-ui.icon name="pets" class="w-5 h-5" />
        </div>
        <div class="action-content">
          <div class="action-title">Add Pet</div>
          <div class="action-subtitle">Register new pet</div>
        </div>
      </a>
      
      <a href="/medical-histories" class="quick-action">
        <div class="action-icon medical-record">
          <x-ui.icon name="medical" class="w-5 h-5" />
        </div>
        <div class="action-content">
          <div class="action-title">Medical Record</div>
          <div class="action-subtitle">Update health info</div>
        </div>
      </a>
      
      <a href="/inventory" class="quick-action">
        <div class="action-icon inventory">
          <x-ui.icon name="package" class="w-5 h-5" />
        </div>
        <div class="action-content">
          <div class="action-title">Inventory</div>
          <div class="action-subtitle">Manage supplies</div>
        </div>
      </a>
    </div>
  </div>
</div>

<style>
/* Dashboard Container */
.dashboard-container {
  padding: 1.5rem;
  font-family: 'Poppins', sans-serif;
  background: #fafbfc;
  min-height: 100vh;
}


/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
  margin-bottom: 3rem;
}

.stat-card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  border: 1px solid #f3f4f6;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #3b82f6, #1d4ed8);
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
}

.income-card::before { background: linear-gradient(90deg, #10b981, #059669); }
.users-card::before { background: linear-gradient(90deg, #3b82f6, #1d4ed8); }
.appointments-card::before { background: linear-gradient(90deg, #f59e0b, #d97706); }
.completion-card::before { background: linear-gradient(90deg, #8b5cf6, #7c3aed); }

.stat-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6b7280;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
}

.income-card .stat-icon { color: #10b981; }
.users-card .stat-icon { color: #3b82f6; }
.appointments-card .stat-icon { color: #f59e0b; }
.completion-card .stat-icon { color: #8b5cf6; }

.stat-trend {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.75rem;
  font-weight: 600;
  padding: 0.25rem 0.5rem;
  border-radius: 6px;
}

.stat-trend.positive {
  background: #dcfce7;
  color: #166534;
}

.stat-trend.neutral {
  background: #f3f4f6;
  color: #6b7280;
}

.stat-value {
  font-size: 2rem;
  font-weight: 600;
  color: #111827;
  line-height: 1;
  margin-bottom: 0.5rem;
}

.stat-label {
  font-size: 0.875rem;
  color: #6b7280;
  font-weight: 500;
}

.stat-actions {
  margin-top: 1rem;
}

.stat-action-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background: rgba(59, 130, 246, 0.1);
  color: #3b82f6;
  text-decoration: none;
  border-radius: 8px;
  font-size: 0.75rem;
  font-weight: 500;
  transition: all 0.2s ease;
}

.stat-action-btn:hover {
  background: rgba(59, 130, 246, 0.2);
  transform: translateY(-1px);
}

/* Main Grid */
.main-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 2rem;
  margin-bottom: 3rem;
}

/* Chart Section */
.chart-section {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  border: 1px solid #f3f4f6;
}

.section-header {
  margin-bottom: 1.5rem;
}

.section-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
  margin: 0 0 0.25rem 0;
}

.section-subtitle {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
}

.chart-container {
  position: relative;
  height: 300px;
}

/* Performers Section */
.performers-section {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  border: 1px solid #f3f4f6;
}

.performers-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.performer-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  border-radius: 12px;
  transition: all 0.2s ease;
  border: 1px solid transparent;
}

.performer-item:hover {
  background: #f9fafb;
  border-color: #e5e7eb;
}

.performer-item.top-performer {
  background: linear-gradient(135deg, #fef3c7, #fde68a);
  border-color: #f59e0b;
}

.performer-rank {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #f3f4f6;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  color: #374151;
  font-size: 0.875rem;
}

.top-performer .performer-rank {
  background: #f59e0b;
  color: white;
}

.performer-avatar {
  position: relative;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  overflow: hidden;
}

.performer-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.crown-badge {
  position: absolute;
  top: -8px;
  right: -8px;
  width: 20px;
  height: 20px;
  background: #f59e0b;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 10px;
  border: 2px solid white;
}

.performer-info {
  flex: 1;
}

.performer-name {
  font-weight: 600;
  color: #111827;
  font-size: 0.875rem;
  margin-bottom: 0.25rem;
}

.performer-count {
  font-size: 0.75rem;
  color: #6b7280;
}

.score-circle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #3b82f6;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 0.875rem;
}

.empty-state {
  text-align: center;
  padding: 2rem;
  color: #6b7280;
}

.empty-state i {
  font-size: 2rem;
  margin-bottom: 0.5rem;
  display: block;
}

/* Quick Actions */
.quick-actions-section {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  border: 1px solid #f3f4f6;
}

.quick-actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.quick-action {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  border-radius: 12px;
  text-decoration: none;
  color: inherit;
  border: 1px solid #f3f4f6;
  transition: all 0.2s ease;
}

.quick-action:hover {
  background: #f9fafb;
  border-color: #d1d5db;
  text-decoration: none;
  color: inherit;
  transform: translateY(-2px);
}

.action-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
}

.action-icon.new-booking { color: #3b82f6; }
.action-icon.add-pet { color: #10b981; }
.action-icon.medical-record { color: #f59e0b; }
.action-icon.inventory { color: #8b5cf6; }

.action-title {
  font-weight: 600;
  color: #111827;
  font-size: 0.875rem;
  margin-bottom: 0.25rem;
}

.action-subtitle {
  font-size: 0.75rem;
  color: #6b7280;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .main-grid {
    grid-template-columns: 1fr;
  }
  
  .stats-grid {
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  }
}

@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .quick-actions-grid {
    grid-template-columns: 1fr;
  }
  
  .dashboard-container {
    padding: 0;
  }
}
</style>

<script>
// Initialize chart
document.addEventListener('DOMContentLoaded', function() {
  
  // Revenue Chart
  const ctx = document.getElementById('revenueChart').getContext('2d');
  
  // Process the daily data for last 7 days
  const dailyData = @json($dailyBookings);
  const labels = [];
  const incomeData = [];
  
  // Get dates for last 7 days
  for(let i = 6; i >= 0; i--) {
    let date = new Date();
    date.setDate(date.getDate() - i);
    let dateStr = date.toISOString().split('T')[0];
    
    labels.push(date.toLocaleDateString('en-US', { weekday: 'short', day: 'numeric' }));
    incomeData.push(dailyData[dateStr] ? dailyData[dateStr].income : 0);
  }

  new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Revenue',
        data: incomeData,
        borderColor: '#3b82f6',
        backgroundColor: 'rgba(59, 130, 246, 0.1)',
        borderWidth: 3,
        fill: true,
        tension: 0.4,
        pointBackgroundColor: '#3b82f6',
        pointBorderColor: '#ffffff',
        pointBorderWidth: 2,
        pointRadius: 6,
        pointHoverRadius: 8,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          backgroundColor: 'rgba(0, 0, 0, 0.8)',
          titleColor: '#ffffff',
          bodyColor: '#ffffff',
          borderColor: '#3b82f6',
          borderWidth: 1,
          cornerRadius: 8,
          displayColors: false,
          titleFont: {
            family: 'Poppins',
            size: 14,
            weight: '600'
          },
          bodyFont: {
            family: 'Poppins',
            size: 13
          },
          callbacks: {
            label: function(context) {
              return 'Revenue: ₱' + context.parsed.y.toLocaleString();
            }
          }
        }
      },
      scales: {
        x: {
          grid: {
            display: false
          },
          ticks: {
            color: '#6b7280',
            font: {
              family: 'Poppins',
              size: 12,
              weight: '500'
            }
          }
        },
        y: {
          grid: {
            color: 'rgba(0, 0, 0, 0.05)',
            borderDash: [5, 5]
          },
          ticks: {
            color: '#6b7280',
            font: {
              family: 'Poppins',
              size: 12,
              weight: '500'
            },
            callback: function(value) {
              return '₱' + value.toLocaleString();
            }
          }
        }
      },
      interaction: {
        intersect: false,
        mode: 'index'
      }
    }
  });
});
</script>
@endsection
