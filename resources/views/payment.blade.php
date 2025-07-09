<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap');

    .payment-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f3f4f6;
        padding: 1rem;
        font-family: 'Inter', sans-serif;
    }

    .payment-card {
        width: 100%;
        max-width: 384px;
        background-color: white;
        padding: 1.75rem;
        text-align: center;
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .icon-container {
        margin: 0 auto;
        width: 8rem;
        height: 8rem;
        margin-bottom: 1.25rem;
    }

    .success-icon {
        width: 100%;
        height: 100%;
        color: #10b981;
    }

    .error-icon {
        width: 100%;
        height: 100%;
        color: #ef4444;
    }

    .payment-title {
        margin-bottom: 0.75rem;
        font-size: 1.625rem;
        font-weight: 600;
        color: #1f2937;
        letter-spacing: -0.025em;
    }

    .payment-message {
        color: #4b5563;
        font-size: 1.125rem;
        line-height: 1.6;
    }
</style>

<div class="payment-container">
    <div class="payment-card">
        @if($success)
            <div class="icon-container">
                <svg xmlns="http://www.w3.org/2000/svg" class="success-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h2 class="payment-title">Payment Successful!</h2>
            <p class="payment-message">Thank you for using PetVax</p>
        @else
            <div class="icon-container">
                <svg xmlns="http://www.w3.org/2000/svg" class="error-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h2 class="payment-title">Payment Failed</h2>
            <p class="payment-message">There was an error processing your payment. Please try again.</p>
        @endif
    </div>
</div>
