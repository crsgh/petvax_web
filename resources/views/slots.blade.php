@extends('layouts.user_type.auth')

@section('content')

<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h6>Schedule Slots</h6>
                <button class="btn btn-primary btn-sm mb-0" onclick="openSidebar()">
                  <i class="fas fa-plus"></i>&nbsp;&nbsp;Add New Slot
                </button>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <select class="form-select" id="filterService" onchange="filterSlots()">
                    <option value="">Filter by Service</option>
                    @foreach($services as $service)
                      <option value="{{ $service->id }}">{{ $service->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-4">
                  <select class="form-select" id="filterDay" onchange="filterSlots()">
                    <option value="">Filter by Day</option>
                    @php
                      $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                    @endphp
                    @foreach($days as $day)
                      <option value="{{ $day }}">{{ ucfirst($day) }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                @if(count($slots) > 0)
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Day</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Service</th>
                      @if(auth()->user()->role_id == 1)
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Clinic</th>
                      @endif
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Time Slots</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="slotsTableBody">
                    @foreach($slots as $slot)
                    <tr data-service="{{ $slot->service_id }}" data-day="{{ $slot->day }}">
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">{{ ucfirst($slot->day) }}</h6>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-xs text-secondary mb-0">{{ $slot->service->name }}</p>
                      </td>
                      @if(auth()->user()->role_id == 1)
                      <td>
                        <p class="text-xs text-secondary mb-0">{{ $slot->clinic->name }}</p>
                      </td>
                      @endif
                      <td class="align-middle text-center">
                        <div class="d-flex flex-wrap gap-1 justify-content-center">
                          @foreach(json_decode($slot->time_slots) as $time)
                            <span class="badge bg-light text-dark">{{ $time }}</span>
                          @endforeach
                        </div>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="badge badge-sm bg-gradient-{{ $slot->status == 1 ? 'success' : 'secondary' }}">
                          {{ $slot->status == 1 ? 'Active' : 'Inactive' }}
                        </span>
                      </td>
                      <td class="align-middle text-center">
                        <div class="d-flex gap-2 justify-content-center">
                          <button class="btn btn-link text-secondary mb-0 p-1" onclick="openSidebar({{ json_encode($slot) }})">
                            <i class="fa fa-edit fa-lg"></i>
                          </button>
                          <form action="{{ url('schedules/duplicate/'.$slot->id) }}" id="duplicateForm" method="GET" class="d-inline">
                            {{-- <button type="submit" class="btn btn-link text-primary mb-0 p-1">
                              <i class="fa fa-copy fa-lg"></i>
                            </button> --}}
<button type="button" class="btn btn-link text-primary mb-0 p-1" onclick="document.getElementById('duplicateDayModal_{{ $slot->id }}').classList.add('show'); document.getElementById('duplicateDayModal_{{ $slot->id }}').style.display = 'block'; document.body.classList.add('modal-open');">
  <i class="fa fa-copy fa-lg"></i>
</button>

<!-- Duplicate Day Modal -->
<div class="modal fade" id="duplicateDayModal_{{ $slot->id }}" tabindex="-1" role="dialog" aria-labelledby="duplicateDayModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="duplicateDayModalLabel">Select Day to Duplicate To</h5>
        <button type="button" class="btn-close" onclick="closeModal('{{ $slot->id }}')" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <select class="form-select" id="duplicateDay_{{ $slot->id }}" onchange="document.getElementById('duplicateForm').action = '{{ url('schedules/duplicate/'.$slot->id) }}/' + this.value">
          @foreach($days as $day)
            @if($day != $slot->day)
              <option value="{{ $day }}">{{ ucfirst($day) }}</option>
            @endif
          @endforeach
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('{{ $slot->id }}')">Close</button>
        <button type="submit" class="btn btn-primary">Duplicate</button>
      </div>
    </div>
  </div>
</div>

<script>
function closeModal(id) {
  const modal = document.getElementById('duplicateDayModal_' + id);
  modal.classList.remove('show');
  modal.style.display = 'none';
  document.body.classList.remove('modal-open');
  // Remove modal backdrop if exists
  const backdrop = document.querySelector('.modal-backdrop');
  if (backdrop) {
    backdrop.remove();
  }
}
</script>

                          </form>
                          <button class="btn btn-link text-danger mb-0 p-1" onclick="deleteSlot({{ $slot->id }})">
                            <i class="fa fa-trash fa-lg"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</main>

<!-- Rest of the code remains the same -->

<script>
// Add this new function for filtering
function filterSlots() {
  const serviceFilter = document.getElementById('filterService').value;
  const dayFilter = document.getElementById('filterDay').value;
  const rows = document.querySelectorAll('#slotsTableBody tr');

  rows.forEach(row => {
    const serviceId = row.getAttribute('data-service');
    const day = row.getAttribute('data-day');
    const showRow = (!serviceFilter || serviceId === serviceFilter) && 
                    (!dayFilter || day === dayFilter);
    row.style.display = showRow ? '' : 'none';
  });
}

// Rest of the JavaScript code remains the same
</script>

@endsection
