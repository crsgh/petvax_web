@extends('layouts.user_type.auth')

@section('content')
<div class="inventory-page">
  <div class="page-header">
    <h1 class="page-title"></h1>
    <div class="header-actions">
      <div class="search-wrapper">
        <x-ui.icon name="search" class="search-icon w-4 h-4" />
        <input 
          type="text" 
          id="inventorySearchInput"
          class="search-input"
          placeholder="Search inventory..." 
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
      
      <x-ui.button 
        variant="primary" 
        size="default" 
                  icon-name="add"
        onclick="openSidebar('inventorySidebar')"
      >
        Add New Item
      </x-ui.button>
    </div>
  </div>

  <div class="inventory-content" style="background: white; border-radius: 16px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #f1f5f9; overflow: hidden;">

    <div class="inventory-table-wrapper">
      <table class="inventory-table" id="inventoryTable">
        <thead>
          <tr>
            <th>Item</th>
            <th>Category</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Total Value</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($inventoryItems as $item)
          <tr data-name="{{ strtolower($item->name) }}" data-category="{{ $item->category_name }}">
            <td>
              <div class="item-info">
                <div class="item-name">{{ $item->name }}</div>
                <div class="item-description">{{ $item->description }}</div>
              </div>
            </td>
            <td>
              @php
                $categoryVariant = match($item->category_name) {
                  'medicine' => 'primary',
                  'equipment' => 'info',
                  'supplies' => 'warning',
                  'food' => 'success',
                  default => 'secondary'
                };
              @endphp
              <x-ui.badge :variant="$categoryVariant">
                {{ ucfirst($item->category_name) }}
              </x-ui.badge>
            </td>
            <td>
              <span class="quantity">{{ $item->quantity }}</span>
            </td>
            <td>
              <span class="unit-price">₱{{ number_format($item->unit_price, 2) }}</span>
            </td>
            <td>
              <span class="total-value">₱{{ number_format($item->quantity * $item->unit_price, 2) }}</span>
            </td>
            <td>
              @php
                $status = 'in_stock';
                $statusVariant = 'success';
                if ($item->quantity == 0) {
                  $status = 'out_of_stock';
                  $statusVariant = 'danger';
                } elseif ($item->quantity <= $item->min_stock) {
                  $status = 'low_stock';
                  $statusVariant = 'warning';
                }
              @endphp
              <x-ui.badge :variant="$statusVariant">
                {{ str_replace('_', ' ', ucfirst($status)) }}
              </x-ui.badge>
            </td>
            <td>
              <div class="actions-group">
                <x-ui.button 
                  variant="primary" 
                  size="xs" 
                  icon-name="edit"
                  onclick="editItem({{ json_encode($item) }})"
                  title="Edit Item"
                >Edit</x-ui.button>
                <x-ui.button 
                  variant="info" 
                  size="xs" 
                  icon-name="add"
                  onclick="addStock({{ $item->id }})"
                  title="Add Stock"
                >Add Stock</x-ui.button>
                <x-ui.button 
                  variant="danger" 
                  size="xs" 
                  icon-name="delete"
                  onclick="deleteItem({{ $item->id }})"
                  title="Delete Item"
                >Delete</x-ui.button>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center py-8">
              <div class="empty-state">
                <x-ui.icon name="package" class="w-12 h-12 text-gray-400 mb-4" />
                <h3 class="text-lg font-medium text-gray-900 mb-2">No inventory items found</h3>
                <p class="text-gray-500 mb-4">Start by adding your first inventory item.</p>
                <button onclick="openSidebar('inventorySidebar')" class="btn btn-primary">
                  <x-ui.icon name="add" class="w-4 h-4" />Add Item
                </button>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Inventory Sidebar -->
