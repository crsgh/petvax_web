@extends('layouts.user_type.auth')

@section('content')

<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12">
        <div class="card mb-4">
          <div class="card-header pb-0 d-flex justify-content-between align-items-center">
            <h6>Medical Histories</h6>
            @if(auth()->user()->role_id != 4)
            <button class="btn btn-primary btn-sm mb-0" onclick="openSidebar()">
              <i class="fas fa-plus"></i>&nbsp;&nbsp;Add Medical Record
            </button>
            @endif
          </div>
          <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Pet Info</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Diagnosis</th>
                    @if(auth()->user()->role_id == 1)
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Clinic</th>
                    @endif
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Treatment Date</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Veterinarian</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Follow-up Date</th>
                    @if(auth()->user()->role_id != 4)
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                    @endif
                  </tr>
                </thead>
                <tbody>
                  @foreach($medicalHistories as $history)
                  <tr>
                    <td>
                      <div class="d-flex px-2 py-1">
                        <div>
                            <img src="{{ $history->pet->image ? asset('storage/' . $history->pet->image) : '../assets/img/dog.png' }}" class="avatar avatar-sm me-3" alt="{{ $history->pet->name }}">
                        </div>
                        <div class="d-flex flex-column justify-content-center">
                          <h6 class="mb-0 text-sm">{{ $history->pet->name }}</h6>
                          <p class="text-xs text-secondary mb-0">Owner: {{ $history->pet->owner->name }}</p>
                        </div>
                      </div>
                    </td>
                    <td>
                      <p class="text-xs text-secondary mb-0">{{ $history->diagnosis }}</p>
                    </td>
                    @if(auth()->user()->role_id == 1)
                    <td>
                      <p class="text-xs text-secondary mb-0">{{ $history->clinic->name }}</p>
                    </td>
                    @endif
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">{{ $history->treatment_date }}</span>
                    </td>
                    <td class="align-middle text-center text-sm">
                      <span class="text-secondary text-xs font-weight-bold">
{{ $history->veterinarian ? "Dr. " . $history->veterinarian->name : ($history->attending_vet ? "Dr. " . $history->attending_vet : "N/A") }}
                      </td>
                    <td class="align-middle text-center text-sm">
                      @if($history->followup)
                        <span class="badge badge-sm bg-gradient-info">
                          {{ $history->followup }}
                        </span>
                      @else
                        <div class="d-flex align-items-center justify-content-center" style="height: 100%;">
                          <span class="badge badge-sm bg-gradient-secondary me-2" style="margin-bottom: 0;">No Follow-up</span>
                          <button type="button"
                             class="btn btn-icon-only btn-success mb-0 p-2 d-flex align-items-center justify-content-center"
                             style="width: 22px; height: 22px; border-radius: 8px;"
                             data-bs-toggle="modal"
                             data-bs-target="#followUpModal"
                             onclick="prepareFollowUp('{{ $history->pet_id }}', '{{ $history->pet->name }}', '{{ $history->id }}')"
                             title="Schedule Follow-up">
                            <i class="fas fa-plus" style="font-size: 0.75rem;"></i>
                          </button>
                        </div>
                      @endif
                    </td>
                    <td class="align-middle text-center">
                      @if(auth()->user()->role_id != 4)
                      <div class="d-flex gap-1 justify-content-center">
                        <button class="btn btn-icon-only btn-rounded btn-outline-primary mb-0 p-2 d-flex align-items-center justify-content-center" 
                                onclick="editHistory('{{ $history->id }}', '{{ $history->pet_id }}', '{{ $history->clinic_id }}', '{{ $history->diagnosis }}', '{{ $history->treatment }}', '{{ $history->treatment_date }}', '{{ $history->veterinarian }}', '{{ $history->notes }}', '{{ $history->inventory_id }}')"
                                data-bs-toggle="tooltip" 
                                data-bs-placement="top"
                                title="Edit Record">
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                          </svg>
                        </button>
                        
                        <form action="/medical-histories/{{ $history->id }}/delete" method="GET" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this medical record?')">
                          @csrf
                            <button class="btn btn-icon-only btn-rounded btn-outline-danger mb-0 p-2 d-flex align-items-center justify-content-center"
                                  data-bs-toggle="tooltip"
                                  data-bs-placement="top" 
                                  title="Delete Record">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                              <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                              <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                            </svg>
                          </button>
                        </form>
                      </div>
                      @endif
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

