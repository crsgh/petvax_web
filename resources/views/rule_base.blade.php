@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12">
        <div class="card mb-4">
          <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6>Question Management</h6>
              {{-- Assuming role_id 4 is a restricted role, similar to your example --}}
              @if(auth()->user()->role_id != 4)
              <button class="btn btn-primary btn-sm" onclick="openAddQuestionSidebar()">
                <i class="fas fa-plus"></i>&nbsp;&nbsp;Add New Question
              </button>
              @endif
            </div>
          </div>
          <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
              <table class="table align-items-center mb-0" id="questionsTable">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Target</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Question</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Yes Response</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No Response</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($questions as $question)
                  <tr>
                    <td>
                      <div class="d-flex px-2 py-1">
                        <div class="d-flex flex-column justify-content-center">
                          <h6 class="mb-0 text-sm">{{ $question->id }}</h6>
                        </div>
                      </div>
                    </td>
                    <td>
                      <p class="text-xs text-secondary mb-0">{{ ucfirst($question->target) }}</p>
                    </td>
                    <td>
                      <p class="text-xs text-secondary mb-0">{{ $question->question }}</p>
                    </td>
                    <td>
                      <p class="text-xs text-secondary mb-0">
                        @if(is_numeric($question->yes))
                        Link to QID: {{ $question->yes }}
                        @else
                        {{ $question->yes }}
                        @endif
                      </p>
                    </td>
                    <td>
                      <p class="text-xs text-secondary mb-0">
                        @if(is_numeric($question->no))
                        Link to QID: {{ $question->no }}
                        @else
                        {{ $question->no }}
                        @endif
                      </p>
                    </td>
                    <td class="align-middle text-center">
                      <div class="btn-group" role="group">
                        @if(auth()->user()->role_id != 4)
                        <button type="button" class="btn btn-info btn-lg px-3 py-2" style="background-color: #90EE90; color: #333; border-radius: 0.3rem; margin-right: 8px;" onclick="openEditQuestionSidebar({{ json_encode($question) }})" title="Edit Question">
                          <i class="fas fa-edit" style="font-size: 1.1em;"></i>
                        </button>
                        <button type="button" class="btn btn-lg px-3 py-2" style="background-color: #FFB6C1; color: #333; border-radius: 0.3rem;" onclick="deleteQuestion({{ $question->id }})" title="Delete Question">
                          <i class="fas fa-trash" style="font-size: 1.1em;"></i>
                        </button>
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
      </div>
    </div>
  </div>
</main>

