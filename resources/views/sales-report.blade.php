@extends('layouts.user_type.auth')

@section('content')
<div class="categories-page">
  <div class="page-header">
    <h1 class="page-title"></h1>
    <div class="header-actions">
      <div class="search-wrapper">
        <x-ui.icon name="search" class="search-icon w-4 h-4" />
        <input 
          type="text" 
          id="salesSearchInput"
          class="search-input"
          placeholder="Search transactions..." 
          onkeyup="filterTable()"
        />
      </div>
      
      <x-ui.button 
        variant="secondary" 
        size="default" 
        icon-name="filter"
        onclick="openModal('filterModal')"
      >
        Filters
      </x-ui.button>
      <x-ui.button variant="primary" size="default" icon-name="download" onclick="exportToCSV()">Export CSV</x-ui.button>
    </div>
  </div>
  <div class="sales-content">
    <div class="sales-table-wrapper">
      <table class="sales-table" id="salesTable">
        <thead>
          <tr>
            <th>Date</th>
            <th>Transaction ID</th>
            <th>Customer</th>
            <th>Service/Items</th>
            <th>Amount</th>
            <th>Payment Method</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @php
            $sampleSales = [
              ['id' => 'TXN001', 'date' => '2024-01-15', 'customer' => 'John Doe', 'items' => 'Vaccination - Rabies', 'amount' => 1500.00, 'method' => 'Cash', 'status' => 'completed'],
              ['id' => 'TXN002', 'date' => '2024-01-15', 'customer' => 'Jane Smith', 'items' => 'Pet Grooming', 'amount' => 800.00, 'method' => 'Card', 'status' => 'completed'],
              ['id' => 'TXN003', 'date' => '2024-01-14', 'customer' => 'Mike Johnson', 'items' => 'Antibiotics', 'amount' => 450.00, 'method' => 'Cash', 'status' => 'completed'],
              ['id' => 'TXN004', 'date' => '2024-01-14', 'customer' => 'Sarah Wilson', 'items' => 'Check-up + Deworming', 'amount' => 1200.00, 'method' => 'GCash', 'status' => 'pending'],
              ['id' => 'TXN005', 'date' => '2024-01-13', 'customer' => 'Robert Brown', 'items' => 'Surgery - Spaying', 'amount' => 5000.00, 'method' => 'Card', 'status' => 'completed'],
            ];
          @endphp
          
          @foreach($sampleSales as $sale)
          <tr data-search="{{ strtolower($sale['id'] . ' ' . $sale['customer'] . ' ' . $sale['items']) }}">
            <td>
              <div class="sale-date">{{ \Carbon\Carbon::parse($sale['date'])->format('M d, Y') }}</div>
            </td>
            <td>
              <div class="transaction-id">{{ $sale['id'] }}</div>
            </td>
            <td>
              <div class="customer-name">{{ $sale['customer'] }}</div>
            </td>
            <td>
              <div class="sale-items">{{ $sale['items'] }}</div>
            </td>
            <td>
              <div class="sale-amount">₱{{ number_format($sale['amount'], 2) }}</div>
            </td>
            <td>
              <x-ui.badge variant="{{ $sale['method'] == 'Cash' ? 'success' : ($sale['method'] == 'Card' ? 'primary' : 'info') }}">
                {{ $sale['method'] }}
              </x-ui.badge>
            </td>
            <td>
              <x-ui.badge variant="{{ $sale['status'] == 'completed' ? 'success' : 'warning' }}">
                {{ ucfirst($sale['status']) }}
              </x-ui.badge>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<style>
.categories-page { 
  padding: 1.5rem; 
  background: #fff; 
  min-height: 100vh; 
  font-family: 'Poppins', sans-serif; 
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.page-title {
  font-size: 1.75rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
  letter-spacing: -0.025em;
  font-family: 'Poppins', sans-serif;
}

.header-actions {
  display: flex;
  gap: 1rem;
  align-items: center;
}

/* Header Search Styles */
.header-actions .search-wrapper {
  position: relative;
  width: 300px;
  padding: 1px;
}

.header-actions .search-icon {
  position: absolute;
  left: 13px;
  top: 50%;
  transform: translateY(-50%);
  color: #9ca3af;
  pointer-events: none;
  z-index: 1;
}

.header-actions .search-input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 0.875rem;
  background: white;
  transition: all 0.2s ease;
  font-family: 'Poppins', sans-serif;
}

