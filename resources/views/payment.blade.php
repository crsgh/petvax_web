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
    
    .mt-4 {
        margin-top: 1.5rem;
    }
    
    .btn-back-to-bookings {
        display: inline-block;
        background-color: #3b82f6;
        color: white;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 0.375rem;
        text-decoration: none;
        transition: background-color 0.2s;
    }
    
    .btn-back-to-bookings:hover {
        background-color: #2563eb;
    }
</style>

<div class="payment-container">
    <div class="payment-card">
        @if($success)
            <div class="icon-container">
                <x-ui.icon name="check-circle" class="w-16 h-16 text-green-500" />
            </div>
            <h2 class="payment-title">Payment Successful!</h2>
            <p class="payment-message">Thank you for using PetVax</p>
            <div class="mt-4">
                <a href="/bookings" class="btn-back-to-bookings">Back to Bookings</a>
            </div>
        @else
            <div class="icon-container">
                <x-ui.icon name="x-circle" class="w-16 h-16 text-red-500" />
            </div>
            <h2 class="payment-title">Payment Failed</h2>
            <p class="payment-message">There was an error processing your payment. Please try again.</p>
            <div class="mt-4">
                <a href="/bookings" class="btn-back-to-bookings">Back to Bookings</a>
            </div>
        @endif
    </div>
</div>