{{-- Add Question Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="addQuestionSidebar" aria-labelledby="addQuestionSidebarLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="addQuestionSidebarLabel">Add New Question</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <form id="addQuestionForm" action="" method="POST">
      @csrf
      <div class="mb-3">
        <label for="addTarget" class="form-label">Target</label>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="target" id="addDog" value="dog" checked>
          <label class="form-check-label" for="addDog">Dog</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="target" id="addCat" value="cat">
          <label class="form-check-label" for="addCat">Cat</label>
        </div>
      </div>
      <div class="mb-3">
        <label for="addQuestionText" class="form-label">Question</label>
        <textarea class="form-control" id="addQuestionText" name="question" rows="3" required></textarea>
      </div>

      {{-- Yes Response --}}
      <div class="mb-3">
        <label for="addYesType" class="form-label">Yes Response Type</label>
        <select class="form-select" id="addYesType" name="yesType" onchange="updateResponseInput('addYesType', 'addYesValueContainer', 'yes', allQuestions)">
          <option value="link">Link to Question ID</option>
          <option value="description">Text Description</option>
        </select>
      </div>
      <div class="mb-3" id="addYesValueContainer">
        {{-- Content will be dynamically loaded by JS --}}
      </div>

      {{-- No Response --}}
      <div class="mb-3">
        <label for="addNoType" class="form-label">No Response Type</label>
        <select class="form-select" id="addNoType" name="noType" onchange="updateResponseInput('addNoType', 'addNoValueContainer', 'no', allQuestions)">
          <option value="link">Link to Question ID</option>
          <option value="description">Text Description</option>
        </select>
      </div>
      <div class="mb-3" id="addNoValueContainer">
        {{-- Content will be dynamically loaded by JS --}}
      </div>

      <button type="submit" class="btn btn-primary">Add Question</button>
    </form>
  </div>
</div>

{{-- Edit Question Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="editQuestionSidebar" aria-labelledby="editQuestionSidebarLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="editQuestionSidebarLabel">Edit Question</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <form id="editQuestionForm" method="POST">
      @csrf
      
      <input type="hidden" id="editQuestionId" name="id">
      <div class="mb-3">
        <label for="editTarget" class="form-label">Target</label>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="target" id="editDog" value="dog">
          <label class="form-check-label" for="editDog">Dog</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="target" id="editCat" value="cat">
          <label class="form-check-label" for="editCat">Cat</label>
        </div>
      </div>
      <div class="mb-3">
        <label for="editQuestionText" class="form-label">Question</label>
        <textarea class="form-control" id="editQuestionText" name="question" rows="3" required></textarea>
      </div>

      {{-- Yes Response --}}
      <div class="mb-3">
        <label for="editYesType" class="form-label">Yes Response Type</label>
        <select class="form-select" id="editYesType" name="yesType" onchange="updateResponseInput('editYesType', 'editYesValueContainer', 'yes', allQuestions)">
          <option value="link">Link to Question ID</option>
          <option value="description">Text Description</option>
        </select>
      </div>
      <div class="mb-3" id="editYesValueContainer">
        {{-- Content will be dynamically loaded by JS --}}
      </div>

      {{-- No Response --}}
      <div class="mb-3">
        <label for="editNoType" class="form-label">No Response Type</label>
        <select class="form-select" id="editNoType" name="noType" onchange="updateResponseInput('editNoType', 'editNoValueContainer', 'no', allQuestions)">
          <option value="link">Link to Question ID</option>
          <option value="description">Text Description</option>
        </select>
      </div>
      <div class="mb-3" id="editNoValueContainer">
        {{-- Content will be dynamically loaded by JS --}}
      </div>

      <button type="submit" class="btn btn-primary">Update Question</button>
    </form>
  </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteQuestionModal" tabindex="-1" aria-labelledby="deleteQuestionModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteQuestionModalLabel">Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this question? This action cannot be undone.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
      </div>
    </div>
  </div>
</div>

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
  // It now accepts an initialValue to pre-fill the field
  function updateResponseInput(typeSelectId, valueContainerId, responseKey, questionsData, initialValue = '') {
    const typeSelect = document.getElementById(typeSelectId);
    const valueContainer = document.getElementById(valueContainerId);
    const selectedType = typeSelect.value;

    let html = '';
    if (selectedType === 'link') {
      html = `
        <label for="${responseKey}Value" class="form-label">Question ID</label>
        <select class="form-select" id="${responseKey}Value" name="${responseKey}Value" required>
          <option value="">Select Question ID</option>
          ${questionsData.map(q => `
            <option value="${q.id}">${q.id} - ${q.question.substring(0, 30)}${q.question.length > 30 ? '...' : ''}</option>
          `).join('')}
        </select>
      `;
    } else {
      html = `
        <label for="${responseKey}Value" class="form-label">Description</label>
        <textarea class="form-control" id="${responseKey}Value" name="${responseKey}Value" rows="2" required></textarea>
      `;
    }
    valueContainer.innerHTML = html;

    // Set the value after the new element has been rendered
    const newValueInput = document.getElementById(`${responseKey}Value`);
    if (newValueInput) { // Ensure the element exists before trying to set its value
      newValueInput.value = initialValue;
    }
  }

  // Initialize the response inputs when the page loads for the Add form
  document.addEventListener('DOMContentLoaded', () => {
    updateResponseInput('addYesType', 'addYesValueContainer', 'yes', allQuestions);
    updateResponseInput('addNoType', 'addNoValueContainer', 'no', allQuestions);
  });

  function openAddQuestionSidebar() {
    const addSidebar = new bootstrap.Offcanvas(document.getElementById('addQuestionSidebar'));
    // Reset form fields
    document.getElementById('addQuestionForm').reset();
    document.getElementById('addDog').checked = true; // Default to dog
    document.getElementById('addYesType').value = 'link'; // Default to link
    document.getElementById('addNoType').value = 'link'; // Default to link
    updateResponseInput('addYesType', 'addYesValueContainer', 'yes', allQuestions);
    updateResponseInput('addNoType', 'addNoValueContainer', 'no', allQuestions);
    addSidebar.show();
  }

  function openEditQuestionSidebar(question) {
    const editSidebar = new bootstrap.Offcanvas(document.getElementById('editQuestionSidebar'));
    const form = document.getElementById('editQuestionForm');

    // Set form action for update
    form.action = `/rule-base/${question.id}`; // Placeholder route

    // Populate basic fields
    document.getElementById('editQuestionId').value = question.id;
    document.getElementById('editQuestionText').value = question.question;
    setTimeout(() => {
        // Find all elements with 'yesValue' and 'noValue' IDs
        const yesElements = document.querySelectorAll('[id$="yesValue"]');
        const noElements = document.querySelectorAll('[id$="noValue"]');

        // Update all yes value elements found
        yesElements.forEach(element => {
            if (element) element.value = question.yes;
        });

        // Update all no value elements found 
        noElements.forEach(element => {
            if (element) element.value = question.no;
        });
    }, 200);

    // Set target radio button
    if (question.target === 'dog') {
      document.getElementById('editDog').checked = true;
    } else {
      document.getElementById('editCat').checked = true;
    }

    // Determine yesType and noType based on values and existing questions
    let determinedYesType = 'description';
    if (isInteger(question.yes) && questionExists(question.yes, allQuestions)) {
      determinedYesType = 'link';
    }

    let determinedNoType = 'description';
    if (isInteger(question.no) && questionExists(question.no, allQuestions)) {
      determinedNoType = 'link';
    }

    // Set the select elements for type
    document.getElementById('editYesType').value = determinedYesType;
    document.getElementById('editNoType').value = determinedNoType;

    // Update the input fields based on the determined types and pre-fill values
    // Pass the actual value from the question object to updateResponseInput
    updateResponseInput('editYesType', 'editYesValueContainer', 'yes', allQuestions, question.yes);
    updateResponseInput('editNoType', 'editNoValueContainer', 'no', allQuestions, question.no);

    editSidebar.show();
  }

  function deleteQuestion(questionId) {
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteQuestionModal'));
    document.getElementById('confirmDeleteBtn').onclick = function() {
      fetch(`/rule-base/${questionId}/delete`, { // Placeholder route

          method: 'GET',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}', // Laravel CSRF token
            'Content-Type': 'application/json',
          },
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            location.reload(); // Reload page on success
          } else {
            alert('Error deleting question: ' + (data.message || 'Unknown error'));
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error deleting question.');
        })
        .finally(() => {
          deleteModal.hide();
        });
    };
    deleteModal.show();
  }
</script>
@endsection