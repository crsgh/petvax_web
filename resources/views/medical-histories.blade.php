@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                @php
                    $petHistories = [];
                    foreach($medicalHistories as $history) {
                        if ($history->pet) {
                            $petId = $history->pet->id;
                            $petName = $history->pet->name;
                            $ownerName = $history->pet->owner ? $history->pet->owner->name : 'Unknown Owner';
                            $petOwnerKey = $petName . '-' . $ownerName;
                            
                            if (!isset($petHistories[$petOwnerKey])) {
                                $petHistories[$petOwnerKey] = [
                                    'pet' => $history->pet,
                                    'count' => 0,
                                    'histories' => [],
                                    'pet_id' => $petId
                                ];
                            }
                            $petHistories[$petOwnerKey]['count']++;
                            $petHistories[$petOwnerKey]['histories'][] = $history;
                        }
                    }
                @endphp
              
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6>Medical Histories</h6>
                        @if(auth()->user()->role_id != 4)
                            <button class="btn btn-primary btn-sm mb-0" onclick="openSidebar()">
                                <i class="fas fa-plus"></i>&nbsp;&nbsp;Add Medical Record
                            </button>
                        @endif
                    </div>
                    <!-- Filters Section -->
<div class="px-3 pt-3">
    <div class="row g-2">
        <div class="col-md-3">
            <input type="text" id="filterPetName" class="form-control" placeholder="Pet Name">
        </div>
        <div class="col-md-3">
            <input type="text" id="filterOwnerName" class="form-control" placeholder="Owner's Name">
        </div>
        <div class="col-md-2">
            <select id="filterSpecies" class="form-select">
                <option value="">All Species</option>
              
                @foreach($species as $specie)
                    <option value="{{ $specie->name }}">{{ $specie->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select id="filterGender" class="form-select">
                <option value="">All Genders</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>
        <div class="col-md-2 d-grid">
            <button class="btn btn-outline-secondary btn-sm" onclick="resetFilters()">Reset</button>
        </div>
    </div>
</div>
                    <div class="card-body pt-3 pb-2">
                        <div class="row" id="medicalHistoriesGrid">
                            @foreach($petHistories as $petOwnerKey => $data)
                            <div class="col-xl-4 col-md-6 mb-4 medical-history-card" 
                                data-pet-name="{{ strtolower($data['pet']->name) }}" 
                                data-owner-name="{{ $data['pet']->owner ? strtolower($data['pet']->owner->name) : '' }}" 
                                data-species="{{ strtolower($data['pet']->species ?? '') }}" 
                                data-gender="{{ strtolower($data['pet']->gender ?? '') }}">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-header pb-0 d-flex align-items-center">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $data['pet']->image ? asset('storage/' . $data['pet']->image) : '../assets/img/dog.png' }}" 
                                                class="avatar avatar-sm me-3" 
                                                alt="{{ $data['pet']->name }}">
                                            <div>
                                                <h6 class="mb-0 text-sm">{{ $data['pet']->name }}</h6>
                                                <p class="text-xs text-secondary mb-0">Owner: {{ $data['pet']->owner ? $data['pet']->owner->name : 'Unknown Owner' }}</p>
                                                <span class="badge bg-gradient-info mt-1">{{ $data['count'] }} Records</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body pt-2">
                                        <div class="mb-3">
                                            <p class="text-xs font-weight-bold mb-0">Latest Diagnosis:</p>
                                            <p class="text-xs text-secondary mb-0">{{ Str::limit($data['histories'][0]->diagnosis, 100) }}</p>
                                        </div>
                                        
                                        <div class="row mb-2">
                                            <div class="col-6">
                                                <p class="text-xs font-weight-bold mb-0">Latest Treatment:</p>
                                                <p class="text-xs text-secondary mb-0">{{ $data['histories'][0]->treatment_date }}</p>
                                            </div>
                                            <div class="col-6">
                                                <p class="text-xs font-weight-bold mb-0">Species/Gender:</p>
                                                <p class="text-xs text-secondary mb-0">
                                                    {{ $data['pet']->species }} / {{ $data['pet']->gender }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer pt-0">
                                        <div class="d-flex justify-content-center">
                                            <button class="btn btn-sm btn-outline-info w-100"
                                                    onclick="viewPetHistories('{{ $data['pet_id'] }}')"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="View All Records">
                                                <i class="fas fa-eye me-1"></i> View All Records
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
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
<div class="offcanvas offcanvas-end" tabindex="-1" id="addClinicSidebar" style="width: 600px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="sidebarTitle">Add Medical Record</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close">
            <span aria-hidden="true" class="text-3xl">&times;</span>
        </button>
    </div>
    <div class="offcanvas-body">
        <form id="addMedicalHistoryForm" action="" method="POST" class="needs-validation" novalidate>
            @csrf
            <input type="hidden" id="recordId" name="record_id">
            
            <!-- Pet Selection -->
            <div class="mb-3">
                <label for="petSelect" class="form-label">Select Pet <span class="text-danger">*</span></label>
                <select class="form-select select2" id="petSelect" name="pet_id" required data-error="Please select a pet">
                    <option value="" disabled selected>Choose a pet</option>
                    @foreach($pets as $pet)
                        <option value="{{ $pet->id }}">{{ $pet->name ?? 'Unnamed Pet' }} - {{ $pet->owner ? $pet->owner->name : 'Unknown Owner' }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback">Please select a pet</div>
            </div>

            <!-- Clinic Selection -->
            @if(auth()->user()->role_id == 1)
                <div class="mb-3">
                    <label for="clinicSelect" class="form-label">Select Clinic <span class="text-danger">*</span></label>
                    <select class="form-select select2" id="clinicSelect" name="clinic_id" required data-error="Please select a clinic">
                        <option value="" disabled selected>Choose a clinic</option>
                        @foreach($clinics as $clinic)
                            <option value="{{ $clinic->id }}">{{ $clinic->name ?? 'Unnamed Clinic' }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback">Please select a clinic</div>
                </div>
            @else
                <input type="hidden" name="clinic_id" value="{{ auth()->user()->clinic_id }}">
            @endif

            <!-- Diagnosis -->
            <div class="mb-3">
                <label for="diagnosis" class="form-label">Diagnosis <span class="text-danger">*</span></label>
                <textarea class="form-control" id="diagnosis" name="diagnosis" rows="3" required 
                          minlength="10" maxlength="1000" 
                          data-error="Please provide a valid diagnosis"></textarea>
                <div class="invalid-feedback" id="diagnosisFeedback">Please provide a diagnosis (minimum 10 characters)</div>
            </div>

            <!-- Treatment -->
            <div class="mb-3">
                <label for="treatment" class="form-label">Treatment <span class="text-danger">*</span></label>
                <textarea class="form-control" id="treatment" name="treatment" rows="3" required 
                          minlength="10" maxlength="1000"
                          data-error="Please provide valid treatment details"></textarea>
                <div class="invalid-feedback" id="treatmentFeedback">Please provide treatment details (minimum 10 characters)</div>
            </div>

            <!-- Inventory Selection -->
            <div class="mb-4">
                <label class="form-label fw-bold">Selected Inventory Items</label>
                <div class="table-responsive mb-3">
                    <table class="table table-hover table-bordered" id="selectedInventoryTable">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">Item Name</th>
                                <th class="text-center" style="width: 20%;">Available Stock</th>
                                <th class="text-center" style="width: 20%;">Quantity</th>
                                <th class="text-center" style="width: 15%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

                <div class="card shadow-sm p-3 bg-light">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <select class="form-select select2" id="inventorySelect">
                                <option value="" selected disabled>Select inventory item...</option>
                                @foreach($inventories as $inventory)
                                    <option value="{{ $inventory->id }}" 
                                            data-name="{{ $inventory->name ?? 'Unknown Item' }}"
                                            data-quantity="{{ $inventory->quantity ?? 0 }}"
                                            {{ ($inventory->quantity ?? 0) <= 0 ? 'disabled' : '' }}>
                                        {{ $inventory->name ?? 'Unknown Item' }} 
                                        <small class="text-muted">(Available: {{ $inventory->quantity ?? 0 }})</small>
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">Qty</span>
                                <input type="number" class="form-control" id="quantityInput" min="1" value="1">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary w-100" type="button" onclick="addInventoryItem()">
                                <i class="fas fa-plus-circle me-1"></i> Add Item
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Hidden input to store selected inventory data -->
                <input type="hidden" id="selectedInventories" name="selected_inventories" value="">
            </div>

            <script>
                let selectedItems = [];

                function addInventoryItem() {
                    const select = document.getElementById('inventorySelect');
                    const quantityInput = document.getElementById('quantityInput');
                    const option = select.options[select.selectedIndex];

                    if (!select.value) return;

                    const itemId = select.value;
                    const itemName = option.dataset.name;
                    const availableQuantity = parseInt(option.dataset.quantity);
                    const requestedQuantity = parseInt(quantityInput.value);

                    if (requestedQuantity > availableQuantity) {
                        alert('Requested quantity exceeds available stock!');
                        return;
                    }

                    // Check if item already exists
                    const existingItem = selectedItems.find(item => item.id === itemId);
                    if (existingItem) {
                        alert('This item is already added!');
                        return;
                    }

                    selectedItems.push({
                        id: itemId,
                        name: itemName,
                        quantity: requestedQuantity,
                        available: availableQuantity
                    });

                    updateTable();
                    updateHiddenInput();
                }

                function removeItem(itemId) {
                    selectedItems = selectedItems.filter(item => item.id !== itemId);
                    updateTable();
                    updateHiddenInput();
                }

                function updateTable() {
                    const tbody = document.querySelector('#selectedInventoryTable tbody');
                    tbody.innerHTML = '';

                    selectedItems.forEach(item => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${item.name}</td>
                            <td>${item.available}</td>
                            <td>${item.quantity}</td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm" 
                                        onclick="removeItem('${item.id}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });
                }

                function updateHiddenInput() {
                    document.getElementById('selectedInventories').value = JSON.stringify(selectedItems);
                }
            </script>

            <!-- Treatment Date -->
            <div class="mb-3">
                <label for="treatmentDate" class="form-label">Treatment Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="treatmentDate" name="treatment_date" required 
                       max="{{ date('Y-m-d') }}" min="{{ date('Y-m-d', strtotime('-1 year')) }}"
                       data-error="Please select a valid treatment date">
                <div class="invalid-feedback">Please select a valid treatment date (within the last year and not in the future)</div>
            </div>

            <!-- Veterinarian Selection -->
            <div class="mb-3">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="useVetList" onchange="toggleVetInput()">
                    <label class="form-check-label" for="useVetList">Select from veterinarian list</label>
                </div>
                
                <label for="vetInCharge" class="form-label">Veterinarian <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="vetInCharge" name="veterinarian" required 
                       pattern="^Dr\.\s[A-Za-z\s]{2,50}$" 
                       title="Must start with 'Dr.' followed by name"
                       data-error="Please enter a valid veterinarian name">
                <div class="invalid-feedback">Please enter veterinarian name starting with 'Dr.' (e.g., Dr. John Smith)</div>
                <input type="hidden" id="vetId" name="vet_id" value="">

                <select class="form-select d-none" id="vetSelect" name="vet_id" data-error="Please select a veterinarian">
                    <option value="" disabled selected>Choose a veterinarian</option>
                    @foreach($veterinarians as $vet)
                        <option value="{{ $vet->id }}">Dr. {{ $vet->name ?? 'Unknown' }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback">Please select a veterinarian</div>
            </div>

            <!-- Additional Notes -->
            <div class="mb-3">
                <label for="notes" class="form-label">Additional Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="2" maxlength="500"></textarea>
                <div class="invalid-feedback">Notes cannot exceed 500 characters</div>

            </div>

            @push('styles')
                <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
            @endpush

            @push('scripts')
                <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
                <script>
                    $(document).ready(function() {
                        // Initialize Select2
                        $('.select2').select2({
                            placeholder: 'Select an option',
                            allowClear: true,
                            width: '100%'
                        });

                        const form = document.getElementById('addMedicalHistoryForm');
                        const submitBtn = document.getElementById('submitBtn');
                        const formElements = {
                            notes: document.getElementById('notes'),
                            diagnosis: document.getElementById('diagnosis'),
                            treatment: document.getElementById('treatment'),
                            petSelect: document.getElementById('petSelect'),
                            clinicSelect: document.getElementById('clinicSelect'),
                            treatmentDate: document.getElementById('treatmentDate'),
                            vetInCharge: document.getElementById('vetInCharge'),
                            vetSelect: document.getElementById('vetSelect'),
                            inventorySelect: document.getElementById('inventorySelect')
                        };

                        // Character count updates
                        ['notes', 'diagnosis', 'treatment'].forEach(field => {
                            const element = formElements[field];
                            const countDisplay = document.getElementById(`${field}Count`);
                            if (element && countDisplay) {
                                element.addEventListener('input', function() {
                                    const count = this.value.length;
                                    const max = this.getAttribute('maxlength');
                                    countDisplay.textContent = `${count}/${max}`;
                                    
                                    // Visual feedback
                                    if (count > max) {
                                        element.classList.add('is-invalid');
                                        submitBtn.disabled = true;
                                    } else {
                                        element.classList.remove('is-invalid');
                                        validateForm();
                                    }
                                });
                            }
                        });

                        // Real-time validation
                        const validateField = (element) => {
                            if (!element) return true;

                            const value = element.value;
                            const isRequired = element.hasAttribute('required');
                            const minLength = element.getAttribute('minlength');
                            const maxLength = element.getAttribute('maxlength');
                            const pattern = element.getAttribute('pattern');

                            if (isRequired && !value) {
                                element.classList.add('is-invalid');
                                return false;
                            }

                            if (minLength && value.length < minLength) {
                                element.classList.add('is-invalid');
                                return false;
                            }

                            if (maxLength && value.length > maxLength) {
                                element.classList.add('is-invalid');
                                return false;
                            }

                            if (pattern && !new RegExp(pattern).test(value)) {
                                element.classList.add('is-invalid');
                                return false;
                            }

                            element.classList.remove('is-invalid');
                            return true;
                        };

                        // Validate entire form
                        const validateForm = () => {
                            let isValid = true;

                            Object.values(formElements).forEach(element => {
                                if (element && !validateField(element)) {
                                    isValid = false;
                                }
                            });

                            // Additional date validation
                            const today = new Date();
                            const oneYearAgo = new Date();
                            oneYearAgo.setFullYear(today.getFullYear() - 1);
                            const selectedDate = new Date(formElements.treatmentDate.value);

                            // if (!formElements.treatmentDate.value || selectedDate > today || selectedDate < oneYearAgo) {
                            //     formElements.treatmentDate.classList.add('is-invalid');
                            //     isValid = false;
                            // }

                            submitBtn.disabled = !isValid;
                            return isValid;
                        };

                        // Form submission handler
                        form.addEventListener('submit', function(event) {
                            event.preventDefault();
                            
                            if (validateForm()) {
                                form.submit();
                            } else {
                                // Scroll to first invalid field
                                const firstInvalid = form.querySelector('.is-invalid');
                                if (firstInvalid) {
                                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                }
                            }
                        });

                        // Real-time validation on input
                        Object.values(formElements).forEach(element => {
                            if (element) {
                                ['input', 'change', 'blur'].forEach(eventType => {
                                    element.addEventListener(eventType, () => {
                                        validateField(element);
                                        validateForm();
                                    });
                                });
                            }
                        });

                        // Initial validation
                        validateForm();
                    });
                </script>
            @endpush

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary" id="submitBtn" >Save Medical Record</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('addMedicalHistoryForm');
        const submitBtn = document.getElementById('submitBtn');

        const validateField = (field) => {
            if (!field) return true;
            const isValid = field.checkValidity();
            field.classList.toggle('is-invalid', !isValid);
            return isValid;
        };

        const validateForm = () => {
            const fields = form.querySelectorAll('[required], [minlength], [maxlength], [pattern]');
            let formIsValid = true;
            fields.forEach(field => {
                if (!validateField(field)) {
                    formIsValid = false;
                }
            });
            return formIsValid;
        };

        form.addEventListener('submit', function (e) {
            if (!validateForm()) {
                e.preventDefault();
                const firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstInvalid.focus();
                }
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const petInput = document.getElementById('filterPetName');
        const ownerInput = document.getElementById('filterOwnerName');
        const speciesSelect = document.getElementById('filterSpecies');
        const genderSelect = document.getElementById('filterGender');
        const cards = document.querySelectorAll('.medical-history-card');

        function filterGrid() {
            const petName = petInput.value.toLowerCase();
            const ownerName = ownerInput.value.toLowerCase();
            const species = speciesSelect.value.toLowerCase();
            const gender = genderSelect.value.toLowerCase();

            cards.forEach(card => {
                const petData = (card.dataset.petName || '').toLowerCase();
                const ownerData = (card.dataset.ownerName || '').toLowerCase();
                const speciesData = (card.dataset.species || '').toLowerCase();
                const genderData = (card.dataset.gender || '').toLowerCase();

                const matchPet = petData.includes(petName);
                const matchOwner = ownerData.includes(ownerName);
                const matchSpecies = !species || species === speciesData;
                const matchGender = !gender || gender === genderData;

                card.style.display = (matchPet && matchOwner && matchSpecies && matchGender) ? '' : 'none';
            });
        }

        petInput.addEventListener('input', filterGrid);
        ownerInput.addEventListener('input', filterGrid);
        speciesSelect.addEventListener('change', filterGrid);
        genderSelect.addEventListener('change', filterGrid);
    });

    function resetFilters() {
        document.getElementById('filterPetName').value = '';
        document.getElementById('filterOwnerName').value = '';
        document.getElementById('filterSpecies').value = '';
        document.getElementById('filterGender').value = '';
        document.querySelectorAll('.medical-history-card').forEach(card => card.style.display = '');
    }
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
                vetIdInput.value = '';
            });
        }
    });

    function prepareFollowUp(petId, petName, historyId) {
        document.getElementById('followUpPetId').value = petId;
        document.getElementById('followUpHistoryId').value = historyId;
        document.getElementById('followUpPetName').value = petName;
    }
</script>

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
        
        document.getElementById('recordId').value = id;
        document.getElementById('petSelect').value = petId;
        @if(auth()->user()->role_id == 1)
            document.getElementById('clinicSelect').value = clinicId;
        @endif
        document.getElementById('diagnosis').value = diagnosis;
        document.getElementById('treatment').value = treatment;
        document.getElementById('treatmentDate').value = treatmentDate;
        document.getElementById('inventorySelect').value = inventory;
        
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
            document.getElementById('useVetList').checked = false;
            toggleVetInput();
            document.getElementById('vetInCharge').value = veterinarian;
        }
        
        document.getElementById('notes').value = notes;

        if (typeof jQuery !== 'undefined' && jQuery('#petSelect').data('select2')) {
            jQuery('#petSelect').val(petId).trigger('change');
        }
        @if(auth()->user()->role_id == 1)
            if (typeof jQuery !== 'undefined' && jQuery('#clinicSelect').data('select2')) {
                jQuery('#clinicSelect').val(clinicId).trigger('change');
            }
        @endif

        document.getElementById('addMedicalHistoryForm').action = "/medical-histories/" + id;
        
        let methodField = document.querySelector('input[name="_method"]');
        if (!methodField) {
            methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            document.getElementById('addMedicalHistoryForm').appendChild(methodField);
        }
    }

    function deleteHistory(id) {
        if (confirm('Are you sure you want to delete this medical record?')) {
            // Route is GET /medical-histories/{id}/delete; navigating shows
            // the success toast from the redirect.
            window.location.href = `/medical-histories/${id}/delete`;
        }
    }
