@extends('layouts.user_type.auth')

@section('content')

  <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h6>Clinic Ratings</h6>
                <div class="d-flex gap-3">
                  <select class="form-select form-select-sm" id="dateFilterType" style="width: 150px;">
                    <option value="single">Single Date</option>
                    <option value="range">Date Range</option>
                  </select>
                  <div id="singleDateContainer">
                    <input type="date" class="form-control form-control-sm" id="singleDate">
                  </div>
                  <div id="dateRangeContainer" style="display: none;">
                    <input type="date" class="form-control form-control-sm" id="startDate">
                    <input type="date" class="form-control form-control-sm" id="endDate">
                  </div>
                  <select class="form-select form-select-sm" id="ratingFilter">
                    <option value="">All Ratings</option>
                    <option value="5">5 Stars</option>
                    <option value="4">4 Stars</option>
                    <option value="3">3 Stars</option>
                    <option value="2">2 Stars</option>
                    <option value="1">1 Star</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0" id="ratingsTable">
                  <thead>
                    <tr>
                      
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Rating</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Review</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">User</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($ratings as $rating)
                    <tr data-rating="{{ $rating->rating }}" data-date="{{ $rating->created_at->format('Y-m-d') }}">
                      <td>
                        <div class="rating-stars">
                          @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= $rating->rating ? 'text-warning' : 'text-secondary' }}"></i>
                          @endfor
                        </div>
                      </td>
                      <td class="align-middle text-center">
                        <p class="text-xs text-secondary mb-0" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">{{ $rating->comment }}</p>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ $rating->created_at->format('Y-m-d') }}</span>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="text-secondary text-xs font-weight-bold">{{ ucwords($rating->user->name) }}</span>
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
  </main>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateFilterType = document.getElementById('dateFilterType');
    const singleDateContainer = document.getElementById('singleDateContainer');
    const dateRangeContainer = document.getElementById('dateRangeContainer');
    const singleDate = document.getElementById('singleDate');
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    const ratingFilter = document.getElementById('ratingFilter');
    const table = document.getElementById('ratingsTable');
    const tbody = table.getElementsByTagName('tbody')[0];
    const rows = tbody.getElementsByTagName('tr');

    // Toggle date filter containers
    dateFilterType.addEventListener('change', function() {
        if (this.value === 'single') {
            singleDateContainer.style.display = 'block';
            dateRangeContainer.style.display = 'none';
            startDate.value = '';
            endDate.value = '';
        } else {
            singleDateContainer.style.display = 'none';
            dateRangeContainer.style.display = 'flex';
            singleDate.value = '';
        }
        filterTable();
    });

    function filterTable() {
        const filterType = dateFilterType.value;
        const selectedRating = ratingFilter.value;

        Array.from(rows).forEach(row => {
            const rowRating = row.getAttribute('data-rating');
            const rowDate = row.getAttribute('data-date');
            
            let showRow = true;

            // Apply rating filter
            if (selectedRating && rowRating !== selectedRating) {
                showRow = false;
            }

            // Apply date filter
            if (filterType === 'single' && singleDate.value) {
                if (rowDate !== singleDate.value) {
                    showRow = false;
                }
            } else if (filterType === 'range' && startDate.value && endDate.value) {
                const date = new Date(rowDate);
                const start = new Date(startDate.value);
                const end = new Date(endDate.value);
                
                if (date < start || date > end) {
                    showRow = false;
                }
            }

            row.style.display = showRow ? '' : 'none';
        });
    }

    // Add event listeners
    singleDate.addEventListener('change', filterTable);
    startDate.addEventListener('change', filterTable);
    endDate.addEventListener('change', filterTable);
    ratingFilter.addEventListener('change', filterTable);
});
</script>


@endsection
