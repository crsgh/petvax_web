@extends('layouts.user_type.auth')

@section('content')

<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
              <h6>Schedule Slots</h6>
              <button class="btn btn-primary btn-sm mb-0" onclick="openSidebar()">
                <i class="fas fa-plus"></i>&nbsp;&nbsp;Add New Slot
              </button>
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
                  <tbody>
                    @foreach($slots as $slot)
                    <tr>
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
                          <form action="{{ url('schedules/duplicate/'.$slot->id) }}" method="GET" class="d-inline">
                            
                            
                            <button type="submit" class="btn btn-link text-primary mb-0 p-1">
                              <i class="fa fa-copy fa-lg"></i>
                            </button>
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

<!-- Add/Edit Slot Sidebar -->
<div class="sidebar-overlay" onclick="closeSidebar()"></div>
<div class="sidebar p-3" id="addSlotSidebar">
  <div class="sidebar-header">
    <h5 id="sidebarTitle">Customize Schedule Slots</h5>
    <button type="button" class="btn-close" onclick="closeSidebar()"></button>
  </div>
  <form id="addSlotForm" method="POST" action="">
    @csrf
    <input type="hidden" id="slot_id" name="slot_id">
    @if(auth()->user()->role_id == 1)
    <div class="sidebar-body">
      <div class="mb-3">
        <label for="clinic_id" class="form-label">Select Clinic</label>
        <select class="form-select" id="clinic_id" name="clinic_id" required>
          @foreach($clinics as $clinic)
            <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
          @endforeach
        </select>
      </div>
    @else
      <input type="hidden" name="clinic_id" value="{{ auth()->user()->clinic_id }}">
    @endif

      <div class="mb-3">
        <label for="service_id" class="form-label">Select Service</label>
        <select class="form-select" id="service_id" name="service_id" required>
          @foreach($services as $service)
            <option value="{{ $service->id }}">{{ $service->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="mb-3">
        <label for="day" class="form-label">Select Day</label>
        <select class="form-select" id="day" name="day" required>
          @php
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
          @endphp
          @foreach($days as $day)
            <option value="{{ $day }}">{{ ucfirst($day) }}</option>
          @endforeach
        </select>
      </div>

      <div class="mb-3 row">
        <div class="col-md-6">
          <label for="startTime" class="form-label">Start Time</label>
          <input type="time" class="form-control" id="startTime" value="08:00" required>
        </div>
        <div class="col-md-6">
          <label for="endTime" class="form-label">End Time</label>
          <input type="time" class="form-control" id="endTime" value="20:00" required>
        </div>
      </div>
      
      <div class="mb-3">
        <label class="form-label">Available Time Slots</label>
        <div id="timeSlots" class="border p-3 rounded" style="max-height: 400px; overflow-y: auto;">
          <div class="time-slots-grid">
            <!-- Time slots will be populated here -->
          </div>
        </div>
      </div>
      <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select class="form-select" id="status" name="status" required>
          <option value="1">Active</option>
          <option value="0">Inactive</option>
        </select>
      </div>
      <div class="d-flex justify-content-end gap-2 mt-3">
        <button type="button" class="btn btn-secondary" onclick="closeSidebar()">Close</button>
        <button type="submit" class="btn btn-primary" id="saveScheduleBtn">Save Schedule</button>
      </div>
    </div>
   
  </form>
</div>

<style>
.sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: none;
  z-index: 1040;
}

.sidebar {
  position: fixed;
  top: 0;
  right: -400px;
  width: 400px;
  height: 100%;
  background: white;
  z-index: 1050;
  transition: right 0.3s ease;
  box-shadow: -2px 0 8px rgba(0,0,0,0.15);
  display: flex;
  flex-direction: column;
}

.sidebar.show {
  right: 0;
}

.sidebar-header {
  padding: 1rem;
  border-bottom: 1px solid #dee2e6;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.sidebar-body {
  padding: 1rem;
  flex-grow: 1;
  overflow-y: auto;
}

.sidebar-footer {
  padding: 1rem;
  border-top: 1px solid #dee2e6;
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
}

.time-slots-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
}