.header-actions .search-input:focus {
  outline: none;
  border: 1px solid #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Modal Actions */
.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 1.5rem;
}

.modal-action-btn {
  flex: 1;
  min-width: 120px;
}

/* Clean Table Styles */
.sales-content {
  background: white;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  overflow: hidden;
  margin-top: 0;
}

.sales-table-wrapper {
  overflow-x: auto;
}

.sales-table {
  width: 100%;
  border-collapse: collapse;
  font-family: 'Poppins', sans-serif;
}

.sales-table th {
  background: #f8fafc;
  padding: 1rem 1.5rem;
  text-align: left;
  font-weight: 600;
  font-size: 0.875rem;
  color: #374151;
  border-bottom: 1px solid #e5e7eb;
}

.sales-table td {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
  font-size: 0.875rem;
}

.sales-table tbody tr:hover {
  background: #f9fafb;
}

.sale-date, .transaction-id, .customer-name, .sale-items {
  color: #111827;
}

.sale-amount {
  font-weight: 600;
  color: #059669;
}

@media (max-width: 768px) { 
  .categories-page { padding: 1rem; } 
  .page-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
  .header-actions { flex-direction: column; width: 100%; gap: 0.75rem; }
  .header-actions .search-wrapper { width: 100%; }
}
</style>

<script>
function filterTable() {
  const searchTerm = document.getElementById('salesSearchInput').value.toLowerCase();
  const rows = document.querySelectorAll('#salesTable tbody tr');

  rows.forEach(row => {
    const searchData = row.dataset.search || '';
    const matchesSearch = searchData.includes(searchTerm);
    
    row.style.display = matchesSearch ? '' : 'none';
  });
}

function clearFilters() {
  document.getElementById('statusFilter').value = '';
  document.getElementById('paymentFilter').value = '';
  filterTable();
}

function exportToCSV() {
  const table = document.getElementById('salesTable');
  const rows = table.querySelectorAll('tr');
  const csv = [];
  
  rows.forEach(row => {
    if (row.style.display === 'none') return;
    const cells = row.querySelectorAll('th, td');
    const rowData = [];
    cells.forEach(cell => {
      let text = cell.textContent.trim().replace(/,/g, ' ').replace(/"/g, '""');
      rowData.push(`"${text}"`);
    });
    csv.push(rowData.join(','));
  });
  
  const blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  const date = new Date().toISOString().slice(0, 10);
  link.href = URL.createObjectURL(blob);
  link.download = `sales-report-${date}.csv`;
  link.click();
  URL.revokeObjectURL(link.href);
}
</script>

<!-- Filter Modal -->
<x-ui.modal id="filterModal" title="Filter Sales" size="sm">
  <div class="filter-form">
    <div class="form-group">
      <label class="form-label">Status</label>
      <select id="statusFilter" class="form-select" onchange="filterTable()">
        <option value="">All Status</option>
        <option value="completed">Completed</option>
        <option value="pending">Pending</option>
        <option value="cancelled">Cancelled</option>
      </select>
    </div>
    
    <div class="form-group">
      <label class="form-label">Payment Method</label>
      <select id="paymentFilter" class="form-select" onchange="filterTable()">
        <option value="">All Methods</option>
        <option value="cash">Cash</option>
        <option value="card">Card</option>
        <option value="gcash">GCash</option>
      </select>
    </div>
  </div>
  
  <div class="modal-actions">
    <x-ui.button 
      type="button" 
      variant="secondary" 
      onclick="clearFilters()"
      class="modal-action-btn"
    >
      Clear Filters
    </x-ui.button>
    <x-ui.button 
      type="button" 
      variant="primary" 
      onclick="closeModal('filterModal')"
      class="modal-action-btn"
    >
      Apply Filters
    </x-ui.button>
  </div>
</x-ui.modal>

@endsection
