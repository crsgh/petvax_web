@extends('layouts.user_type.auth')

@section('content')
<div class="categories-page">
  <div class="page-header">
    <h1 class="page-title"></h1>
    <div class="header-actions">
      <div class="search-wrapper">
        <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8"></circle>
          <path d="m21 21-4.35-4.35"></path>
        </svg>
        <input 
          type="text" 
          id="categorySearchInput"
          class="search-input"
          placeholder="Search categories..." 
          onkeyup="filterTable()"
        />
      </div>
      
      <x-ui.button 
        variant="secondary" 
        size="default" 
        icon="fas fa-filter"
        onclick="openModal('filterModal')"
      >
        Filters
      </x-ui.button>
      
      <x-ui.button 
        variant="primary" 
        size="default" 
        icon="fas fa-plus"
        onclick="openSidebar('categorySidebar')"
      >
        Add New Category
      </x-ui.button>
    </div>
  </div>
  <div class="categories-content">
    <div class="categories-table-wrapper">
      <table class="categories-table" id="categoriesTable">
        <thead>
          <tr>
            <th>Category Name</th>
            <th>Type</th>
            <th>Items Count</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($categories as $category)
          <tr data-search="{{ strtolower($category->name) }}">
            <td>
              <div class="category-name">{{ $category->name }}</div>
            </td>
            <td>
              <x-ui.badge variant="secondary">
                Product
              </x-ui.badge>
            </td>
            <td>
              <span class="items-count">0 items</span>
            </td>
            <td>
              <x-ui.badge variant="{{ $category->status == 'active' ? 'success' : 'danger' }}">
                {{ ucfirst($category->status) }}
              </x-ui.badge>
            </td>
            <td>
              <div class="actions-group">
                <x-ui.button 
                  variant="primary" 
                  size="sm" 
                  icon="fas fa-edit"
                  onclick="editCategory({{ json_encode($category) }})"
                  title="Edit Category"
                />
                <x-ui.button 
                  variant="danger" 
                  size="sm" 
                  icon="fas fa-trash"
                  onclick="deleteCategory({{ $category->id }})"
                  title="Delete Category"
                />
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="text-center py-8">
              <div class="empty-state">
                <i class="fas fa-tags text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No categories found</h3>
                <p class="text-gray-500 mb-4">Start by adding your first category.</p>
                <button onclick="openSidebar('categorySidebar')" class="btn btn-primary">
                  <i class="fas fa-plus mr-2"></i>Add Category
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

