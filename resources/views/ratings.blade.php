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
          id="ratingsSearchInput"
          class="search-input"
          placeholder="Search ratings..." 
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
      <x-ui.button variant="primary" size="default" icon-name="star" onclick="openModal('ratingsModal')">View All Ratings</x-ui.button>
    </div>
  </div>
  <div class="ratings-content">
    <div class="ratings-table-wrapper">
      <table class="ratings-table" id="ratingsTable">
        <thead>
          <tr>
            <th>Customer</th>
            <th>Service</th>
            <th>Rating</th>
            <th>Review</th>
            <th>Date</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @php
            $sampleRatings = [
              ['customer' => 'Alice Johnson', 'service' => 'Pet Grooming', 'rating' => 5, 'review' => 'Excellent service! My dog looks amazing.', 'date' => '2024-01-15', 'status' => 'published'],
              ['customer' => 'Bob Smith', 'service' => 'Vaccination', 'rating' => 4, 'review' => 'Professional staff, quick service.', 'date' => '2024-01-14', 'status' => 'published'],
              ['customer' => 'Carol Davis', 'service' => 'Check-up', 'rating' => 5, 'review' => 'Very thorough examination. Highly recommended!', 'date' => '2024-01-13', 'status' => 'published'],
              ['customer' => 'David Wilson', 'service' => 'Surgery', 'rating' => 4, 'review' => 'Good care during recovery period.', 'date' => '2024-01-12', 'status' => 'pending'],
              ['customer' => 'Emma Brown', 'service' => 'Deworming', 'rating' => 5, 'review' => 'Affordable and effective treatment.', 'date' => '2024-01-11', 'status' => 'published'],
            ];
          @endphp
          
          @foreach($sampleRatings as $rating)
          <tr data-search="{{ strtolower($rating['customer'] . ' ' . $rating['service'] . ' ' . $rating['review']) }}">
            <td>
              <div class="customer-name">{{ $rating['customer'] }}</div>
            </td>
            <td>
              <div class="service-name">{{ $rating['service'] }}</div>
            </td>
            <td>
              <div class="rating-stars">
                @for($i = 1; $i <= 5; $i++)
                  <x-ui.icon name="star" class="w-4 h-4 {{ $i <= $rating['rating'] ? 'text-yellow-400' : 'text-gray-300' }}" />
                @endfor
                <span class="rating-number">({{ $rating['rating'] }}/5)</span>
              </div>
            </td>
            <td>
              <div class="review-text">{{ Str::limit($rating['review'], 50) }}</div>
            </td>
            <td>
              <div class="rating-date">{{ \Carbon\Carbon::parse($rating['date'])->format('M d, Y') }}</div>
            </td>
            <td>
              <x-ui.badge variant="{{ $rating['status'] == 'published' ? 'success' : 'warning' }}">
                {{ ucfirst($rating['status']) }}
              </x-ui.badge>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Ratings Modal -->
<x-ui.modal id="ratingsModal" title="View All Ratings" size="wide">
  <div class="ratings-analytics">
    <!-- Overall Rating -->
    <div class="rating-overview">
      <div class="overall-rating">
        <div class="rating-score">4.8</div>
        <div class="rating-stars">
          <x-ui.icon name="star" class="w-4 h-4" />
          <x-ui.icon name="star" class="w-4 h-4" />
          <x-ui.icon name="star" class="w-4 h-4" />
          <x-ui.icon name="star" class="w-4 h-4" />
          <x-ui.icon name="star" class="w-4 h-4" />
        </div>
        <div class="rating-count">Based on 247 reviews</div>
      </div>
      
      <div class="rating-breakdown">
        <div class="rating-bar">
          <span class="rating-label">5 stars</span>
          <div class="bar-container">
            <div class="bar-fill" style="width: 75%"></div>
          </div>
          <span class="rating-percent">75%</span>
        </div>
        <div class="rating-bar">
          <span class="rating-label">4 stars</span>
          <div class="bar-container">
            <div class="bar-fill" style="width: 15%"></div>
          </div>
          <span class="rating-percent">15%</span>
        </div>
        <div class="rating-bar">
          <span class="rating-label">3 stars</span>
          <div class="bar-container">
            <div class="bar-fill" style="width: 7%"></div>
          </div>
          <span class="rating-percent">7%</span>
        </div>
        <div class="rating-bar">
          <span class="rating-label">2 stars</span>
          <div class="bar-container">
            <div class="bar-fill" style="width: 2%"></div>
          </div>
          <span class="rating-percent">2%</span>
        </div>
        <div class="rating-bar">
          <span class="rating-label">1 star</span>
          <div class="bar-container">
            <div class="bar-fill" style="width: 1%"></div>
          </div>
          <span class="rating-percent">1%</span>
        </div>
      </div>
    </div>

    <!-- Recent Reviews -->
    <div class="recent-reviews">
      <h3>Recent Reviews</h3>
      <div class="review-item">
        <div class="review-header">
          <div class="reviewer-info">
            <strong>Sarah Johnson</strong>
            <div class="review-stars">
              <x-ui.icon name="star" class="w-4 h-4" />
              <x-ui.icon name="star" class="w-4 h-4" />
              <x-ui.icon name="star" class="w-4 h-4" />
              <x-ui.icon name="star" class="w-4 h-4" />
              <x-ui.icon name="star" class="w-4 h-4" />
            </div>
          </div>
          <span class="review-date">2 days ago</span>
        </div>
        <p class="review-text">Excellent service! Dr. Smith was very professional and caring with my dog Max. Highly recommended!</p>
      </div>
      
      <div class="review-item">
        <div class="review-header">
          <div class="reviewer-info">
            <strong>Mike Chen</strong>
            <div class="review-stars">
              <x-ui.icon name="star" class="w-4 h-4" />
              <x-ui.icon name="star" class="w-4 h-4" />
              <x-ui.icon name="star" class="w-4 h-4" />
              <x-ui.icon name="star" class="w-4 h-4" />
              <x-ui.icon name="star" class="w-4 h-4 text-gray-300" />
            </div>
          </div>
          <span class="review-date">1 week ago</span>
        </div>
        <p class="review-text">Great clinic with modern facilities. The staff is friendly and knowledgeable.</p>
      </div>
    </div>
  </div>
