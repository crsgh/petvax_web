@extends('layouts.user_type.auth')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12">
        <div class="card mb-0">
          <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h6 class="mb-0">Available Clinics</h6>
            </div>

            <!-- Filter Section -->
            <div class="row g-3 align-items-center mb-4">
              <div class="col-md-6">
                <input type="text" class="form-control" id="searchClinic" placeholder="Search clinics...">
              </div>
              <div class="col-md-3" style="display: none;">
                <select class="form-select" id="serviceFilter">
                  <option value="">All Services</option>
                  @foreach($services as $service)
                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-3" style="display: none;">
                <select class="form-select" id="locationFilter">
                  <option value="">All Locations</option>
                  @foreach($clinics->pluck('address')->unique() as $address)
                    <option value="{{ $address }}">{{ $address }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>

          <div class="card-body">
            <div class="row g-4">
              @foreach($clinics as $clinic)
              <div class="col-md-4 clinic-card">
                <div class="card h-100">
                  <img src="{{ asset('storage/' . $clinic->image) }}" class="card-img-top" alt="{{ $clinic->name }}" style="height: 200px; object-fit: cover;">
                  <div class="card-body">
                    <h5 class="card-title">{{ $clinic->name }}</h5>
                    <p class="card-text text-sm text-muted mb-2">
                      <i class="fas fa-map-marker-alt"></i> {{ $clinic->address }}
                    </p>
                    <p class="card-text text-sm mb-2">
                      <i class="fas fa-phone"></i> {{ $clinic->contact }}
                      <i class="fas fa-ambulance ms-2"></i>
                    </p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                      <div class="clinic-rating">
                        @for($i = 1; $i <= 5; $i++)
                          <i class="fas fa-star {{ $i <= $clinic->stars  ? 'text-warning' : 'text-secondary' }}"></i>
                        @endfor
                        <span class="ms-1 text-sm">({{ $clinic->reviews_count }} reviews)</span>
                      </div>
                      @if(auth()->user()->role_id != 4)
                      <button class="btn btn-primary btn-sm" onclick="openSidebarWithClinic('{{ $clinic->id }}')">
                        Book Now
                      </button>
                      @else
                      <button class="btn btn-primary btn-sm" onclick="viewClinicDetails('{{ $clinic->id }}')">
                        View Details
                      </button>
                      @endif
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

<!-- Clinic Details Modal -->
<div class="modal fade" id="clinicDetailsModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Clinic Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="clinicDetailsContent"></div>
      </div>
    </div>
  </div>
</div>

<!-- Add/Edit Booking Sidebar -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="addClinicSidebar" style="width: 600px;">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title" id="sidebarTitle">Add New Booking</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close">
      <span aria-hidden="true" class="text-3xl">&times;</span>
    </button>
  </div>
  <div class="offcanvas-body">
    <form id="bookingForm" action="/owner" method="POST" class="needs-validation" enctype="multipart/form-data" novalidate>
      @csrf
      <input type="hidden" id="bookingId" name="booking_id">
      <input type="hidden" id="clinicSelect" name="clinic_id">
      <input type="hidden" name="client_id" value="{{ auth()->id() }}">
      <input type="hidden" name="status" value="pending">
      <input type="hidden" id="totalAmount" name="total_amount" value="0">
      
      <div class="mb-3">
        <label for="petSelect" class="form-label">Select Pet</label>
        <select class="form-select" id="petSelect" name="pet_id" required>
          <option value="" selected disabled>Choose a pet</option>
          @foreach($pets as $pet)
            <option value="{{ $pet->id }}">{{ $pet->name }}</option>
          @endforeach
        </select>
        <div class="invalid-feedback">
          Please select a pet.
        </div>
      </div>
      <div class="mb-3">
        <label for="serviceSelect" class="form-label">Select Service</label>
        <select class="form-select" id="serviceSelect" name="service_id" required disabled>
          <option value="" selected disabled>Choose a service</option>
        </select>
        <div class="invalid-feedback">
          Please select a service.
        </div>
      </div>
      <div class="mb-3">
        <label for="staffSelect" class="form-label">Assign Staff</label>
        <select class="form-select" id="staffSelect" name="staff_id" required disabled>
          <option value="" selected disabled>Choose a staff member</option>
        </select>
        <div class="invalid-feedback">
          Please select a staff member.
        </div>
      </div>
      <div class="mb-3">
        <label for="appointmentDate" class="form-label">Appointment Date & Time</label>
        <input type="datetime-local" class="form-control" id="appointmentDate" name="appointment_date" required 
               min="{{ date('Y-m-d\TH:i') }}">
        <div class="invalid-feedback">
          Please select a valid appointment date and time.
        </div>
      </div>
      
      <div class="mb-3">
        <label for="notes" class="form-label">Notes (Optional)</label>
        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
      </div>
      
      <div class="mb-3">
        <label for="paymentMethod" class="form-label">Payment Method</label>
        <select class="form-select" id="paymentMethod" name="payment_method" required onchange="togglePaymentFields()">
          <option value="" selected disabled>Select Payment Method</option>
          <option value="cash">Cash</option>
          <option value="gcash">GCash</option>
          <option value="online">Online Payment</option>
        </select>
        <div class="invalid-feedback">
          Please select a payment method.
        </div>
      </div>
      
      <!-- GCash Fields -->
      <div id="gcashFields" style="display: none;">
        <div class="mb-3">
          <label class="form-label">GCash Number</label>
          <p class="form-control-static" id="gcashNumber"></p>
          <small class="text-muted">Send payment to this GCash number</small>
        </div>
        <div class="mb-3">
          <label for="paymentProofRef" class="form-label">Reference Number</label>
          <input type="text" class="form-control" id="paymentProofRef" name="payment_reference">
          <div class="invalid-feedback">
            Please enter the reference number.
          </div>
        </div>
        <div class="mb-3">
          <label for="paymentProofImage" class="form-label">Upload Receipt</label>
          <input type="file" class="form-control" id="paymentProofImage" name="proof" accept="image/*" required>
          <div class="invalid-feedback">
            Please upload a receipt image.
          </div>
          <small class="text-muted">Upload a clear image of your GCash payment receipt. Accepted formats: JPG, PNG, JPEG.</small>
        </div>
      </div>
      
      <!-- Online Payment Button -->
      <div id="onlinePaymentFields" style="display: none;">
        <div class="mb-3">
          <button type="button" id="paymongoBtn" class="btn btn-info w-100">Proceed to Online Payment</button>
          <small class="text-muted">You will be redirected to our payment gateway</small>
          <input type="hidden" id="paymentSessionId" name="payment_session_id">
          <input type="hidden" id="paymentStatus" name="payment_status" value="pending">
        </div>
      </div>
      
      <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary" id="submitBtn">Book Now</button>
      </div>
    </form>
  </div>
</div>

<script>
function viewClinicDetails(clinicId) {
  fetch(`/api/clinics/${clinicId}/details`)
    .then(response => response.json())
    .then(data => {
      const content = `
        <div class="row">
          <div class="col-md-6">
            <img src="${data.image}" class="img-fluid rounded" alt="${data.name}">
          </div>
          <div class="col-md-6">
            <h4>${data.name}</h4>
            <p><i class="fas fa-map-marker-alt"></i> ${data.address}</p>
            <p><i class="fas fa-phone"></i> ${data.contact_number}</p>
            <p><i class="fas fa-clock"></i> Operating Hours: ${data.operating_hours}</p>
            <h5 class="mt-3">Available Services:</h5>
            <ul>
              ${data.services.map(service => `<li>${service.name} - ₱${service.price}</li>`).join('')}
            </ul>
          </div>
        </div>
      `;
      document.getElementById('clinicDetailsContent').innerHTML = content;
      new bootstrap.Modal(document.getElementById('clinicDetailsModal')).show();
    });
}

function openSidebarWithClinic(clinicId) {
  const sidebar = new bootstrap.Offcanvas(document.getElementById('addClinicSidebar'));
  sidebar.show();
  document.getElementById('clinicSelect').value = clinicId;
  loadServicesAndStaff();
}

// Search and filter functionality
document.getElementById('searchClinic').addEventListener('input', filterClinics);
document.getElementById('serviceFilter').addEventListener('change', filterClinics);
document.getElementById('locationFilter').addEventListener('change', filterClinics);

function filterClinics() {
  const searchTerm = document.getElementById('searchClinic').value.toLowerCase();
  const serviceId = document.getElementById('serviceFilter').value;
  const location = document.getElementById('locationFilter').value.toLowerCase();
  
  document.querySelectorAll('.clinic-card').forEach(card => {
    const clinicName = card.querySelector('.card-title').textContent.toLowerCase();
    const clinicLocation = card.querySelector('.card-text').textContent.toLowerCase();
    const clinicServices = card.dataset.services ? card.dataset.services.split(',') : [];
    
    const matchesSearch = clinicName.includes(searchTerm);
    const matchesService = !serviceId || clinicServices.includes(serviceId);
    const matchesLocation = !location || clinicLocation.includes(location);
    
    card.style.display = (matchesSearch && matchesService && matchesLocation) ? 'block' : 'none';
  });
}

// Form validation
(function () {
  'use strict'
  var forms = document.querySelectorAll('.needs-validation')
  Array.prototype.slice.call(forms)
    .forEach(function (form) {
      form.addEventListener('submit', function (event) {
        console.log('Form submission attempted');
        
        // Check if payment method is online
        const paymentMethod = document.getElementById('paymentMethod').value;
        if (paymentMethod === 'online' && document.getElementById('paymentStatus').value === 'pending') {
          // Prevent form submission for online payment until payment is completed
          event.preventDefault();
          event.stopPropagation();
          alert('Please complete the online payment process before submitting the form.');
          return;
        }
        
        // Log form data for debugging
        const formData = new FormData(form);
        console.log('Form action:', form.action);
        console.log('Form method:', form.method);
        for (let pair of formData.entries()) {
          console.log(pair[0] + ': ' + pair[1]);
        }
        
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
          console.log('Form validation failed');
        } else {
          console.log('Form validation passed');
          // Show loading indicator or disable submit button to prevent double submission
          const submitBtn = document.getElementById('submitBtn');
          if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
          }
        }
        form.classList.add('was-validated');
      }, false);
    });
})();