<div id="inventorySidebar" class="sidebar-overlay hidden">
  <div class="sidebar-backdrop" onclick="closeSidebar('inventorySidebar')"></div>
  <div class="sidebar-content">
    <div class="sidebar-header">
      <h3 class="sidebar-title">Add New Item</h3>
      <button type="button" class="sidebar-close" onclick="closeSidebar('inventorySidebar')">
        <x-ui.icon name="close" class="w-3.5 h-3.5" />
      </button>
    </div>

    <div class="sidebar-body">
      <form id="inventoryForm" method="POST" action="/inventory">
        @csrf
        <input type="hidden" name="item_id" id="itemId">
        
        <!-- Form Fields -->
        <div class="form-fields">
          <div class="form-group">
            <label class="form-label">Item Name *</label>
            <input type="text" name="name" id="itemName" class="form-input" required placeholder="Enter item name">
          </div>
          
          <div class="form-group">
            <label class="form-label">Category *</label>
            <select name="category_id" id="itemCategory" class="form-select" required>
              <option value="">Select Category</option>
              @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">SKU/Code</label>
            <input type="text" name="sku" id="itemSku" class="form-input" placeholder="Enter SKU or item code">
          </div>

          <div class="form-group">
            <label class="form-label">Quantity *</label>
            <input type="number" min="0" name="quantity" id="itemQuantity" class="form-input" required placeholder="Enter quantity">
          </div>

          <div class="form-group">
            <label class="form-label">Unit Price (₱) *</label>
            <input type="number" step="0.01" min="0" name="unit_price" id="itemPrice" class="form-input" required placeholder="Enter unit price">
          </div>

          <div class="form-group">
            <label class="form-label">Minimum Stock Level</label>
            <input type="number" min="0" name="min_stock" id="itemMinStock" class="form-input" placeholder="Enter minimum stock level">
          </div>

          <div class="form-group">
            <label class="form-label">Expiry Date</label>
            <input type="date" name="expiry_date" id="itemExpiryDate" class="form-input">
          </div>

          <div class="form-group">
            <label class="form-label">Supplier</label>
            <input type="text" name="supplier" id="itemSupplier" class="form-input" placeholder="Enter supplier name">
          </div>

          <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" id="itemDescription" rows="3" class="form-input" placeholder="Enter item description..."></textarea>
          </div>
        </div>

        <div class="sidebar-actions">
          <button type="button" class="btn-secondary" onclick="closeSidebar('inventorySidebar')">
            Cancel
          </button>
          <button type="submit" class="btn-primary" id="saveItemButton">
            Save Item
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Filter Modal -->
<x-ui.modal id="filterModal" title="Filter Inventory" size="sm">
  <div class="filter-form">
    <div class="form-group">
      <label class="form-label">Category</label>
      <select id="categoryFilter" class="form-select" onchange="filterTable()">
        <option value="">All Categories</option>
        <option value="medicine">Medicine</option>
        <option value="equipment">Equipment</option>
        <option value="supplies">Supplies</option>
        <option value="food">Food</option>
      </select>
    </div>
    
    <div class="form-group">
      <label class="form-label">Status</label>
      <select id="statusFilter" class="form-select" onchange="filterTable()">
        <option value="">All Status</option>
        <option value="in_stock">In Stock</option>
        <option value="low_stock">Low Stock</option>
        <option value="out_of_stock">Out of Stock</option>
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

<style>
/* Inventory Page Layout */
.inventory-page {
  padding: 1.5rem;
  background: #fff;
  min-height: 100vh;
  font-family: 'Poppins', sans-serif;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 2rem;
}

.header-content {
  flex: 1;
}

.page-title {
  font-size: 2rem;
  font-weight: 700;
  color: #111827;
  margin: 0 0 0.5rem 0;
  letter-spacing: -0.025em;
  font-family: 'Poppins', sans-serif;
}

.page-subtitle {
  font-size: 1rem;
  color: #6b7280;
  margin: 0;
  font-weight: 400;
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

.filters-section {
  padding: 1.25rem 1.5rem;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  border-bottom: 1px solid #e2e8f0;
}

.search-filter-container {
  display: flex;
  gap: 1rem;
  align-items: center;
  max-width: 800px;
}

.search-wrapper {
  position: relative;
  flex: 1;
}

.search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #9ca3af;
  pointer-events: none;
}

.search-input {
  width: 100%;
  padding: 0.875rem 1rem 0.875rem 2.5rem;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  font-size: 0.875rem;
  background: white;
  transition: all 0.2s ease;
  font-family: 'Poppins', sans-serif;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.search-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1), 0 1px 3px rgba(0, 0, 0, 0.1);
  transform: translateY(-1px);
}

.filter-select {
  min-width: 140px;
  padding: 0.875rem 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  font-size: 0.875rem;
  background: white;
  transition: all 0.2s ease;
  font-family: 'Poppins', sans-serif;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  cursor: pointer;
}

.filter-select:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1), 0 1px 3px rgba(0, 0, 0, 0.1);
  transform: translateY(-1px);
}

.filter-select:hover {
  border-color: #d1d5db;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.inventory-table-wrapper {
  overflow-x: auto;
}

.inventory-table {
  width: 100%;
  border-collapse: collapse;
  font-family: 'Poppins', sans-serif;
}

.inventory-table thead {
  background: #f8fafc;
  border-bottom: 1px solid #e5e7eb;
}

.inventory-table th {
  padding: 1rem 1.5rem;
  text-align: left;
  font-weight: 600;
  font-size: 0.875rem;
  color: #374151;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-family: 'Poppins', sans-serif;
}

.inventory-table td {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}

.inventory-table tbody tr:hover {
  background: #f8fafc;
}

/* Sidebar Styles */
.sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 1000;
  display: flex;
  justify-content: flex-end;
  visibility: hidden;
  transition: visibility 0.3s ease;
}

.sidebar-overlay:not(.hidden) {
  visibility: visible;
}

.sidebar-backdrop {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.sidebar-overlay:not(.hidden) .sidebar-backdrop {
  opacity: 1;
}

.sidebar-content {
  position: relative;
  width: 480px;
  height: 100vh;
  background: white;
  box-shadow: -4px 0 20px rgba(0, 0, 0, 0.15);
  display: flex;
  flex-direction: column;
  transform: translateX(100%);
  transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.sidebar-overlay:not(.hidden) .sidebar-content {
  transform: translateX(0);
}

.sidebar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 2rem 2rem 1rem 2rem;
  border-bottom: 1px solid #e5e7eb;
}

.sidebar-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
  font-family: 'Poppins', sans-serif;
}