<!-- Category Sidebar -->
<div id="categorySidebar" class="sidebar-overlay hidden">
  <div class="sidebar-backdrop" onclick="closeSidebar('categorySidebar')"></div>
  <div class="sidebar-content">
    <div class="sidebar-header">
      <h3 class="sidebar-title">Add New Category</h3>
      <button type="button" class="sidebar-close" onclick="closeSidebar('categorySidebar')">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M13 1L1 13M1 1L13 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
    </div>

    <div class="sidebar-body">
      <form id="categoryForm" method="POST" action="/categories">
        @csrf
        <input type="hidden" name="category_id" id="categoryId">
        
        <!-- Form Fields -->
        <div class="form-fields">
          <div class="form-group">
            <label class="form-label">Category Name *</label>
            <input type="text" name="name" id="categoryName" class="form-input" required placeholder="Enter category name">
          </div>
          
          <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" id="categoryDescription" rows="3" class="form-input" placeholder="Enter category description..."></textarea>
          </div>
          
          <div class="form-group">
            <label class="form-label">Status *</label>
            <select name="status" id="categoryStatus" class="form-select" required>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>

        <div class="sidebar-actions">
          <button type="button" class="btn-secondary" onclick="closeSidebar('categorySidebar')">
            Cancel
          </button>
          <button type="submit" class="btn-primary" id="saveCategoryButton">
            Save Category
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
.categories-page { padding: 1.5rem; background: #fff; min-height: 100vh; font-family: 'Poppins', sans-serif; }
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

/* Clean Table Styles */
.categories-content {
  background: white;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  overflow: hidden;
  margin-top: 0;
}

.categories-table-wrapper {
  overflow-x: auto;
}

.categories-table {
  width: 100%;
  border-collapse: collapse;
  font-family: 'Poppins', sans-serif;
}

.categories-table th {
  background: #f8fafc;
  padding: 1rem 1.5rem;
  text-align: left;
  font-weight: 600;
  font-size: 0.875rem;
  color: #374151;
  border-bottom: 1px solid #e5e7eb;
}

.categories-table td {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
  font-size: 0.875rem;
}

.categories-table tbody tr:hover {
  background: #f9fafb;
}

.category-name {
  font-weight: 500;
  color: #111827;
}

.items-count {
  color: #6b7280;
  font-size: 0.875rem;
}

.actions-group {
  display: flex;
  gap: 0.5rem;
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
}

.sidebar-backdrop {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
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
  transition: transform 0.3s ease;
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

function filterTable() {
  const searchTerm = document.getElementById('categorySearchInput').value.toLowerCase();
  const rows = document.querySelectorAll('#categoriesTable tbody tr');

  rows.forEach(row => {
    const searchData = row.dataset.search || '';
    const matchesSearch = searchData.includes(searchTerm);
    
    row.style.display = matchesSearch ? '' : 'none';
  });
}

// Form submission
document.getElementById('categoryForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  const categoryId = document.getElementById('categoryId').value;
  
  try {
    const url = categoryId ? `/categories/${categoryId}` : '/categories';
    const response = await fetch(url, {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    });

    if (response.ok) {
      closeModal('categoryModal');
      window.location.reload();
    } else {
      const data = await response.json();
      alert(data.message || 'Failed to save category');
    }
  } catch (error) {
    console.error('Error saving category:', error);
    alert('Failed to save category');
  }
});

function editCategory(categoryData) {
  document.getElementById('categoryId').value = categoryData.id;
  document.getElementById('categoryName').value = categoryData.name;
  document.getElementById('categoryType').value = categoryData.type;
  document.getElementById('categoryStatus').value = categoryData.status;
  
  document.querySelector('#categorySidebar .sidebar-title').textContent = 'Edit Category';
  document.getElementById('saveCategoryButton').textContent = 'Update Category';
  
  openSidebar('categorySidebar');
}

async function deleteCategory(categoryId) {
  console.log('Delete category called with ID:', categoryId);
  if (confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
    try {
      console.log('Making delete request...');
      const response = await fetch(`/categories/${categoryId}/delete`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
      });
      
      console.log('Response status:', response.status);
      const data = await response.json();
      console.log('Response data:', data);
      
      if (data.success) {
        // Remove the row from the table
        const button = document.querySelector(`[onclick="deleteCategory(${categoryId})"]`);
        if (button) {
          const row = button.closest('tr');
          if (row) {
            row.remove();
          }
        }
        alert('Category deleted successfully');
      } else {
        alert('Failed to delete category: ' + data.message);
      }
    } catch (error) {
      console.error('Error deleting category:', error);
      alert('Failed to delete category: ' + error.message);
    }
  }
}

function clearFilters() {
  document.getElementById('typeFilter').value = '';
  document.getElementById('statusFilter').value = '';
  filterTable();
}
</script>

<!-- Filter Modal -->
<x-ui.modal id="filterModal" title="Filter Categories" size="sm">
  <div class="filter-form">
    <div class="form-group">
      <label class="form-label">Type</label>
      <select id="typeFilter" class="form-select" onchange="filterTable()">
        <option value="">All Types</option>
        <option value="service">Service</option>
        <option value="inventory">Inventory</option>
      </select>
    </div>
    
    <div class="form-group">
      <label class="form-label">Status</label>
      <select id="statusFilter" class="form-select" onchange="filterTable()">
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
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