// Additional date-time validation
document.getElementById('appointmentDate').addEventListener('change', function(e) {
  const selectedDate = new Date(e.target.value);
  const now = new Date();
  
  if (selectedDate < now) {
    e.target.setCustomValidity('Please select a future date and time');
  } else {
    e.target.setCustomValidity('');
  }
});

async function loadServicesAndStaff() {
  const clinicId = document.getElementById('clinicSelect').value;
  const serviceSelect = document.getElementById('serviceSelect');
  const staffSelect = document.getElementById('staffSelect');

  if (!clinicId) {
    serviceSelect.disabled = true;
    staffSelect.disabled = true;
    return;
  }

  try {
    const [servicesResponse, staffsResponse, clinicResponse] = await Promise.all([
      fetch(`/api/clinics/${clinicId}/services`).then(response => response.json()),
      fetch(`/api/clinics/${clinicId}/staffs`).then(response => response.json()),
      fetch(`/api/clinics/${clinicId}`).then(response => response.json())
    ]);

    serviceSelect.innerHTML = '<option value="" selected disabled>Choose a Service</option>';
    servicesResponse.forEach(service => {
      serviceSelect.innerHTML += `<option value="${service.id}" data-price="${service.price}">${service.name} - ₱${service.price}</option>`;
    });
    serviceSelect.disabled = false;
    
    // Add event listener to update total amount when service is selected
    serviceSelect.addEventListener('change', function() {
      const selectedOption = this.options[this.selectedIndex];
      const price = selectedOption.dataset.price || 0;
      document.getElementById('totalAmount').value = price;
    });

    staffSelect.innerHTML = '<option value="" selected disabled>Choose a Staff</option>';
    staffsResponse.forEach(staff => {
      staffSelect.innerHTML += `<option value="${staff.id}">${staff.name}</option>`;
    });
    staffSelect.disabled = false;
    
    // Set GCash number to clinic contact
    if (clinicResponse && clinicResponse.contact) {
      document.getElementById('gcashNumber').textContent = clinicResponse.contact;
    }

  } catch (error) {
    console.error('Error loading dropdown data:', error);
    alert('Failed to load dropdown data. Please try again.');
  }
}

