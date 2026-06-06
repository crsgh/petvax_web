@extends('layouts.user_type.auth')

@section('content')
<div class="rule-base-page">
  <div class="page-header">
    <div class="header-content">
      <h1 class="page-title"></h1>
      <p class="page-subtitle"></p>
    </div>
    <div class="header-actions">
      @if(auth()->user()->role_id != 4)
      <x-ui.button 
        variant="primary" 
        size="default" 
        icon-name="add"
        onclick="openAddQuestionSidebar()"
      >
        Add New Question
      </x-ui.button>
      @endif
    </div>
  </div>

  <div class="questions-content">
    <div class="questions-table-wrapper">
      <table class="questions-table" id="questionsTable">
        <thead>
          <tr>
            <th>ID</th> 
            <th>Target</th>
            <th>Question</th>
            <th>Yes Response</th>
            <th>No Response</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($questions as $question)
          <tr>
            <td>
              <div class="question-id">{{ $question->id }}</div>
            </td>
            <td>
              <x-ui.badge :variant="$question->target === 'dog' ? 'info' : 'warning'">
                {{ ucfirst($question->target) }}
              </x-ui.badge>
            </td>
            <td>
              <div class="question-text">{{ $question->question }}</div>
            </td>
            <td>
              <div class="response-text">
                @if(is_numeric($question->yes))
                  <span class="response-link">Link to QID: {{ $question->yes }}</span>
                @else
                  <span class="response-desc">{{ Str::limit($question->yes, 50) }}</span>
                @endif
              </div>
            </td>
            <td>
              <div class="response-text">
                @if(is_numeric($question->no))
                  <span class="response-link">Link to QID: {{ $question->no }}</span>
                @else
                  <span class="response-desc">{{ Str::limit($question->no, 50) }}</span>
                @endif
              </div>
            </td>
            <td>
              <div class="actions-group">
                @if(auth()->user()->role_id != 4)
                <x-ui.button 
                  variant="primary" 
                  size="xs" 
                  icon-name="edit"
                  onclick="openEditQuestionSidebar({{ $question->toJson() }})"
                  title="Edit Question"
                >Edit</x-ui.button>
                <x-ui.button 
                  variant="danger" 
                  size="xs" 
                  icon-name="delete"
                  onclick="deleteQuestion({{ $question->id }})"
                  title="Delete Question"
                >Delete</x-ui.button>
                @endif
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Add Question Sidebar -->
<div id="addQuestionSidebar" class="sidebar-overlay hidden">
  <div class="sidebar-backdrop" onclick="closeAddQuestionSidebar()"></div>
  <div class="sidebar-content">
    <div class="sidebar-header">
      <h3 class="sidebar-title">Add New Question</h3>
      <button type="button" class="sidebar-close" onclick="closeAddQuestionSidebar()">
        <x-ui.icon name="close" class="w-4 h-4" />
      </button>
    </div>
    
    <div class="sidebar-body">
      <form id="addQuestionForm" action="" method="POST">
        @csrf
        
        <div class="form-group">
          <label class="form-label">Target Animal</label>
          <div class="radio-group">
            <label class="radio-option">
              <input type="radio" name="target" value="dog" checked>
              <span class="radio-mark"></span>
              Dog
            </label>
            <label class="radio-option">
              <input type="radio" name="target" value="cat">
              <span class="radio-mark"></span>
              Cat
            </label>
          </div>
        </div>
        
        <div class="form-group">
          <label class="form-label">Question Text</label>
          <textarea class="form-input" name="question" rows="3" required placeholder="Enter the diagnostic question..."></textarea>
        </div>

        <div class="form-group">
          <label class="form-label">Yes Response Type</label>
          <select class="form-input" id="addYesType" name="yesType" onchange="updateResponseInput('addYesType', 'addYesValueContainer', 'yes', allQuestions)">
            <option value="link">Link to Question ID</option>
            <option value="description">Text Description</option>
          </select>
        </div>
        <div class="form-group" id="addYesValueContainer">
          <!-- Content will be dynamically loaded by JS -->
        </div>

        <div class="form-group">
          <label class="form-label">No Response Type</label>
          <select class="form-input" id="addNoType" name="noType" onchange="updateResponseInput('addNoType', 'addNoValueContainer', 'no', allQuestions)">
            <option value="link">Link to Question ID</option>
            <option value="description">Text Description</option>
          </select>
        </div>
        <div class="form-group" id="addNoValueContainer">
          <!-- Content will be dynamically loaded by JS -->
        </div>
      </form>
    </div>
    
    <div class="sidebar-footer">
      <x-ui.button 
        type="button" 
        variant="secondary" 
        onclick="closeAddQuestionSidebar()"
      >
        Cancel
      </x-ui.button>
      <x-ui.button 
        type="submit" 
        variant="primary" 
        form="addQuestionForm"
      >
        Add Question
      </x-ui.button>
    </div>
  </div>