</x-ui.modal>

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
.ratings-content {
  background: white;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  overflow: hidden;
  margin-top: 0;
}

.ratings-table-wrapper {
  overflow-x: auto;
}

.ratings-table {
  width: 100%;
  border-collapse: collapse;
  font-family: 'Poppins', sans-serif;
}

.ratings-table th {
  background: #f8fafc;
  padding: 1rem 1.5rem;
  text-align: left;
  font-weight: 600;
  font-size: 0.875rem;
  color: #374151;
  border-bottom: 1px solid #e5e7eb;
}

.ratings-table td {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
  font-size: 0.875rem;
}

.ratings-table tbody tr:hover {
  background: #f9fafb;
}

.customer-name, .service-name {
  color: #111827;
  font-weight: 500;
}

.rating-stars {
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.rating-stars .rating-star {
  width: 14px;
  height: 14px;
}

.rating-number {
  color: #6b7280;
  font-size: 0.75rem;
  margin-left: 0.5rem;
}

.review-text {
  color: #6b7280;
  max-width: 200px;
}

.rating-date {
  color: #6b7280;
  font-size: 0.875rem;
}



@media (max-width: 768px) { 
  .categories-page { padding: 1rem; } 
  .page-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
  .header-actions { flex-direction: column; width: 100%; gap: 0.75rem; }
  .header-actions .search-wrapper { width: 100%; }
}

/* Ratings Modal Styles */
.ratings-analytics {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
}

.rating-overview {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.overall-rating {
  text-align: center;
  padding: 2rem;
  background: #f8fafc;
  border-radius: 12px;
}

.rating-score {
  font-size: 4rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.rating-stars {
  color: #fbbf24;
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
}

.rating-count {
  color: #6b7280;
  font-size: 0.875rem;
}

.rating-breakdown {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.rating-bar {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.rating-label {
  font-size: 0.875rem;
  color: #374151;
  min-width: 60px;
}

.bar-container {
  flex: 1;
  height: 8px;
  background: #e5e7eb;
  border-radius: 4px;
  overflow: hidden;
}

.bar-fill {
  height: 100%;
  background: #3b82f6;
  transition: width 0.3s ease;
}

.rating-percent {
  font-size: 0.875rem;
  color: #6b7280;
  min-width: 40px;
  text-align: right;
}

.recent-reviews h3 {
  margin-bottom: 1.5rem;
  color: #1f2937;
  font-size: 1.25rem;
  font-weight: 600;
}

.review-item {
  padding: 1.5rem;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  margin-bottom: 1rem;
}

.review-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 0.75rem;
}

.reviewer-info strong {
  display: block;
  color: #1f2937;
  font-size: 0.875rem;
  margin-bottom: 0.25rem;
}

.review-stars {
  color: #fbbf24;
  font-size: 0.75rem;
}

.review-date {
  color: #6b7280;
  font-size: 0.75rem;
}

.review-text {
  color: #374151;
  font-size: 0.875rem;
  line-height: 1.5;
  margin: 0;
}

@media (max-width: 768px) {
  .ratings-analytics {
    grid-template-columns: 1fr;
  }
}
</style>

<script>
function filterTable() {
  const searchTerm = document.getElementById('ratingsSearchInput').value.toLowerCase();
  const rows = document.querySelectorAll('#ratingsTable tbody tr');

  rows.forEach(row => {
    const searchData = row.dataset.search || '';
    const matchesSearch = searchData.includes(searchTerm);
    
    row.style.display = matchesSearch ? '' : 'none';
  });
}

function clearFilters() {
  document.getElementById('ratingFilter').value = '';
  document.getElementById('statusFilter').value = '';
  filterTable();
}
</script>

<!-- Filter Modal -->
<x-ui.modal id="filterModal" title="Filter Ratings" size="sm">
  <div class="filter-form">
    <div class="form-group">
      <label class="form-label">Rating</label>
      <select id="ratingFilter" class="form-select" onchange="filterTable()">
        <option value="">All Ratings</option>
        <option value="5">5 Stars</option>
        <option value="4">4 Stars</option>
        <option value="3">3 Stars</option>
        <option value="2">2 Stars</option>
        <option value="1">1 Star</option>
      </select>
    </div>
    
    <div class="form-group">
      <label class="form-label">Status</label>
      <select id="statusFilter" class="form-select" onchange="filterTable()">
        <option value="">All Status</option>
        <option value="published">Published</option>
        <option value="pending">Pending</option>
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