<!-- Follow-up Modal -->
<div class="modal fade" id="followUpModal" tabindex="-1" aria-labelledby="followUpModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="followUpModalLabel">Schedule Follow-up</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="followUpForm" action="/medical-histories/follow-up" method="POST">
        @csrf
        <div class="modal-body">
          <input type="hidden" id="followUpPetId" name="pet_id">
          <input type="hidden" id="followUpHistoryId" name="id">
          <div class="mb-3">
            <label class="form-label">Pet Name</label>
            <input type="text" class="form-control" id="followUpPetName" readonly>
          </div>
          <div class="mb-3">
            <label for="followUpDate" class="form-label">Follow-up Date</label>
            <input type="date" class="form-control" id="followUpDate" name="appointment_date" required>
          </div>
          <div class="mb-3">
            <label for="followUpTime" class="form-label">Preferred Time</label>
            <input type="time" class="form-control" id="followUpTime" name="appointment_time" required>
          </div>
          <div class="mb-3">
            <label for="followUpNotes" class="form-label">Notes</label>
            <textarea class="form-control" id="followUpNotes" name="notes" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Schedule Follow-up</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add/Edit Medical History Sidebar -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="addClinicSidebar">
    <div class="offcanvas-header border-bottom">
      <h5 class="offcanvas-title" id="sidebarTitle">Add Medical Record</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close">
        <span aria-hidden="true" class="text-3xl">&times;</span>
      </button>
    </div>
    <div class="offcanvas-body">
      <form id="addMedicalHistoryForm" action="" method="POST">
        @csrf
        <input type="hidden" id="recordId" name="record_id">
        <div class="mb-3">
          <label for="petSelect" class="form-label">Select Pet</label>
          <select class="form-select select2" id="petSelect" name="pet_id" required>
            <option value="" disabled selected>Choose a pet</option>
            @foreach($pets as $pet)
              <option value="{{ $pet->id }}">{{ $pet->name }} - {{ $pet->owner->name }}</option>
            @endforeach
          </select>
        </div>
        @if(auth()->user()->role_id == 1)
        <div class="mb-3">
          <label for="clinicSelect" class="form-label">Select Clinic</label>
          <select class="form-select select2" id="clinicSelect" name="clinic_id" required>
            <option value="" disabled selected>Choose a clinic</option>
            @foreach($clinics as $clinic)
              <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
            @endforeach
          </select>
        </div>
        @else
        <input type="hidden" name="clinic_id" value="{{ auth()->user()->clinic_id }}">
        @endif
        <div class="mb-3">
          <label for="diagnosis" class="form-label">Diagnosis</label>
          <textarea class="form-control" id="diagnosis" name="diagnosis" rows="3" required></textarea>
        </div>
        <div class="mb-3">
          <label for="treatment" class="form-label">Treatment</label>
          <textarea class="form-control" id="treatment" name="treatment" rows="3"></textarea>
        </div>
        <div class="mb-3">
          <label for="inventorySelect" class="form-label">Select Inventory Items</label>
          <select class="form-select" id="inventorySelect" name="inventory_id">
            <option value="" selected disabled>Choose inventory items</option>
            @foreach($inventories as $inventory)
              <option value="{{ $inventory->id }}">{{ $inventory->name }} (Stock: {{ $inventory->quantity }})</option>
            @endforeach
          </select>
        </div>
        <div class=" mb-3">
          <label for="treatmentDate" class="form-label">Treatment Date</label>
            <input type="date" class="form-control" id="treatmentDate" name="treatment_date" required>
        </div>
        <div class="mb-3">
          <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" id="useVetList" onchange="toggleVetInput()">
            <label class="form-check-label" for="useVetList">Select from veterinarian list</label>
          </div>
          
          <label for="vetInCharge" class="form-label">Veterinarian</label>
          <input type="text" class="form-control" id="vetInCharge" name="veterinarian" required>
          <input type="hidden" id="vetId" name="vet_id" value="">

          <select class="form-select d-none" id="vetSelect" name="vet_id">
            <option value="" disabled selected>Choose a veterinarian</option>
            @foreach($veterinarians as $vet)
              <option value="{{ $vet->id }}">Dr. {{ $vet->name }}</option>
            @endforeach
          </select>
        </div>
        <script>
          function toggleVetInput() {
            const useList = document.getElementById('useVetList').checked;
            const textInput = document.getElementById('vetInCharge');
            const selectInput = document.getElementById('vetSelect');
            const vetIdInput = document.getElementById('vetId');

            if (useList) {
              textInput.classList.add('d-none');
              textInput.removeAttribute('required');
              selectInput.classList.remove('d-none');
              selectInput.setAttribute('required', 'required');
              vetIdInput.value = selectInput.value || '';
              textInput.value = '';
            } else {
              textInput.classList.remove('d-none');
              textInput.setAttribute('required', 'required');
              selectInput.classList.add('d-none');
              selectInput.removeAttribute('required');
              vetIdInput.value = '';
              selectInput.value = '';
            }
          }

          document.addEventListener('DOMContentLoaded', function() {
            const vetSelect = document.getElementById('vetSelect');
            const vetIdInput = document.getElementById('vetId');
            const vetInput = document.getElementById('vetInCharge');
            if (vetSelect) {
              vetSelect.addEventListener('change', function() {
          vetIdInput.value = vetSelect.value;
              });
            }
            if (vetInput) {
              vetInput.addEventListener('input', function() {
          // If typing in input, ensure vet_id is null
          vetIdInput.value = '';
              });
            }
          });
        </script>
        <script>
          function toggleVetInput() {
            const useList = document.getElementById('useVetList').checked;
            const textInput = document.getElementById('vetInCharge');
            const selectInput = document.getElementById('vetSelect');
            const vetIdInput = document.getElementById('vetId');

            if (useList) {
              textInput.classList.add('d-none');
              textInput.removeAttribute('required');
              selectInput.classList.remove('d-none');
              selectInput.setAttribute('required', 'required');
              vetIdInput.value = selectInput.value || '';
            } else {
              textInput.classList.remove('d-none');
              textInput.setAttribute('required', 'required');
              selectInput.classList.add('d-none');
              selectInput.removeAttribute('required');
              vetIdInput.value = 0;
            }
          }

          // Update vetId input when select changes
          document.addEventListener('DOMContentLoaded', function() {
            const vetSelect = document.getElementById('vetSelect');
            const vetIdInput = document.getElementById('vetId');
            if (vetSelect) {
              vetSelect.addEventListener('change', function() {
                vetIdInput.value = vetSelect.value;
              });
            }
          });
        </script>

        <script>
          function toggleVetInput() {
            const useList = document.getElementById('useVetList').checked;
            const textInput = document.getElementById('vetInCharge');
            const selectInput = document.getElementById('vetSelect');
            
            if (useList) {
              textInput.classList.add('d-none');
              textInput.removeAttribute('required');
              selectInput.classList.remove('d-none');
              selectInput.setAttribute('required', 'required');
            } else {
              textInput.classList.remove('d-none');
              textInput.setAttribute('required', 'required');
              selectInput.classList.add('d-none');
              selectInput.removeAttribute('required');
            }
          }

          function prepareFollowUp(petId, petName, historyId) {
            document.getElementById('followUpPetId').value = petId;
            document.getElementById('followUpHistoryId').value = historyId;
            document.getElementById('followUpPetName').value = petName;
          }
        </script>
        <div class="mb-3">
          <label for="notes" class="form-label">Additional Notes</label>
          <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
        </div>
  
        @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        @endpush

        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
          $(document).ready(function() {
            $('.select2').select2({
              placeholder: 'Select an option',
              allowClear: true,
              width: '100%'
            });
          });
        </script>
        @endpush
        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary" id="submitBtn">Save Medical Record</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openSidebar(mode = 'add') {
      var sidebar = new bootstrap.Offcanvas(document.getElementById('addClinicSidebar'));
      document.getElementById('addMedicalHistoryForm').action = "";
      document.getElementById('sidebarTitle').textContent = mode === 'add' ? 'Add Medical Record' : 'Edit Medical Record';
      document.getElementById('submitBtn').textContent = mode === 'add' ? 'Save Medical Record' : 'Update Medical Record';
      sidebar.show();
    }

    function closeSidebar() {
      var sidebar = bootstrap.Offcanvas.getInstance(document.getElementById('addClinicSidebar'));
      sidebar.hide();
    }