</div>

<!-- Edit Question Sidebar -->
<div id="editQuestionSidebar" class="sidebar-overlay hidden">
  <div class="sidebar-backdrop" onclick="closeEditQuestionSidebar()"></div>
  <div class="sidebar-content">
    <div class="sidebar-header">
      <h3 class="sidebar-title">Edit Question</h3>
      <button type="button" class="sidebar-close" onclick="closeEditQuestionSidebar()">
        <x-ui.icon name="close" class="w-4 h-4" />
      </button>
    </div>
    
    <div class="sidebar-body">
      <form id="editQuestionForm" method="POST">
        @csrf
        <input type="hidden" id="editQuestionId" name="id">
        
        <div class="form-group">
          <label class="form-label">Target Animal</label>
          <div class="radio-group">
            <label class="radio-option">
              <input type="radio" name="target" id="editDog" value="dog">
              <span class="radio-mark"></span>
              Dog
            </label>
            <label class="radio-option">
              <input type="radio" name="target" id="editCat" value="cat">
              <span class="radio-mark"></span>
              Cat
            </label>
          </div>
        </div>
        
        <div class="form-group">
          <label class="form-label">Question Text</label>
          <textarea class="form-input" id="editQuestionText" name="question" rows="3" required></textarea>
        </div>

        <div class="form-group">
          <label class="form-label">Yes Response Type</label>
          <select class="form-input" id="editYesType" name="yesType" onchange="updateResponseInput('editYesType', 'editYesValueContainer', 'yes', allQuestions)">
            <option value="link">Link to Question ID</option>
            <option value="description">Text Description</option>
          </select>
        </div>
        <div class="form-group" id="editYesValueContainer">
          <!-- Content will be dynamically loaded by JS -->
        </div>

        <div class="form-group">
          <label class="form-label">No Response Type</label>
          <select class="form-input" id="editNoType" name="noType" onchange="updateResponseInput('editNoType', 'editNoValueContainer', 'no', allQuestions)">
            <option value="link">Link to Question ID</option>
            <option value="description">Text Description</option>
          </select>
        </div>
        <div class="form-group" id="editNoValueContainer">
          <!-- Content will be dynamically loaded by JS -->
        </div>
      </form>
    </div>
    
    <div class="sidebar-footer">
      <x-ui.button 
        type="button" 
        variant="secondary" 
        onclick="closeEditQuestionSidebar()"
      >
        Cancel
      </x-ui.button>
      <x-ui.button 
        type="submit" 
        variant="primary" 
        form="editQuestionForm"
      >
        Update Question
      </x-ui.button>
    </div>
  </div>
</div>

<style>
/* Clean & Minimalist Rule Base Styles */
.rule-base-page {
  padding: 2rem;
  background: #fafbfc;
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
  font-size: 1.75rem;
  font-weight: 600;
  color: #111827;
  margin: 0 0 0.5rem 0;
  letter-spacing: -0.025em;
}

.page-subtitle {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
}

