@extends('layouts.user_type.auth')

@section('content')

  <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
    <div class="container-fluid py-5">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-2 px-4">
              <h6>Activity Logs</h6>
              <div class="row g-3 align-items-center">
                <div class="col-md-2">
                  <select id="userFilter" class="form-select">
                    <option value="">Select User</option>
                    @foreach($users as $user)
                      <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-2">
                  <select id="roleFilter" class="form-select">
                    <option value="">Select Role</option>
                    <option value="2">Owner</option>
                    <option value="3">Front Desk</option>
                    <option value="4">Veterinarian</option>
                    <option value="5">User</option>
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
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">Description</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">User</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">Role</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">Date</th>
                    </tr>
                  </thead>
                  <tbody id="activitiesTableBody">
                    @foreach($activities as $activity)
                    <tr class="activity-row" 
                        data-user="{{ $activity->user->id }}"
                        data-role="{{ $activity->user->role_id }}"
                        data-date="{{ $activity->created_at->format('Y-m-d') }}">
                      <td>
                        <p class="text-sm font-weight-normal mb-0 ps-4">{{ $activity->description }}</p>
                      </td>
                      <td>
                        <p class="text-sm font-weight-normal mb-0 ps-4">{{ $activity->user->name }}</p>
                      </td>
                      <td class="ps-4">
                        @switch($activity->user->role_id)
                            @case(2)
                                <span class="badge badge-sm bg-gradient-success">Owner</span>
                                @break
                            @case(3)
                                <span class="badge badge-sm bg-gradient-info">Front Desk</span>
                                @break
                            @case(4)
                                <span class="badge badge-sm bg-gradient-warning">Veterinarian</span>
                                @break
                            @case(5)
                                <span class="badge badge-sm bg-gradient-primary">User</span>
                                @break
                            @default
                                <span class="badge badge-sm bg-gradient-secondary">Unknown</span>
                        @endswitch
                      </td>
                      <td>
                        <p class="text-sm font-weight-normal mb-0 ps-4">{{ $activity->created_at->format('M d, Y h:i A') }}</p>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
                <div id="noActivitiesMessage" style="display: none;">
                  <p class="text-sm mb-0 ps-4">No activities found</p>
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
    const userFilter = document.getElementById('userFilter');
    const roleFilter = document.getElementById('roleFilter');
    const singleDate = document.getElementById('singleDate');
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    const resetButton = document.getElementById('resetFilters');
    const noActivitiesMessage = document.getElementById('noActivitiesMessage');

    function filterActivities() {
        const rows = document.querySelectorAll('.activity-row');
        let visibleRows = 0;

        rows.forEach(row => {
            const userId = row.dataset.user;
            const roleId = row.dataset.role;
            const activityDate = new Date(row.dataset.date);
            let showRow = true;

            if (userFilter.value && userId !== userFilter.value) {
                showRow = false;
            }

            if (roleFilter.value && roleId !== roleFilter.value) {
                showRow = false;
            }

            if (dateFilterType.value === 'single' && singleDate.value) {
                const filterDate = new Date(singleDate.value);
                if (activityDate.toDateString() !== filterDate.toDateString()) {
                    showRow = false;
                }
            } else if (dateFilterType.value === 'range' && startDate.value && endDate.value) {
                const start = new Date(startDate.value);
                const end = new Date(endDate.value);
                if (activityDate < start || activityDate > end) {
                    showRow = false;
                }
            }

            row.style.display = showRow ? '' : 'none';
            if (showRow) visibleRows++;
        });

        noActivitiesMessage.style.display = visibleRows === 0 ? 'block' : 'none';
    }

    dateFilterType.addEventListener('change', function() {
        singleDateContainer.style.display = this.value === 'single' ? 'block' : 'none';
        dateRangeContainer.style.display = this.value === 'range' ? 'block' : 'none';
        filterActivities();
    });

    [userFilter, roleFilter, singleDate, startDate, endDate].forEach(filter => {
        filter.addEventListener('change', filterActivities);
    });

    resetButton.addEventListener('click', function() {
        userFilter.value = '';
        roleFilter.value = '';
        dateFilterType.value = 'single';
        singleDate.value = '';
        startDate.value = '';
        endDate.value = '';
        singleDateContainer.style.display = 'block';
        dateRangeContainer.style.display = 'none';
        
        document.querySelectorAll('.activity-row').forEach(row => {
            row.style.display = '';
        });
        
        noActivitiesMessage.style.display = 'none';
    });
});
</script> 