.time-slot-chip {
  border: 1px solid #ddd;
  border-radius: 16px;
  padding: 8px 16px;
  cursor: pointer;
  transition: all 0.3s ease;
  background: #f8f9fa;
  text-align: center;
  min-height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.time-slot-chip label {
  margin: 0;
  cursor: pointer;
  width: 100%;
  text-align: center;
}

.time-slot-chip input[type="checkbox"] {
  display: none;
}

.time-slot-chip.selected {
  background: #4CAF50;
  color: white;
  border-color: #45a049;
}
</style>

<script>
function openSidebar(slot = null) {
  const sidebar = document.getElementById('addSlotSidebar');
  const overlay = document.querySelector('.sidebar-overlay');
  const title = document.getElementById('sidebarTitle');
  const daySelect = document.getElementById('day');
  
  // Reset form before opening
  resetForm();
  
  // Set title based on whether editing or adding
  title.textContent = slot ? 'Edit Schedule Slot' : 'Add New Schedule Slot';
  
  // If editing, populate form with slot data
  if (slot) {
    const slotData = typeof slot === 'string' ? JSON.parse(slot) : slot;
    document.getElementById('slot_id').value = slotData.id;
    if(document.getElementById('clinic_id')) {
      document.getElementById('clinic_id').value = slotData.clinic_id;
    }
    document.getElementById('service_id').value = slotData.service_id;
    document.getElementById('status').value = slotData.status;
    
    // Add the current day as an option if it doesn't exist
    let dayExists = false;
    for(let i = 0; i < daySelect.options.length; i++) {
      if(daySelect.options[i].value === slotData.day) {
        dayExists = true;
        break;
      }
    }
    if(!dayExists) {
      const option = new Option(slotData.day.charAt(0).toUpperCase() + slotData.day.slice(1), slotData.day);
      daySelect.add(option);
    }
    daySelect.value = slotData.day;
    
    // Generate time slots and mark selected ones
    generateTimeSlots();
    setTimeout(() => {
      const selectedSlots = JSON.parse(slotData.time_slots);
      selectedSlots.forEach(time => {
        const slotId = `slot_${time.replace(/\s/g, '')}`;
        const checkbox = document.getElementById(slotId);
        if (checkbox) {
          checkbox.checked = true;
          checkbox.closest('.time-slot-chip').classList.add('selected');
        }
      });
    }, 100);
  } else {
    generateTimeSlots();
  }
  
  // Show sidebar and overlay
  sidebar.classList.add('show');
  overlay.style.display = 'block';
  document.body.style.overflow = 'hidden';
}

function closeSidebar() {
  const sidebar = document.getElementById('addSlotSidebar');
  const overlay = document.querySelector('.sidebar-overlay');
  
  sidebar.classList.remove('show');
  overlay.style.display = 'none';
  document.body.style.overflow = 'auto';
  resetForm();
}

function resetForm() {
  document.getElementById('addSlotForm').reset();
  document.getElementById('slot_id').value = '';
  document.getElementById('startTime').value = '08:00';
  document.getElementById('endTime').value = '20:00';
  const timeSlotsGrid = document.querySelector('.time-slots-grid');
  if (timeSlotsGrid) {
    timeSlotsGrid.innerHTML = '';
  }
}

document.getElementById('startTime').addEventListener('change', generateTimeSlots);
document.getElementById('endTime').addEventListener('change', generateTimeSlots);
document.getElementById('day').addEventListener('change', generateTimeSlots);

function generateTimeSlots() {
  const startTimeInput = document.getElementById('startTime').value;
  const endTimeInput = document.getElementById('endTime').value;
  
  if (!startTimeInput || !endTimeInput) return;
  
  const timeSlotsContainer = document.getElementById('timeSlots').querySelector('.time-slots-grid');
  timeSlotsContainer.innerHTML = '';
  
  const [startHours, startMinutes] = startTimeInput.split(':').map(Number);
  const [endHours, endMinutes] = endTimeInput.split(':').map(Number);
  
  const startTime = new Date();
  startTime.setHours(startHours, startMinutes, 0);
  
  const endTime = new Date();
  endTime.setHours(endHours, endMinutes, 0);
  
  if (startTime >= endTime) {
    alert('Start time must be before end time');
    return;
  }
  
  while (startTime < endTime) {
    const timeString = startTime.toLocaleTimeString('en-US', { 
      hour: '2-digit', 
      minute: '2-digit',
      hour12: true 
    });
    
    const slotDiv = document.createElement('div');
    slotDiv.className = 'time-slot-chip';
    const timeId = timeString.replace(/\s/g, '');
    slotDiv.innerHTML = `
      <input type="checkbox" name="time_slots[]" 
             value="${timeString}" id="slot_${timeId}">
      <label for="slot_${timeId}">
        ${timeString}
      </label>
    `;
    
    slotDiv.addEventListener('click', function() {
      const checkbox = this.querySelector('input[type="checkbox"]');
      checkbox.checked = !checkbox.checked;
      this.classList.toggle('selected', checkbox.checked);
    });
    
    timeSlotsContainer.appendChild(slotDiv);
    startTime.setMinutes(startTime.getMinutes() + 30);
  }
}

document.getElementById('addSlotForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  const saveBtn = document.getElementById('saveScheduleBtn');
  saveBtn.disabled = true;
  saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
  
  const formData = new FormData(this);
  const selectedSlots = Array.from(formData.getAll('time_slots[]'));
  const slotId = formData.get('slot_id');
  
  const data = {
    id: slotId,
    clinic_id: formData.get('clinic_id'),
    service_id: formData.get('service_id'),
    status: parseInt(formData.get('status')),
    day: formData.get('day'),
    time_slots: selectedSlots
  };
  
  const url = slotId ? `/services-schedule/${slotId}` : '/services-schedule';
  const method = 'POST';
  
  fetch(url, {
    method: method,
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(data)
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      location.reload();
    } else {
      alert('Error saving schedule: ' + data.message);
      saveBtn.disabled = false;
      saveBtn.innerHTML = 'Save Schedule';
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred while saving the schedule');
    saveBtn.disabled = false;
    saveBtn.innerHTML = 'Save Schedule';
  });
});

function deleteSlot(id) {
  if (confirm('Are you sure you want to delete this slot?')) {
    fetch(`/services-schedule/${id}/delete`, {
      method: 'GET',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        location.reload();
      } else {
        alert('Error deleting slot: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred while deleting the slot');
    });
  }
}

// Generate time slots when page loads
window.addEventListener('load', function() {
  document.getElementById('startTime').value = '08:00';
  document.getElementById('endTime').value = '20:00';
});
</script>

@endsection
