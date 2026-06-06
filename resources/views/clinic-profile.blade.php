@extends('layouts.user_type.auth')

@section('content')

<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header pb-0 px-3">
            <h6 class="mb-0">Clinic Profile</h6>
        </div>
        <div class="card-body pt-4 p-3">
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card card-profile">
                        <img src="{{ asset('assets/img/home-decor-2.jpg') }}" alt="clinic-image" class="img-fluid" style="height: 250px; object-fit: cover; width: 100%;">
                        <div class="card-body text-center">
                            <h5 class="mb-3">{{ $clinic->name ?? 'Clinic Name' }}</h5>
                            <p class="text-muted">
                                <x-ui.icon name="map-pin" class="w-4 h-4 me-2" />
                                {{ $clinic->address ?? 'Clinic Address' }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label">Clinic Name</label>
                                            <input type="text" class="form-control" value="{{ $clinic->name ?? '' }}" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label">Contact Number</label>
                                            <input type="text" class="form-control" value="{{ $clinic->phone ?? '' }}" disabled>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-control-label">Email</label>
                                    <input type="email" class="form-control" value="{{ $clinic->email ?? '' }}" disabled>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-control-label">Address</label>
                                    <textarea class="form-control" rows="3" disabled>{{ $clinic->address ?? '' }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label">Operating Days</label>
                                    <div class="d-flex flex-wrap gap-3">
                                        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ $day }}" 
                                                {{ in_array($day, json_decode($clinic->operation_days) ?? []) ? 'checked' : '' }} disabled>
                                            <label class="form-check-label">{{ ucfirst($day) }}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label">Opening Hour</label>
                                            <input type="time" class="form-control" value="{{ $clinic->opening_time ?? '' }}" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label">Closing Hour</label>
                                            <input type="time" class="form-control" value="{{ $clinic->closing_time ?? '' }}" disabled>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0 px-3">
                            <h6 class="mb-0">Location</h6>
                        </div>
                        <div class="card-body pt-4 p-3">
                            <div id="map" style="height: 400px; width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>

<script>
window.addEventListener('load', function() {
    const lat = {{ $clinic->latitude ?? 0 }};
    const lng = {{ $clinic->longitude ?? 0 }};
    
    if (!lat || !lng) {
        document.getElementById('map').innerHTML = '<p class="text-center">Location coordinates not available</p>';
        return;
    }

    const map = L.map('map', {
        center: [lat, lng],
        zoom: 15,
        scrollWheelZoom: false
    });
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    L.marker([lat, lng]).addTo(map)
        .bindPopup("{{ $clinic->name ?? 'Clinic Location' }}")
        .openPopup();

    // Force a map refresh after initialization
    setTimeout(() => {
        map.invalidateSize();
    }, 100);
});
</script>
@endpush

@endsection