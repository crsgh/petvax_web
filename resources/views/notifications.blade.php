@extends('layouts.user_type.auth')

@section('content')
<div class="categories-page">
  <div class="page-header">
    <div class="header-content">
      <h1 class="page-title"></h1>
      <p class="page-subtitle">Manage system notifications and alerts</p>
    </div>
    <div class="header-actions">
      <x-ui.button variant="primary" size="default" icon="fas fa-bell">Mark All Read</x-ui.button>
    </div>
  </div>
  <div class="categories-content" style="background: white; border-radius: 16px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #f1f5f9; overflow: hidden;">
    <div class="filters-section">
      <div class="flex gap-4 items-center">
        <div class="flex-1">
          <x-ui.input type="text" placeholder="Search notifications..." />
        </div>
      </div>
    </div>
    <div class="table-wrapper">
      <p style="padding: 2rem; text-align: center; color: #6b7280;">Notifications interface - Connect to your data source</p>
    </div>
  </div>
</div>

<style>
.categories-page { padding: 1.5rem; background: #fff; min-height: 100vh; font-family: 'Poppins', sans-serif; }
.page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; }
.header-content { flex: 1; }
.page-title { font-size: 1.5rem; font-weight: 600; color: #111827; margin: 0 0 0.5rem 0; letter-spacing: -0.025em; font-family: 'Poppins', sans-serif; }
.page-subtitle { font-size: 1rem; color: #6b7280; margin: 0; font-weight: 400; font-family: 'Poppins', sans-serif; }
.header-actions { flex-shrink: 0; }
.filters-section { padding: 1.5rem; border-bottom: 1px solid #f1f5f9; }
@media (max-width: 768px) { .categories-page { padding: 1rem; } .page-header { flex-direction: column; align-items: flex-start; gap: 1rem; } }
</style>
@endsection
