@extends('layouts.user_type.auth')

@section('content')

  <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
              <h6>Clinics Table</h6>
              <button class="btn btn-primary btn-sm mb-0" onclick="openSidebar()">
                <i class="fas fa-plus"></i>&nbsp;&nbsp;Add New Clinic
              </button>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Clinic Info</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Location</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Contact</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Email</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Operating Days</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Opening Time</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Closing Time</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($clinics as $clinic)
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">{{ $clinic->name }}</h6>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-xs text-secondary mb-0">{{ Str::limit($clinic->address, 50) }}</p>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ $clinic->contact }}</span>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="text-secondary text-xs font-weight-bold">{{ $clinic->email }}</span>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="text-secondary text-xs font-weight-bold">
                          {{ implode(', ', array_map(function($day) { return substr($day, 0, 3); }, json_decode($clinic->operation_days))) }}
                        </span>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="text-secondary text-xs font-weight-bold">{{ date('H:i', strtotime($clinic->opening_time)) }}</span>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="text-secondary text-xs font-weight-bold">{{ date('H:i', strtotime($clinic->closing_time)) }}</span>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="badge badge-sm {{ $clinic->status === 'active' ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                          {{ ucfirst($clinic->status) }}
                        </span>
                      </td>
                      <td class="align-middle text-center">
                        <div class="d-flex gap-1">
                          <button onclick="editClinic({{ $clinic }})" 
                                  class="btn btn-icon-only btn-rounded btn-outline-primary mb-0 p-2 d-flex align-items-center justify-content-center"
                                  data-bs-toggle="tooltip"
                                  data-bs-placement="top"
                                  title="Edit Clinic">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                              <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                            </svg>
                          </button>
                          
                          <form action="clinics/{{ $clinic->id }}/delete" method="GET" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-icon-only btn-rounded btn-outline-danger mb-0 p-2 d-flex align-items-center justify-content-center"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Delete Clinic">
                              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                              </svg>
                            </button>
                          </form>
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

  <!-- Add/Edit Clinic Sidebar -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="clinicSidebar" style="width: 800px;">
    <div class="offcanvas-header border-bottom">
      <h5 class="offcanvas-title" id="sidebarTitle">Add New Clinic</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close">
        <span aria-hidden="true" class="text-3xl">&times;</span>
      </button>
    </div>
    <div class="offcanvas-body">
      <form id="clinicForm" onsubmit="handleSubmit(event)">
        @csrf
        <input type="hidden" id="clinicId" name="clinic_id">
        <div class="mb-3">
          <label for="clinicImage" class="form-label">Clinic Image</label>
          <input type="file" class="form-control" id="clinicImage" name="clinic_image" accept="image/*" onchange="previewImage(this)">
          <div class="mt-4 d-flex justify-content-center">
            <img id="imagePreview" src="../assets/img/dog.png" alt="Clinic Preview" class="rounded-circle" style="width: 200px; height: 200px; object-fit: cover;">
          </div>
        </div>
        <div class="mb-3">
          <label for="clinicName" class="form-label">Clinic Name</label>
          <input type="text" class="form-control" id="clinicName" name="clinic_name" required>
        </div>
        <div class="mb-3">
          <label for="clinicAddress" class="form-label">Location</label>
          <div id="map" style="height: 300px;" class="mb-2"></div>
          <input type="text" class="form-control" id="clinicAddress" name="clinic_address" required>
          <input type="hidden" id="latitude" name="latitude">
          <input type="hidden" id="longitude" name="longitude">
        </div>
        <div class="row mb-3">
          <div class="col-md-6">
            <label for="clinicPhone" class="form-label">Contact</label>
            <input type="tel" class="form-control" id="clinicPhone" name="clinic_phone" required>
          </div>
          <div class="col-md-6">
            <label for="clinicEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="clinicEmail" name="clinic_email" required>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Operating Days</label>
          <div class="d-flex flex-wrap gap-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="monday" name="operating_days[]" value="monday">
              <label class="form-check-label" for="monday">Monday</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="tuesday" name="operating_days[]" value="tuesday">
              <label class="form-check-label" for="tuesday">Tuesday</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="wednesday" name="operating_days[]" value="wednesday">
              <label class="form-check-label" for="wednesday">Wednesday</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="thursday" name="operating_days[]" value="thursday">
              <label class="form-check-label" for="thursday">Thursday</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="friday" name="operating_days[]" value="friday">
              <label class="form-check-label" for="friday">Friday</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="saturday" name="operating_days[]" value="saturday">
              <label class="form-check-label" for="saturday">Saturday</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="sunday" name="operating_days[]" value="sunday">
              <label class="form-check-label" for="sunday">Sunday</label>
            </div>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-6">
            <label for="openingTime" class="form-label">Opening Time</label>
            <input type="time" class="form-control" id="openingTime" name="opening_time" required>
          </div>
          <div class="col-md-6">
            <label for="closingTime" class="form-label">Closing Time</label>
            <input type="time" class="form-control" id="closingTime" name="closing_time" required>
          </div>
        </div>
        <div class="mb-3">
          <label for="clinicStatus" class="form-label">Status</label>
          <select class="form-select" id="clinicStatus" name="clinic_status" required>
            <option value="active" selected>Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary" id="submitBtn">Save Clinic</button>
        </div>
      </form>
    </div>
  </div>

  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

  <script>
    let map;
    let marker;

    function initMap() {
      const defaultLocation = [14.5995, 120.9842];
      map = L.map('map').setView(defaultLocation, 13);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
      }).addTo(map);

      map.on('click', function(e) {
        const latlng = e.latlng;
        if (marker) {
          marker.setLatLng(latlng);
        } else {
          marker = L.marker(latlng).addTo(map);
        }

        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latlng.lat}&lon=${latlng.lng}`)
          .then(response => response.json())
          .then(data => {
            document.getElementById('clinicAddress').value = data.display_name;
            document.getElementById('latitude').value = latlng.lat;
            document.getElementById('longitude').value = latlng.lng;
          });
      });
    }

    document.getElementById('clinicSidebar').addEventListener('shown.bs.offcanvas', function () {
      setTimeout(initMap, 250);
    });

    function previewImage(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('imagePreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
      }
    }

    function openSidebar() {
      document.getElementById('sidebarTitle').textContent = 'Add New Clinic';
      document.getElementById('clinicId').value = '';
      document.getElementById('clinicForm').reset();
      document.getElementById('imagePreview').src = '../assets/img/dog.png';
      var sidebar = new bootstrap.Offcanvas(document.getElementById('clinicSidebar'));
      sidebar.show();
    }

    function editClinic(data) {
      console.log(data);
      document.getElementById('sidebarTitle').textContent = 'Edit Clinic';
      document.getElementById('clinicId').value = data.id;
      
      document.getElementById('clinicName').value = data.name;
      document.getElementById('clinicAddress').value = data.address;
      document.getElementById('clinicPhone').value = data.contact;
      document.getElementById('clinicEmail').value = data.email;
      document.getElementById('openingTime').value = data.opening_time;
      document.getElementById('closingTime').value = data.closing_time;
      document.getElementById('clinicStatus').value = data.status;

      if (data.latitude && data.longitude) {
        document.getElementById('latitude').value = data.latitude;
        document.getElementById('longitude').value = data.longitude;
      }

      const operatingDays = JSON.parse(data.operation_days);
      document.querySelectorAll('input[name="operating_days[]"]').forEach(checkbox => {
        checkbox.checked = operatingDays.some(day => 
          day.substring(0, 3).toLowerCase() === checkbox.value.substring(0, 3)
        );
      });
          
      if (data.image) {
        document.getElementById('imagePreview').src = data.image;
      }
          
      var sidebar = new bootstrap.Offcanvas(document.getElementById('clinicSidebar'));
      sidebar.show();
    }

    function deleteClinic(id) {
      if (confirm('Are you sure you want to delete this clinic?')) {
        fetch(`/clinics/delete/${id}`, {
          method: 'GET',
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            //location.reload();
          }
        });
      }
    }

    function handleSubmit(event) {
      event.preventDefault();
      
      const formData = new FormData(event.target);
      const id = document.getElementById('clinicId').value;
      const url = id ? `/clinics/${id}` : '/clinics';
      const method = id ? 'POST' : 'POST';

      // Format time to H:i before sending
      const openingTime = document.getElementById('openingTime').value;
      const closingTime = document.getElementById('closingTime').value;
      formData.set('opening_time', openingTime.substring(0, 5));
      formData.set('closing_time', closingTime.substring(0, 5));
      
      fetch(url, {
        method: method,
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          location.reload();
        }
      });
    }
  </script>
  
  @endsection
