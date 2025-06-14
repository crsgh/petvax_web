@extends('layouts.user_type.auth')

@section('content')

  <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
    <div class="container-fluid py-5">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-2 px-4">
              <h6>Notifications</h6>
              <div class="row g-3 align-items-center">
                <div class="col-md-2">
                  <select id="statusFilter" class="form-select">
                    <option value="">All Status</option>
                    <option value="unread">Unread</option>
                    <option value="read">Read</option>
                  </select>
                </div>
                <div class="col-md-2">
                  <select id="typeFilter" class="form-select">
                    <option value="">All Types</option>
                    <option value="appointment">Appointment</option>
                    <option value="reminder">Reminder</option>
                    <option value="system">System</option>
                  </select>
                </div>
                <div class="col-md-2">
                  <select id="dateFilterType" class="form-select">
                    <option value="single">Single Date</option>
                    <option value="range">Date Range</option>
                  </select>
                </div>
                <div class="col-md-4" id="singleDateContainer">
                  <input type="date" id="singleDate" class="form-control">
                </div>
                <div class="col-md-4" id="dateRangeContainer" style="display: none;">
                  <div class="input-group">
                    <input type="date" id="startDate" class="form-control me-2" placeholder="Start Date">
                    <input type="date" id="endDate" class="form-control" placeholder="End Date">
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="h-3"></div>
                  <button type="button" id="resetFilters" class="btn btn-secondary">Reset</button>
                </div>
              </div>
            </div>
            <div class="card-body px-0 pt-2 pb-3">
              <div class="table-responsive p-2">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">Message</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">Type</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">Status</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">Date</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="notificationsTableBody">
                    @foreach($notifications as $notification)
                    <tr class="notification-row" 
                        data-status="{{ $notification->read_at ? 'read' : 'unread' }}"
                        data-type="{{ $notification->type }}"
                        data-date="{{ $notification->created_at->format('Y-m-d') }}">
                      <td>
                        <p class="text-sm font-weight-normal mb-0 ps-4">{{ $notification->message }}</p>
                      </td>
                      <td>
                        <span class="badge badge-sm {{ $notification->type == 'appointment' ? 'bg-gradient-info' : ($notification->type == 'reminder' ? 'bg-gradient-warning' : 'bg-gradient-secondary') }}">
                          {{ ucfirst($notification->type) }}
                        </span>
                      </td>
                      <td>
                        <span class="badge badge-sm {{ $notification->read_at ? 'bg-gradient-success' : 'bg-gradient-danger' }}">
                          {{ $notification->read_at ? 'Read' : 'Unread' }}
                        </span>
                      </td>
                      <td>
                        <p class="text-sm font-weight-normal mb-0 ps-4">{{ $notification->created_at->format('M d, Y h:i A') }}</p>
                      </td>
                      <td>
                        @if(!$notification->read_at)
                          <button class="btn btn-sm btn-info mark-read" data-id="{{ $notification->id }}">Mark as Read</button>
                        @endif
                        <button class="btn btn-sm btn-danger delete-notification" data-id="{{ $notification->id }}">Delete</button>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
                <div id="noNotificationsMessage" style="display: none;">
                  <p class="text-sm mb-0 ps-4">No notifications found</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateFilterType = document.getElementById('dateFilterType');
    const singleDateContainer = document.getElementById('singleDateContainer');
    const dateRangeContainer = document.getElementById('dateRangeContainer');
    const statusFilter = document.getElementById('statusFilter');
    const typeFilter = document.getElementById('typeFilter');
    const singleDate = document.getElementById('singleDate');
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    const resetButton = document.getElementById('resetFilters');
    const noNotificationsMessage = document.getElementById('noNotificationsMessage');

    function filterNotifications() {
        const rows = document.querySelectorAll('.notification-row');
        let visibleRows = 0;

        rows.forEach(row => {
            const status = row.dataset.status;
            const type = row.dataset.type;
            const notificationDate = new Date(row.dataset.date);
            let showRow = true;

            if (statusFilter.value && status !== statusFilter.value) {
                showRow = false;
            }

            if (typeFilter.value && type !== typeFilter.value) {
                showRow = false;
            }

            if (dateFilterType.value === 'single' && singleDate.value) {
                const filterDate = new Date(singleDate.value);
                if (notificationDate.toDateString() !== filterDate.toDateString()) {
                    showRow = false;
                }
            } else if (dateFilterType.value === 'range' && startDate.value && endDate.value) {
                const start = new Date(startDate.value);
                const end = new Date(endDate.value);
                if (notificationDate < start || notificationDate > end) {
                    showRow = false;
                }
            }

            row.style.display = showRow ? '' : 'none';
            if (showRow) visibleRows++;
        });

        noNotificationsMessage.style.display = visibleRows === 0 ? 'block' : 'none';
    }

    dateFilterType.addEventListener('change', function() {
        singleDateContainer.style.display = this.value === 'single' ? 'block' : 'none';
        dateRangeContainer.style.display = this.value === 'range' ? 'block' : 'none';
        filterNotifications();
    });

    [statusFilter, typeFilter, singleDate, startDate, endDate].forEach(filter => {
        filter.addEventListener('change', filterNotifications);
    });

    resetButton.addEventListener('click', function() {
        statusFilter.value = '';
        typeFilter.value = '';
        dateFilterType.value = 'single';
        singleDate.value = '';
        startDate.value = '';
        endDate.value = '';
        singleDateContainer.style.display = 'block';
        dateRangeContainer.style.display = 'none';
        
        document.querySelectorAll('.notification-row').forEach(row => {
            row.style.display = '';
        });
        
        noNotificationsMessage.style.display = 'none';
    });

    // Handle mark as read
    document.querySelectorAll('.mark-read').forEach(button => {
        button.addEventListener('click', function() {
            const notificationId = this.dataset.id;
            // Add your AJAX call here to mark notification as read
        });
    });

    // Handle delete notification
    document.querySelectorAll('.delete-notification').forEach(button => {
        button.addEventListener('click', function() {
            const notificationId = this.dataset.id;
            // Add your AJAX call here to delete notification
        });
    });
});
</script>
