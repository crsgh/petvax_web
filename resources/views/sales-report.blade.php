@extends('layouts.user_type.auth')

@section('content')

<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Sales Report</h6>
                            <div>
                                <button class="btn btn-success btn-sm" onclick="exportToExcel()">
                                    <i class="fas fa-file-excel"></i>&nbsp;Save to Excel
                                </button>
                            </div>
                        </div>
                        
                        <div class="row align-items-center mt-2">
                            <div class="col-md-3">
                                <label class="form-label small mb-1">From Date</label>
                                <div class="input-group input-group-sm">
                                    <input type="date" class="form-control" id="startDate">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small mb-1">To Date</label>
                                <div class="input-group input-group-sm">
                                    <input type="date" class="form-control" id="endDate">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small mb-1">Service</label>
                                <div class="d-flex">
                                    <select class="form-select form-select-sm me-2 w-100" id="serviceFilter">
                                        <option value="">All Services</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="" style="height:1.8rem"></label>
                                <div class="d-flex">
                                    <button class="btn btn-secondary btn-sm me-2" onclick="resetFilters()">
                                        Reset
                                    </button>
                                    <button class="btn btn-primary btn-sm" onclick="applyFilters()">
                                        Apply
                                    </button>
                                </div>                               
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-items-center m-0" id="salesReportTable">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Transaction ID</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Service</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date & Time</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Quantity</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="salesTableBody">
                                    @php 
                                        $total = 0;
                                        $totalQuantity = 0;
                                    @endphp
                                    @foreach($bookings->where('status', 'completed') as $booking)
                                    @php 
                                        $total += $booking->total_amount;
                                        $totalQuantity++;
                                    @endphp
                                    <tr class="booking-row"
                                        data-date="{{ \Carbon\Carbon::parse($booking->appointment_datetime)->format('Y-m-d') }}"
                                        data-service="{{ $booking->service_id }}">
                                        <td>
                                            <h6 class="mb-0 text-sm ps-2">TXN-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</h6>
                                        </td>
                                        <td>
                                            <p class="text-xs text-secondary mb-0 ps-2">{{ $booking->service->name }}</p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">
                                                {{ \Carbon\Carbon::parse($booking->appointment_datetime)->format('M d, Y g:i A') }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">1</td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">
                                                ₱{{ number_format($booking->total_amount, 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <td colspan="3" class="text-end fw-bold">Totals:</td>
                                        <td class="text-center fw-bold" id="totalQuantity">{{ $totalQuantity }}</td>
                                        <td class="text-center fw-bold" id="totalAmount">₱{{ number_format($total, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function applyFilters() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const serviceFilter = document.getElementById('serviceFilter').value;
    
    const rows = document.querySelectorAll('.booking-row');
    let filteredTotal = 0;
    let filteredQuantity = 0;
    
    rows.forEach(row => {
        let show = true;
        const rowDate = row.dataset.date;
        
        if (startDate && rowDate < startDate) show = false;
        if (endDate && rowDate > endDate) show = false;
        if (serviceFilter && row.dataset.service !== serviceFilter) show = false;
        
        row.style.display = show ? '' : 'none';
        
        if (show) {
            filteredQuantity++;
            const amount = parseFloat(row.querySelector('td:last-child span').textContent.replace('₱', '').replace(/,/g, ''));
            filteredTotal += amount;
        }
    });
    
    // Update totals
    document.getElementById('totalQuantity').textContent = filteredQuantity;
    document.getElementById('totalAmount').textContent = '₱' + filteredTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

function resetFilters() {
    document.getElementById('startDate').value = '';
    document.getElementById('endDate').value = '';
    document.getElementById('serviceFilter').value = '';
    
    const rows = document.querySelectorAll('.booking-row');
    rows.forEach(row => row.style.display = '');
    
    // Reset totals to original values
    const originalQuantity = {{ $totalQuantity }};
    const originalTotal = {{ $total }};
    
    document.getElementById('totalQuantity').textContent = originalQuantity;
    document.getElementById('totalAmount').textContent = '₱' + originalTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

function exportToExcel() {
    const table = document.getElementById('salesReportTable');
    const wb = XLSX.utils.table_to_book(table, {sheet: "Sales Report"});
    XLSX.writeFile(wb, 'sales_report.xlsx');
}

// Initialize date inputs with current month range
window.addEventListener('DOMContentLoaded', (event) => {
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
    
    document.getElementById('startDate').value = firstDay.toISOString().split('T')[0];
    document.getElementById('endDate').value = lastDay.toISOString().split('T')[0];
    applyFilters();
});
</script>

@endsection
    