.header-actions {
  flex-shrink: 0;
}

.questions-content {
  background: white;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  overflow: hidden;
}

.questions-table-wrapper {
  overflow-x: auto;
}

.questions-table {
  width: 100%;
  border-collapse: collapse;
  font-family: 'Poppins', sans-serif;
}

.questions-table th {
  background: #f8fafc;
  padding: 1rem 1.5rem;
  text-align: left;
  font-weight: 600;
  font-size: 0.875rem;
  color: #374151;
  border-bottom: 1px solid #e5e7eb;
}

.questions-table td {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
  font-size: 0.875rem;
}

.questions-table tbody tr:hover {
  background: #f9fafb;
}

.question-id {
  font-weight: 600;
  color: #111827;
  font-size: 0.875rem;
}

.question-text {
  color: #111827;
  line-height: 1.5;
  max-width: 300px;
}

.response-text {
  max-width: 200px;
}

.response-link {
  color: #3b82f6;
  font-weight: 500;
  font-size: 0.8125rem;
}

.response-desc {
  color: #6b7280;
  font-size: 0.8125rem;
}

.actions-group {
  display: flex;
  gap: 0.5rem;
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
  width: 500px;
  max-width: 90vw;
  background: var(--sidebar-bg-color);
  box-shadow: -4px 0 24px rgba(0, 0, 0, 0.15);
  display: flex;
  flex-direction: column;
  height: 100vh;
  transform: translateX(100%);
  transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.sidebar-overlay:not(.hidden) .sidebar-content {
  transform: translateX(0);
}

.sidebar-header {
  padding: 1.5rem 2rem;
  border-bottom: 1px solid var(--sidebar-border-color);
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: var(--sidebar-header-color);
}

.sidebar-title {
  font-size: 1.25rem;
  font-weight: 600;
  margin: 0;
  font-family: var(--font-family), sans-serif;
}

.sidebar-close {
  background: none;
  border: none;
  color: var(--secondary-font-color);
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.sidebar-close:hover {
  background: var(--sidebar-border-color);
  color: var(--font-color);
}

.sidebar-body {
  flex: 1;
  overflow-y: auto;
  padding: 2rem;
  background: var(--sidebar-bg-color);
}

.sidebar-footer {
  padding: 1.5rem 2rem;
  border-top: 1px solid var(--sidebar-border-color);
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  background: var(--sidebar-header-color);
}

/* Form Styles */
.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  margin-bottom: 0.5rem;
  font-family: var(--font-family), sans-serif;
}

.form-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid var(--sidebar-border-color);
  border-radius: 8px;
  font-size: var(--font-size);
  transition: all 0.2s ease;
  background: var(--sidebar-bg-color);
  color: var(--font-color);
  font-family: var(--font-family), sans-serif;
}

.form-input:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.radio-group {
  display: flex;
  gap: 1.5rem;
}

.radio-option {
  display: flex;
  align-items: center;
  cursor: pointer;
  font-size: 0.875rem;
  color: #374151;
}

.radio-option input[type="radio"] {
  display: none;
}

.radio-mark {
  width: 18px;
  height: 18px;
  border: 2px solid #d1d5db;
  border-radius: 50%;
  margin-right: 0.5rem;
  position: relative;
  transition: all 0.2s ease;
}

.radio-option input[type="radio"]:checked + .radio-mark {
  border-color: #3b82f6;
  background: #3b82f6;
}

.radio-option input[type="radio"]:checked + .radio-mark::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 6px;
  height: 6px;
  background: white;
  border-radius: 50%;
}

@media (max-width: 768px) {
  .rule-base-page {
    padding: 1rem;
  }
  
  .page-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .questions-table th,
  .questions-table td {
    padding: 0.75rem 1rem;
  }
  
  .question-text,
  .response-text {
    max-width: 150px;
  }
}
</style>

<script>
// Pass questions data from Laravel to JavaScript
const allQuestions = @json($questions);