// Function to toggle payment fields based on selected payment method
function togglePaymentFields() {
  const paymentMethod = document.getElementById('paymentMethod').value;
  const gcashFields = document.getElementById('gcashFields');
  const onlinePaymentFields = document.getElementById('onlinePaymentFields');
  
  // Hide all payment fields first
  gcashFields.style.display = 'none';
  onlinePaymentFields.style.display = 'none';
  
  // Show fields based on selected payment method
  if (paymentMethod === 'gcash') {
    gcashFields.style.display = 'block';
    document.getElementById('paymentProofRef').setAttribute('required', 'required');
    document.getElementById('paymentProofImage').setAttribute('required', 'required');
  } else if (paymentMethod === 'online') {
    onlinePaymentFields.style.display = 'block';
  }
  
  // Remove required attribute if not GCash
  if (paymentMethod !== 'gcash') {
    document.getElementById('paymentProofRef').removeAttribute('required');
    document.getElementById('paymentProofImage').removeAttribute('required');
  }
}

// PayMongo Payment Integration
document.getElementById('paymongoBtn').addEventListener('click', function() {
  // Get booking details
  const totalAmount = parseFloat(document.getElementById('totalAmount').value);
  const petId = document.getElementById('petSelect').value;
  const serviceId = document.getElementById('serviceSelect').value;
  const staffId = document.getElementById('staffSelect').value;
  const appointmentDate = document.getElementById('appointmentDate').value;
  const notes = document.getElementById('notes').value;
  const clinicId = document.getElementById('clinicSelect').value;
  
  if (!totalAmount || !petId || !serviceId || !staffId || !appointmentDate || !clinicId) {
    alert('Please fill in all required fields before proceeding to payment.');
    return;
  }
  
  // Disable the button and show loading state
  const payButton = document.getElementById('paymongoBtn');
  payButton.disabled = true;
  payButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
  
  // Create a payment intent with PayMongo
  fetch('/api/create-payment', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify({
      amount: totalAmount * 100, // Convert to smallest currency unit (centavos)
      pet_id: petId,
      service_id: serviceId,
      staff_id: staffId,
      appointment_date: appointmentDate,
      notes: notes,
      clinic_id: clinicId,
      client_id: '{{ auth()->id() }}',
      payment_method: 'online'
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Store the payment session ID and booking ID
      document.getElementById('paymentSessionId').value = data.payment_id;
      
      // Set up a listener for when the user returns from the payment page
      // Store booking ID in localStorage to check payment status when returning
      localStorage.setItem('pendingBookingId', data.booking_id);
      localStorage.setItem('pendingPaymentId', data.payment_id);
      
      // Redirect to PayMongo checkout URL
      window.location.href = data.checkout_url;
    } else {
      alert('Error creating payment: ' + data.message);
      payButton.disabled = false;
      payButton.innerHTML = 'Proceed to Online Payment';
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred while processing your payment. Please try again.');
    payButton.disabled = false;
    payButton.innerHTML = 'Proceed to Online Payment';
  });
});

// Check payment status when page loads (for returning from payment)
document.addEventListener('DOMContentLoaded', function() {
  const pendingBookingId = localStorage.getItem('pendingBookingId');
  const pendingPaymentId = localStorage.getItem('pendingPaymentId');
  
  if (pendingBookingId && pendingPaymentId) {
    // Check payment status
    fetch(`/api/payment/${pendingBookingId}`)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Payment was successful
          document.getElementById('paymentStatus').value = 'completed';
          document.getElementById('paymongoBtn').disabled = true;
          document.getElementById('paymongoBtn').innerHTML = 'Payment Completed ✓';
          document.getElementById('paymongoBtn').classList.remove('btn-info');
          document.getElementById('paymongoBtn').classList.add('btn-success');
          
          // Auto-submit the form
          document.getElementById('submitBtn').click();
          
          // Clear localStorage
          localStorage.removeItem('pendingBookingId');
          localStorage.removeItem('pendingPaymentId');
        }
      })
      .catch(error => {
        console.error('Error checking payment status:', error);
      });
  }
});
</script>

@endsection