.sidebar-close {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border: none;
  background: #f8fafc;
  color: #64748b;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.sidebar-close:hover {
  background: #e2e8f0;
  color: #475569;
}

.sidebar-body {
  flex: 1;
  padding: 2rem;
  overflow-y: auto;
}

.form-fields {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-label {
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.5rem;
  font-family: 'Poppins', sans-serif;
}

.form-input, .form-select {
  padding: 1rem 1.25rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 0.875rem;
  transition: all 0.2s ease;
  font-family: 'Poppins', sans-serif;
  background: #fafbfc;
  width: 100%;
}

.form-input:focus, .form-select:focus {
  outline: none;
  border-color: #3b82f6;
  background: white;
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
  transform: translateY(-1px);
}

.form-input::placeholder {
  color: #9ca3af;
}

.sidebar-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  padding-top: 2rem;
  margin-top: 2rem;
  border-top: 1px solid #e5e7eb;
}

.btn-primary, .btn-secondary {
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 600;
  font-family: 'Poppins', sans-serif;
  transition: all 0.2s ease;
  border: none;
  cursor: pointer;
}

.btn-primary {
  background: #3b82f6;
  color: white;
}

.btn-primary:hover {
  background: #2563eb;
}

.btn-secondary {
  background: #f3f4f6;
  color: #374151;
}

.btn-secondary:hover {
  background: #e5e7eb;
}

@media (max-width: 768px) {
  .sidebar-content {
    width: 100vw;
  }
  
  .sidebar-header {
    padding: 1.5rem;
  }
  
  .sidebar-body {
    padding: 1.5rem;
  }
}

.item-info {
  display: flex;
  flex-direction: column;
}

.item-name {
  font-weight: 600;
  color: #111827;
  font-size: 0.875rem;
  margin-bottom: 0.25rem;
}

.item-description {
  font-size: 0.75rem;
  color: #6b7280;
}

.quantity {
  font-weight: 600;
  color: #111827;
  font-size: 0.875rem;
}

.unit-price, .total-value {
  font-weight: 600;
  color: #059669;
  font-size: 0.875rem;
}

.actions-group {
  display: flex;
  gap: 0.5rem;
}

@media (max-width: 768px) {
  .inventory-page {
    padding: 1rem;
  }
  
  .page-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .actions-group {
    flex-direction: column;
    gap: 0.25rem;
  }
}
</style>

<script>
// Sidebar Functions
function openSidebar(sidebarId) {
  document.getElementById(sidebarId).classList.remove('hidden');
  document.body.style.overflow = 'hidden';
}

function closeSidebar(sidebarId) {
  document.getElementById(sidebarId).classList.add('hidden');
  document.body.style.overflow = 'auto';
}

function clearFilters() {
  document.getElementById('categoryFilter').value = '';
  document.getElementById('statusFilter').value = '';
  filterTable();
}

function filterTable() {
  const searchTerm = document.getElementById('searchInput').value.toLowerCase();
  const categoryFilter = document.getElementById('categoryFilter').value;
  const statusFilter = document.getElementById('statusFilter').value;
  const rows = document.querySelectorAll('#inventoryTable tbody tr');

  rows.forEach(row => {
    const name = row.dataset.name;
    const category = row.dataset.category;
    const statusBadge = row.querySelector('td:nth-child(6) .badge')?.textContent.toLowerCase().replace(' ', '_') || '';

    const matchesSearch = name.includes(searchTerm);
    const matchesCategory = !categoryFilter || category === categoryFilter;
    const matchesStatus = !statusFilter || statusBadge.includes(statusFilter.replace('_', ' '));

    row.style.display = matchesSearch && matchesCategory && matchesStatus ? '' : 'none';
  });
}

function viewItem(itemId) {
  console.log('View item:', itemId);
}

function editItem(itemData) {
  document.getElementById('itemId').value = itemData.id;
  document.getElementById('itemName').value = itemData.name;
  document.getElementById('itemCategory').value = itemData.category_id;
  document.getElementById('itemQuantity').value = itemData.quantity;
  document.getElementById('itemPrice').value = itemData.unit_price;
  document.getElementById('itemDescription').value = itemData.description;
  
  document.querySelector('#inventorySidebar .sidebar-title').textContent = 'Edit Item';
  document.getElementById('saveItemButton').textContent = 'Update Item';
  
  openSidebar('inventorySidebar');
}

function addStock(itemId) {
  const quantity = prompt('Enter quantity to add:');
  if (quantity && !isNaN(quantity) && quantity > 0) {
    // You can implement actual stock addition logic here
    fetch(`/inventory/${itemId}/add-stock`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ quantity: parseInt(quantity) })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        location.reload();
      } else {
        alert('Error adding stock: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error adding stock');
    });
  }
}

function deleteItem(itemId) {
  if (confirm('Are you sure you want to delete this inventory item? This action cannot be undone.')) {
    fetch(`/inventory/${itemId}/delete`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        location.reload();
      } else {
        alert('Error deleting item: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error deleting item');
    });
  }
}
</script>
@endsection