function editHistory(id, petId, clinicId, diagnosis, treatment, treatmentDate, veterinarian, notes, inventory) {
    openSidebar('edit');
    
    // Populate form fields
    document.getElementById('recordId').value = id;
    document.getElementById('petSelect').value = petId;
    @if(auth()->user()->role_id == 1)
    document.getElementById('clinicSelect').value = clinicId;
    @endif
    document.getElementById('diagnosis').value = diagnosis;
    document.getElementById('treatment').value = treatment;
    document.getElementById('treatmentDate').value = treatmentDate;
    document.getElementById('inventorySelect').value = inventory;
    
    // Handle veterinarian selection
    try {
        const vetData = JSON.parse(veterinarian);
        if (vetData && vetData.id) {
            document.getElementById('useVetList').checked = true;
            toggleVetInput();
            document.getElementById('vetSelect').value = vetData.id;
            document.getElementById('vetId').value = vetData.id;
        } else {
            document.getElementById('useVetList').checked = false;
            toggleVetInput();
            document.getElementById('vetInCharge').value = veterinarian;
        }
    } catch (e) {
        // If veterinarian is not JSON, treat as plain text
        document.getElementById('useVetList').checked = false;
        toggleVetInput();
        document.getElementById('vetInCharge').value = veterinarian;
    }
    
    document.getElementById('notes').value = notes;

    // Trigger Select2 update with proper initialization check
    if (typeof jQuery !== 'undefined' && jQuery('#petSelect').data('select2')) {
        jQuery('#petSelect').val(petId).trigger('change');
    }
    @if(auth()->user()->role_id == 1)
    if (typeof jQuery !== 'undefined' && jQuery('#clinicSelect').data('select2')) {
        jQuery('#clinicSelect').val(clinicId).trigger('change');
    }
    @endif

    // Update form action for edit
    document.getElementById('addMedicalHistoryForm').action = "/medical-histories/" + id;
    
    // Add method spoofing for PUT request
    let methodField = document.querySelector('input[name="_method"]');
    if (!methodField) {
        methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        
        document.getElementById('addMedicalHistoryForm').appendChild(methodField);
    } else {
        
    }
}

    function deleteHistory(id) {
      if (confirm('Are you sure you want to delete this medical record?')) {
        // Create and submit form for deletion
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/medical-histories/${id}`;
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        const csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = '_token';
        csrfField.value = document.querySelector('meta[name="csrf-token"]').content;
        
        form.appendChild(methodField);
        form.appendChild(csrfField);
        document.body.appendChild(form);
        form.submit();
      }
    }
  </script>
  
  @endsection
