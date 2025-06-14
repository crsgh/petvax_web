@extends('layouts.user_type.auth')

@section('content')

  <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Bookings Table</h6>
                <button class="btn btn-primary btn-sm" onclick="openSidebar()">
                  <i class="fas fa-plus"></i>&nbsp;&nbsp;Add New Booking
                </button>
              </div>
              
              <!-- Filter Section -->
              <div class="row g-3 align-items-center mb-2">
                <div class="col-md-2">
                  <select class="form-select form-select-sm" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="declined">Declined</option>
                  </select>
                </div>
                @if(auth()->user()->role_id == 1)
                <div class="col-md-2">
                  <select class="form-select form-select-sm" id="clinicFilter">
                    <option value="">All Clinics</option>
                    @foreach($clinics as $clinic)
                      <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
                    @endforeach
                  </select>
                </div>
                @endif
                <div class="col-md-2">
                  <input type="date" class="form-control form-control-sm" id="dateFilter" placeholder="Filter by date">
                </div>
                <div class="col-md-2 d-flex flex-col mt-1">
                  <div>&nbsp;</div>
                  <button class="btn btn-secondary btn-sm" onclick="resetFilters()">Reset Filters</button>
                </div>
              </div>
            </div>

            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Pet Name</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Service</th>
                      @if(auth()->user()->role_id == 1)
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Clinic</th>
                      @endif
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date & Time</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Payment Method</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="bookingsTableBody">
                    @foreach($bookings as $booking)
                    <tr class="booking-row" 
                        data-status="{{ $booking->status }}"
                        data-clinic="{{ $booking->clinic_id }}"
                        data-date="{{ \Carbon\Carbon::parse($booking->appointment_datetime)->format('Y-m-d') }}">
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">{{ $booking->pet->name }}</h6>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-xs text-secondary mb-0">{{ $booking->service->name }}</p>
                      </td>
                      @if(auth()->user()->role_id == 1)
                      <td>
                        <p class="text-xs text-secondary mb-0">{{ $booking->clinic->name }}</p>
                      </td>
                      @endif
                      <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">
                          {{ \Carbon\Carbon::parse($booking->appointment_datetime)->format('M d, Y g:i A') }}
                      </span>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <div class="d-flex flex-column align-items-center">
                          <span class="text-secondary text-xs font-weight-bold">{{ $booking->payment_method }}</span>
                          @if($booking->payment_method == 'gcash')
                            <button type="button" class="btn btn-link btn-sm p-0 mt-1" data-bs-toggle="modal" data-bs-target="#paymentProofModal_{{ $booking->id }}">
                              View Proof
                            </button>

                            <!-- Payment Proof Modal -->
                            <div class="modal fade" id="paymentProofModal_{{ $booking->id }}" tabindex="-1" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered" style="max-width:400px">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title">Payment Proof<br><span class="text-muted">Reference #{{ $booking->payment_reference }}</span></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <div class="modal-body text-center p-0">
                                    <div style="width: 100%; height: 700px; position: relative; overflow: hidden;">
                                      <img src="{{ asset('storage/' . $booking->payment_proof) }}" 
                                           style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);" 
                                           alt="Payment Proof">
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          @endif
                        </div>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="badge badge-sm 
                          @switch($booking->status)
                              @case('completed')
                                  bg-gradient-success
                                  @break
                              @case('cancel')
                                  bg-gradient-danger
                                  @break
                              @case('decline') 
                                  bg-gradient-warning
                                  @break
                              @case('confirmed')
                                  bg-gradient-info
                                  @break
                              @default
                                  bg-gradient-secondary
                          @endswitch
                        ">
                          {{ ucfirst($booking->status) }}
                        </span>
                      </td>
                      <td class="align-middle text-center">
                        <div class="d-flex gap-2 justify-content-center">
                          <select class="form-select form-select-sm" style="width: auto;" id="actionSelect_{{ $booking->id }}" onchange="handleAction(this.value, {{ $booking->id }})">
                            <option value="" selected disabled>Select Action</option>
                            @if($booking->status == 'pending')
                              <option value="confirmed">Approve Booking</option>
                              <option value="declined">Decline Booking</option>
                              <option value="edit">Edit Booking</option>
                              <option value="delete">Delete Booking</option>
                            @elseif($booking->status == 'confirmed') 
                              <option value="completed">Complete Booking</option>
                              <option value="cancelled">Cancel Booking</option>
                            @elseif($booking->status == 'declined')
                              <option value="delete">Delete Booking</option>
                            @elseif($booking->status == 'cancelled')
                              <option value="delete">Delete Booking</option>
                            @endif
                          </select>
                          
                          <form id="updateForm_{{ $booking->id }}" action="/bookings/action" method="POST" style="display: none;">
                            @csrf
                            <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                            <input type="hidden" name="action" id="actionInput_{{ $booking->id }}">
                          </form>
                        </div>

                        <!-- Modal for completing booking -->
                        <div class="modal fade" id="completeBookingModal_{{ $booking->id }}" tabindex="-1">
                          <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title">Complete Booking</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                              </div>
                              <div class="modal-body">
                                <form id="completeBookingForm_{{ $booking->id }}" action="/bookings/complete/{{ $booking->id }}" method="POST">
                                  @csrf


                                  <div class="mb-3">
                                    <label for="diagnosis_{{ $booking->id }}" class="form-label">Diagnosis</label>
                                    <input type="text" class="form-control" id="diagnosis_{{ $booking->id }}" name="diagnosis" required>
                                  </div>

                                  <div class="mb-3">
                                    <label for="treatment_{{ $booking->id }}" class="form-label">Treatment</label>
                                    <input type="text" class="form-control" id="treatment_{{ $booking->id }}" name="treatment" required>
                                  </div>

                                  <div class="mb-3">
                                    <label for="inventoryItems_{{ $booking->id }}" class="form-label">Inventory Items Used</label>
                                    <select class="form-select" id="inventoryItems_{{ $booking->id }}" name="inventory_id" required>
                                      <option value="" selected disabled>Select an item</option>
                                      @foreach($inventoryItems as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                      @endforeach
                                    </select>
                                  </div>

                                

                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary" onclick="submitCompletedBooking({{ $booking->id }})">Complete Booking</button>
                                  </div>
                                </form>
                              </div>
                            </div>
                          </div>
                        </div>
                        <!-- Modal for assigning staff -->
                        <div class="modal fade" id="assignStaffModal_{{ $booking->id }}" tabindex="-1">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title">Assign Staff</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                              </div>
                              <div class="modal-body">
                                <select class="form-select" id="staffSelect_{{ $booking->id }}" >
                                  <option value="" selected disabled>Select Staff</option>
                                  @foreach($veterinarians as $vet)
                                    <option value="{{ $vet->id }}">{{ $vet->name }}</option>
                                  @endforeach
                                </select>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" onclick="confirmWithStaff({{ $booking->id }})">Confirm</button>
                              </div>
                            </div>
                          </div>
                        </div>

                      
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

  <!-- Add/Edit Booking Sidebar -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="addClinicSidebar" style="width: 600px;">
    <div class="offcanvas-header border-bottom">
      <h5 class="offcanvas-title" id="sidebarTitle">Add New Booking</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close">
        <span aria-hidden="true" class="text-3xl">&times;</span>
      </button>
    </div>
    <div class="offcanvas-body">
      <form id="bookingForm" action="" method="POST">
        @csrf
        <input type="hidden" id="bookingId" name="booking_id">
        <div class="mb-3">
          <label for="clinicSelect" class="form-label">Select Clinic</label>
          <select class="form-select" id="clinicSelect" required onchange="loadPetsAndServicesAndStaff()" name="clinic_id">
            <option value="" selected disabled>Choose a clinic</option>
            @if(auth()->user()->role_id == 1)
              @foreach($clinics as $clinic)
                <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
              @endforeach
            @else
              @foreach($clinics as $clinic)
                @if($clinic->id == auth()->user()->clinic_id)
                  <option value="{{ $clinic->id }}" selected>{{ $clinic->name }}</option>
                @endif
              @endforeach
            @endif
          </select>
        </div>
        <div class="mb-3">
          <label for="petSelect" class="form-label">Select Pet</label>
          <select class="form-select" id="petSelect" name="pet_id" required disabled>
            <option value="" selected disabled>Choose a pet</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="serviceSelect" class="form-label">Select Service</label>
          <select class="form-select" id="serviceSelect" name="service_id" required disabled>
            <option value="" selected disabled>Choose a service</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="staffSelect" class="form-label">Assign Staff</label>
          <select class="form-select" id="staffSelect" name="staff_id" required disabled>
            <option value="" selected disabled>Choose a staff member</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="bookingNotes" class="form-label">Notes</label>
          <textarea class="form-control" id="bookingNotes" name="notes" rows="3"></textarea>
        </div>
        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary" id="submitBtn">Save Booking</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Existing functions remain the same
    function openSidebar(isEdit = false) {
      document.getElementById('sidebarTitle').textContent = isEdit ? 'Edit Booking' : 'Add New Booking';
      var form = document.getElementById('bookingForm');
      form.reset();
      document.getElementById('bookingId').value = '';

      @if(auth()->user()->role_id != 1)
        document.getElementById('clinicSelect').value = '{{ auth()->user()->clinic_id }}';
        loadPetsAndServicesAndStaff();
      @endif
      
      var sidebar = new bootstrap.Offcanvas(document.getElementById('addClinicSidebar'));
      sidebar.show();
    }

    async function openEditSidebar(booking) {
      var form = document.getElementById('bookingForm');
      form.action = `/bookings/${booking.id}`;
      openSidebar(true);
      
      document.getElementById('clinicSelect').value = booking.clinic_id;
      await loadPetsAndServicesAndStaff();
      
      document.getElementById('bookingId').value = booking.id;
      document.getElementById('bookingDateTime').value = booking.datetime;
      document.getElementById('bookingNotes').value = booking.notes;
      document.getElementById('petSelect').value = booking.pet_id;
      document.getElementById('serviceSelect').value = booking.service_id;
      document.getElementById('staffSelect').value = booking.staff_id;
    }

    function closeSidebar() {
      var sidebar = bootstrap.Offcanvas.getInstance(document.getElementById('addClinicSidebar'));
      sidebar.hide();
    }

    async function loadPetsAndServicesAndStaff() {
      const clinicId = document.getElementById('clinicSelect').value;
      const petSelect = document.getElementById('petSelect');
      const serviceSelect = document.getElementById('serviceSelect');
      const staffSelect = document.getElementById('staffSelect');

      if (!clinicId) {
        petSelect.disabled = true;
        serviceSelect.disabled = true;
        staffSelect.disabled = true;
        return;
      }

      try {
        const [petsResponse, servicesResponse, staffsResponse] = await Promise.all([
          fetch(`/api/clinics/${clinicId}/pets`).then(response => response.json()),
          fetch(`/api/clinics/${clinicId}/services`).then(response => response.json()),
          fetch(`/api/clinics/${clinicId}/staffs`).then(response => response.json())
        ]);

        petSelect.innerHTML = '<option value="" selected disabled>Choose a pet</option>';
        petsResponse.forEach(pet => {
          petSelect.innerHTML += `<option value="${pet.id}">${pet.name}</option>`;
        });
        petSelect.disabled = false;

        serviceSelect.innerHTML = '<option value="" selected disabled>Choose a Service</option>';
        servicesResponse.forEach(service => {
          serviceSelect.innerHTML += `<option value="${service.id}">${service.name}</option>`;
        });
        serviceSelect.disabled = false;

        staffSelect.innerHTML = '<option value="" selected disabled>Choose a Staff</option>';
        staffsResponse.forEach(staff => {
          staffSelect.innerHTML += `<option value="${staff.id}">${staff.name}</option>`;
        });
        staffSelect.disabled = false;

      } catch (error) {
        console.error('Error loading dropdown data:', error);
        alert('Failed to load dropdown data. Please try again.');
      }
    }

    function deleteBooking(bookingId) {
      if (confirm('Are you sure you want to delete this booking?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/bookings/${bookingId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
      }
    }

    // New filter functions
    function applyFilters() {
      const statusFilter = document.getElementById('statusFilter').value;
      const clinicFilter = document.getElementById('clinicFilter')?.value;
      const dateFilter = document.getElementById('dateFilter').value;
      
      const rows = document.querySelectorAll('.booking-row');
      
      rows.forEach(row => {
        let show = true;
        
        if (statusFilter && row.dataset.status !== statusFilter) {
          show = false;
        }
        
        if (clinicFilter && row.dataset.clinic !== clinicFilter) {
          show = false;
        }
        
        if (dateFilter && row.dataset.date !== dateFilter) {
          show = false;
        }
        
        row.style.display = show ? '' : 'none';
      });
    }

    function resetFilters() {
      document.getElementById('statusFilter').value = '';
      if (document.getElementById('clinicFilter')) {
        document.getElementById('clinicFilter').value = '';
      }
      document.getElementById('dateFilter').value = '';
      
      const rows = document.querySelectorAll('.booking-row');
      rows.forEach(row => row.style.display = '');
    }

    // Add event listeners for filters
    document.getElementById('statusFilter').addEventListener('change', applyFilters);
    if (document.getElementById('clinicFilter')) {
      document.getElementById('clinicFilter').addEventListener('change', applyFilters);
    }
    document.getElementById('dateFilter').addEventListener('change', applyFilters);

    // Auto-load data for non-admin users when page loads
    @if(auth()->user()->role_id != 1)
      window.addEventListener('load', function() {
        loadPetsAndServicesAndStaff();
      });
    @endif

    function handleAction(action, bookingId) {
                            if (action === 'edit') {
                              openEditSidebar({
                                id: bookingId,
                                clinic_id: '{{ $booking->clinic_id }}',
                                pet_id: '{{ $booking->pet_id }}',
                                service_id: '{{ $booking->service_id }}',
                                staff_id: '{{ $booking->staff_id }}',
                                notes: '{{ $booking->notes }}'
                              });
                            } else if (action === 'delete') {
                              deleteBooking(bookingId);
                            } else if (action === 'confirmed') {
                              if (!{{ $booking->staff_id ?? 'null' }}) {
                                const modal = new bootstrap.Modal(document.getElementById('assignStaffModal_' + bookingId));
                                modal.show();
                              } else {
                                submitAction(action, bookingId);
                              }
                            } else if (action === 'completed') {
                              const modal = new bootstrap.Modal(document.getElementById('completeBookingModal_' + bookingId));
                              modal.show();
                            } else if (action) {
                              submitAction(action, bookingId);
                            }
                          }

                          function submitAction(action, bookingId) {
                            const form = document.getElementById('updateForm_' + bookingId);
                            document.getElementById('actionInput_' + bookingId).value = action;
                            form.submit();
                          }

                          function confirmWithStaff(bookingId) {
                            const staffId = document.getElementById('staffSelect_' + bookingId).value;
                            if (!staffId) {
                              alert('Please select a staff member');
                              return;
                            }
                            // Add staff ID to form and submit    
                            const form = document.getElementById('updateForm_' + bookingId);
                            const staffInput = document.createElement('input');
                            staffInput.type = 'hidden';
                            staffInput.name = 'staff_id';
                            staffInput.value = staffId;
                            form.appendChild(staffInput);
                            submitAction('confirmed', bookingId);
                          }
  </script>
  
  @endsection