</script>

<!-- View Medical Record Modal -->
<div class="modal fade" id="viewMedicalRecordModal" tabindex="-1" aria-labelledby="viewMedicalRecordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewMedicalRecordModalLabel">Medical Record Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <img id="recordModalPetImage" src="../assets/img/dog.png" class="avatar avatar-xl rounded-circle mb-3" alt="Pet Image">
                                <h5 id="recordModalPetName" class="mb-0">Pet Name</h5>
                                <p id="recordModalPetOwner" class="text-muted">Owner Name</p>
                                <div class="d-flex justify-content-between mt-3">
                                    <span id="recordModalPetSpecies" class="badge bg-gradient-primary">Species</span>
                                    <span id="recordModalPetGender" class="badge bg-gradient-secondary">Gender</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header pb-0">
                                <h6>Medical Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p class="text-sm mb-0"><strong>Treatment Date:</strong></p>
                                        <p id="recordModalTreatmentDate" class="text-sm text-dark">-</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="text-sm mb-0"><strong>Veterinarian:</strong></p>
                                        <p id="recordModalVeterinarian" class="text-sm text-dark">-</p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p class="text-sm mb-0"><strong>Clinic:</strong></p>
                                        <p id="recordModalClinic" class="text-sm text-dark">-</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="text-sm mb-0"><strong>Follow-up Date:</strong></p>
                                        <p id="recordModalFollowup" class="text-sm text-dark">-</p>
                                    </div>
                                </div>
                                <hr class="horizontal dark">
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <p class="text-sm mb-1"><strong>Diagnosis:</strong></p>
                                        <p id="recordModalDiagnosis" class="text-sm text-dark">-</p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <p class="text-sm mb-1"><strong>Treatment:</strong></p>
                                        <p id="recordModalTreatment" class="text-sm text-dark">-</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <p class="text-sm mb-1"><strong>Notes:</strong></p>
                                        <p id="recordModalNotes" class="text-sm text-dark">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Inventory Items Used</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Item Name</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Quantity Used</th>
                                    </tr>
                                </thead>
                                <tbody id="recordModalInventoryTableBody">
                                    <!-- Inventory items will be inserted here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Pet History Modal -->
