@extends('layouts.user_type.auth')

@section('content')
<div class="dashboard-container">

  <!-- Stats Grid -->
  <div class="stats-grid">
    <!-- Today's Income Card -->
    <div class="stat-card income-card">
      <div class="stat-header">
        <div class="stat-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
          </svg>
        </div>
        <div class="stat-trend positive">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="19" x2="12" y2="5"/>
            <polyline points="5,12 12,5 19,12"/>
          </svg>
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
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
        </div>
        <div class="stat-trend neutral">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="5" y1="12" x2="19" y2="12"/>
          </svg>
          <span>0%</span>
        </div>
      </div>
      <div class="stat-content">
        <div class="stat-value">{{ $todayStats['total_users'] ?? 0 }}</div>
        <div class="stat-label">Total Users</div>
      </div>
      <div class="stat-actions">
        <a href="/sales-report" class="stat-action-btn">
          <i class="fas fa-chart-line"></i>
          View Report
        </a>
      </div>
    </div>

    <!-- Appointments Card -->
    <div class="stat-card appointments-card">
      <div class="stat-header">
        <div class="stat-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
            <path d="M9 14l2 2 4-4"/>
          </svg>
        </div>
        <div class="stat-trend positive">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="19" x2="12" y2="5"/>
            <polyline points="5,12 12,5 19,12"/>
          </svg>
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
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/>
          </svg>
        </div>
        <div class="stat-trend positive">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="19" x2="12" y2="5"/>
            <polyline points="5,12 12,5 19,12"/>
          </svg>
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
                  <i class="fas fa-crown"></i>
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
            <i class="fas fa-chart-bar"></i>
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
          <i class="fas fa-plus"></i>
        </div>
        <div class="action-content">
          <div class="action-title">New Appointment</div>
          <div class="action-subtitle">Schedule a visit</div>
        </div>
      </a>
      
      <a href="/pets" class="quick-action">
        <div class="action-icon add-pet">
          <i class="fas fa-paw"></i>
        </div>
        <div class="action-content">
          <div class="action-title">Add Pet</div>
          <div class="action-subtitle">Register new pet</div>
        </div>
      </a>
      
      <a href="/medical-histories" class="quick-action">
        <div class="action-icon medical-record">
          <i class="fas fa-file-medical"></i>
        </div>
        <div class="action-content">
          <div class="action-title">Medical Record</div>
          <div class="action-subtitle">Update health info</div>
        </div>
      </a>
      
      <a href="/inventory" class="quick-action">
        <div class="action-icon inventory">
          <i class="fas fa-boxes"></i>
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