// Helper to check if a string is an integer
function isInteger(value) {
  return /^\d+$/.test(value);
}

// Helper to check if a question ID exists in the allQuestions array
function questionExists(id, questions) {
  const numericId = parseInt(id, 10);
  return questions.some(q => q.id === numericId);
}

// Function to dynamically update the input field for 'yes'/'no' responses
function updateResponseInput(typeSelectId, valueContainerId, responseKey, questionsData, initialValue = '') {
  const typeSelect = document.getElementById(typeSelectId);
  const valueContainer = document.getElementById(valueContainerId);
  const selectedType = typeSelect.value;

  let html = '';
  if (selectedType === 'link') {
    html = `
      <label class="form-label">Question ID</label>
      <select class="form-input" id="${responseKey}Value" name="${responseKey}Value" required>
        <option value="">Select Question ID</option>
        ${questionsData.map(q => `
          <option value="${q.id}">${q.id} - ${q.question.substring(0, 30)}${q.question.length > 30 ? '...' : ''}</option>
        `).join('')}
      </select>
    `;
  } else {
    html = `
      <label class="form-label">Description</label>
      <textarea class="form-input" id="${responseKey}Value" name="${responseKey}Value" rows="2" required></textarea>
    `;
  }
  valueContainer.innerHTML = html;

  // Set the value after the new element has been rendered
  const newValueInput = document.getElementById(`${responseKey}Value`);
  if (newValueInput) {
    newValueInput.value = initialValue;
  }
}

// Sidebar functions
function openAddQuestionSidebar() {
  document.getElementById('addQuestionSidebar').classList.remove('hidden');
  document.body.style.overflow = 'hidden';
  
  // Reset form and initialize
  document.getElementById('addQuestionForm').reset();
  updateResponseInput('addYesType', 'addYesValueContainer', 'yes', allQuestions);
  updateResponseInput('addNoType', 'addNoValueContainer', 'no', allQuestions);
}

function closeAddQuestionSidebar() {
  document.getElementById('addQuestionSidebar').classList.add('hidden');
  document.body.style.overflow = 'auto';
}

function openEditQuestionSidebar(question) {
  document.getElementById('editQuestionSidebar').classList.remove('hidden');
  document.body.style.overflow = 'hidden';
  
  // Populate form
  document.getElementById('editQuestionId').value = question.id;
  document.getElementById('editQuestionText').value = question.question;
  
  // Set target radio button
  if (question.target === 'dog') {
    document.getElementById('editDog').checked = true;
  } else {
    document.getElementById('editCat').checked = true;
  }
  
  // Determine response types
  let yesType = isInteger(question.yes) && questionExists(question.yes, allQuestions) ? 'link' : 'description';
  let noType = isInteger(question.no) && questionExists(question.no, allQuestions) ? 'link' : 'description';
  
  document.getElementById('editYesType').value = yesType;
  document.getElementById('editNoType').value = noType;
  
  // Update input fields with values
  updateResponseInput('editYesType', 'editYesValueContainer', 'yes', allQuestions, question.yes);
  updateResponseInput('editNoType', 'editNoValueContainer', 'no', allQuestions, question.no);
}

function closeEditQuestionSidebar() {
  document.getElementById('editQuestionSidebar').classList.add('hidden');
  document.body.style.overflow = 'auto';
}

function deleteQuestion(questionId) {
  if (confirm('Are you sure you want to delete this question? This action cannot be undone.')) {
    fetch(`/rule-base/${questionId}/delete`, {
      method: 'GET',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json',
      },
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        location.reload();
      } else {
        alert('Error deleting question: ' + (data.message || 'Unknown error'));
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error deleting question.');
    });
  }
}

// Initialize response inputs when page loads
document.addEventListener('DOMContentLoaded', () => {
  updateResponseInput('addYesType', 'addYesValueContainer', 'yes', allQuestions);
  updateResponseInput('addNoType', 'addNoValueContainer', 'no', allQuestions);
});
</script>
@endsection