<div class="modal fade" id="petHistoryModal" tabindex="-1" aria-labelledby="petHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="petHistoryModalLabel">Pet Medical History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <img id="modalPetImage" src="../assets/img/dog.png" class="avatar avatar-xl rounded-circle mb-3" alt="Pet Image">
                                <h5 id="modalPetName" class="mb-0">Pet Name</h5>
                                <p id="modalPetOwner" class="text-muted">Owner Name</p>
                                <div class="d-flex justify-content-between mt-3">
                                    <span id="modalPetSpecies" class="badge bg-gradient-primary">Species</span>
                                    <span id="modalPetGender" class="badge bg-gradient-secondary">Gender</span>
                                    <span id="modalRecordCount" class="badge bg-gradient-info">0 Records</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Diagnosis</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Treatment</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Treatment Date</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Veterinarian</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Follow-up</th>
                                    </tr>
                                </thead>
                                <tbody id="modalHistoryTableBody">
                                    <!-- History records will be inserted here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Store pet histories data for modal display
    const petHistoriesData = @json($petHistories);
    
    function viewPetHistories(petId) {
        // Find the pet data by pet_id
        let petData = null;
        for (const key in petHistoriesData) {
            if (petHistoriesData[key].pet_id === petId) {
                petData = petHistoriesData[key];
                break;
            }
        }
        if (!petData) return;
        
        const pet = petData.pet;
        const histories = petData.histories;
        
        // Update modal header information
        document.getElementById('modalPetName').textContent = pet.name || 'Unknown Pet';
        document.getElementById('modalPetOwner').textContent = pet.owner ? pet.owner.name : 'Unknown Owner';
        document.getElementById('modalPetSpecies').textContent = pet.species || 'Unknown';
        document.getElementById('modalPetGender').textContent = pet.gender || 'Unknown';
        document.getElementById('modalRecordCount').textContent = `${petData.count} Records`;
        
        // Set pet image
        const petImage = document.getElementById('modalPetImage');
        petImage.src = pet.image ? `/storage/${pet.image}` : '../assets/img/dog.png';
        petImage.alt = pet.name || 'Pet';
        
        // Populate history table
        const tableBody = document.getElementById('modalHistoryTableBody');
        tableBody.innerHTML = '';
        
        histories.forEach(history => {
            const row = document.createElement('tr');
            
            // Diagnosis cell
            const diagnosisCell = document.createElement('td');
            diagnosisCell.innerHTML = `
                <div class="d-flex px-2 py-1">
                    <div class="d-flex flex-column justify-content-center">
                        <p class="text-xs text-secondary mb-0">${history.diagnosis}</p>
                    </div>
                </div>
            `;
            
            // Treatment cell
            const treatmentCell = document.createElement('td');
            treatmentCell.innerHTML = `
                <p class="text-xs text-secondary mb-0">${history.treatment}</p>
            `;
            
            // Treatment date cell
            const dateCell = document.createElement('td');
            dateCell.className = 'align-middle text-center';
            dateCell.innerHTML = `
                <span class="text-secondary text-xs font-weight-bold">${history.treatment_date}</span>
            `;
            
            // Veterinarian cell
            const vetCell = document.createElement('td');
            vetCell.className = 'align-middle text-center text-sm';
            
            let vetName = 'N/A';
            if (history.veterinarian && history.veterinarian.name) {
                vetName = `Dr. ${history.veterinarian.name}`;
            } else if (history.attending_vet) {
                vetName = `Dr. ${history.attending_vet}`;
            }
            
            vetCell.innerHTML = `
                <span class="text-secondary text-xs font-weight-bold">${vetName}</span>
            `;
            
            // Follow-up cell
            const followupCell = document.createElement('td');
            followupCell.className = 'align-middle text-center text-sm';
            
            if (history.followup) {
                followupCell.innerHTML = `
                    <span class="badge badge-sm bg-gradient-info">${history.followup}</span>
                `;
            } else {
                followupCell.innerHTML = `
                    <span class="badge badge-sm bg-gradient-secondary">No Follow-up</span>
                `;
            }
            
            // Append all cells to the row
            row.appendChild(diagnosisCell);
            row.appendChild(treatmentCell);
            row.appendChild(dateCell);
            row.appendChild(vetCell);
            row.appendChild(followupCell);
            
            // Append the row to the table body
            tableBody.appendChild(row);
        });
        
        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('petHistoryModal'));
        modal.show();
    }
</script>

@endsection
