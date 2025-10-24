@props([
    'id' => 'modal',
    'title' => null,
    'size' => 'default',
    'closable' => true
])

@php
$sizeClasses = match($size) {
    'sm' => 'max-w-lg',
    'lg' => 'max-w-5xl',
    'xl' => 'max-w-7xl',
    'full' => 'max-w-full mx-4',
    'wide' => 'wide',
    default => 'max-w-3xl'
};
@endphp

<div id="{{ $id }}" class="modal-overlay hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50" onclick="closeModal('{{ $id }}')">
    <div class="modal-content bg-white rounded-2xl shadow-2xl {{ $sizeClasses }} w-full mx-4 max-h-screen overflow-y-auto" onclick="event.stopPropagation()">
        @if($title || $closable)
            <div class="modal-header flex justify-between items-center border-b border-gray-100">
                @if($title)
                    <h3 class="text-xl font-600 text-gray-900 font-poppins">{{ $title }}</h3>
                @endif
                @if($closable)
                    <button type="button" class="close-button" onclick="closeModal('{{ $id }}')">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13 1L1 13M1 1L13 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                @endif
            </div>
        @endif
        
        <div class="modal-body">
            {{ $slot }}
        </div>
    </div>
</div>

<script>
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const visibleModal = document.querySelector('.modal-overlay:not(.hidden)');
        if (visibleModal) {
            closeModal(visibleModal.id);
        }
    }
});
</script>

<style>
.modal-overlay {
    backdrop-filter: blur(8px);
    animation: fadeIn 0.2s ease-out;
}

.modal-content {
    animation: slideUp 0.3s ease-out;
    font-family: 'Poppins', sans-serif;
    
}

.wide{
  max-width: 800px;
}

.modal-header h3 {
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from { 
        opacity: 0;
        transform: translateY(20px) scale(0.95);
    }
    to { 
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.font-poppins {
    font-family: 'Poppins', sans-serif;
}

.close-button {
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
    outline: none;
}

.close-button:hover {
    background: #e2e8f0;
    color: #475569;
    transform: scale(1.05);
}

.close-button:active {
    transform: scale(0.95);
}

.close-button svg {
    transition: transform 0.2s ease;
}

.close-button:hover svg {
    transform: rotate(90deg);
}
</style>
