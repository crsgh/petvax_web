<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\ActivityRecord;
use App\Models\Pet;
use App\Models\Service;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    // PayMongo API credentials
    private $secretKey;
    private $apiUrl = 'https://api.paymongo.com/v1';
    
    public function __construct()
    {
        // Set your PayMongo secret key from environment variable
        // You should add PAYMONGO_SECRET_KEY to your .env file
        $this->secretKey = env('PAYMONGO_SECRET_KEY', 'sk_test_tjT14yUCvcKpYs9Q8DrQnY2f');
    }
    
    /**
     * Create a payment session with PayMongo
     */
    public function createPayment(Request $request)
    {
        try {
            // Validate request data
            $validated = $request->validate([
                'amount' => 'required|numeric|min:100', // Minimum amount in centavos (1 PHP)
                'pet_id' => 'required|exists:pets,id',
                'service_id' => 'required|exists:services,id',
                'staff_id' => 'required|exists:users,id',
                'appointment_date' => 'required|date',
                'notes' => 'nullable|string',
                'clinic_id' => 'required|exists:clinics,id',
                'client_id' => 'required|exists:users,id',
                'payment_method' => 'required|string'
            ]);
            
            // Get pet and service details
            $pet = Pet::findOrFail($validated['pet_id']);
            $service = Service::findOrFail($validated['service_id']);
            
            // Create a new booking record with pending status
            $booking = new Booking();
            $booking->pet_id = $validated['pet_id'];
            $booking->clinic_id = $validated['clinic_id'];
            $booking->client_id = $validated['client_id'];
            $booking->service_id = $validated['service_id'];
            $booking->staff_id = $validated['staff_id'];
            $booking->appointment_datetime = $validated['appointment_date'];
            $booking->notes = $validated['notes'] ?? '';
            $booking->total_amount = $validated['amount'] / 100; // Convert from centavos to PHP
            $booking->payment_method = $validated['payment_method'];
            $booking->status = 'pending';
            $booking->is_paid = 0;
            $booking->save();
            
            // Create a unique reference number for this transaction
            $referenceNumber = 'PETVAX-' . $booking->id . '-' . Str::random(8);
            
            // Create a PayMongo payment session
            $response = Http::withBasicAuth($this->secretKey, '')
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->post($this->apiUrl . '/checkout_sessions', [
                    'data' => [
                        'attributes' => [
                            'line_items' => [
                                [
                                    'name' => $service->name,
                                    'quantity' => 1,
                                    'amount' => (int)$validated['amount'],
                                    'currency' => 'PHP',
                                    'description' => 'Appointment for ' . $pet->name
                                ]
                            ],
                            'payment_method_types' => ['card', 'gcash', 'grab_pay', 'paymaya'],
                            'send_email_receipt' => true,
                            'show_description' => true,
                            'show_line_items' => true,
                            'reference_number' => $referenceNumber,
                            'success_url' => url('/api/payment/success?id=' . $booking->id),
                            'cancel_url' => url('/api/payment/failed?booking_id=' . $booking->id),
                            'description' => 'Payment for ' . $service->name . ' service'
                        ]
                    ]
                ]);
            
            $responseData = $response->json();
            
            if ($response->successful() && isset($responseData['data']['id'])) {
                // Update booking with payment session ID
                $booking->payment_reference = $responseData['data']['id'];
                $booking->save();
                
                // Create notification for new booking
                Notification::create([
                    'user_id' => $booking->client_id,
                    'clinic_id' => $booking->clinic_id,
                    'pet_id' => $booking->pet_id,
                    'title' => 'Booking Created',
                    'message' => 'Your booking has been created. Please complete the payment.',
                    'type' => 'booking',
                    'for_user' => 1,
                    'is_read' => 0,
                ]);
                
                // Notification for clinic staff
                Notification::create([
                    'clinic_id' => $booking->clinic_id,
                    'pet_id' => $booking->pet_id,
                    'title' => 'New Booking',
                    'message' => 'New booking has been created pending payment.',
                    'type' => 'booking',
                    'for_user' => 0,
                    'is_read' => 0,
                ]);
                
                // Record activity
                ActivityRecord::create([
                    'user_id' => $booking->client_id,
                    'clinic_id' => $booking->clinic_id,
                    'action' => 'created booking',
                    'description' => 'Created a new booking for pet ' . $pet->name . ' with online payment',
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Payment session created successfully',
                    'payment_id' => $responseData['data']['id'],
                    'checkout_url' => $responseData['data']['attributes']['checkout_url'],
                    'booking_id' => $booking->id
                ]);
            } else {
                // Delete the booking if payment session creation failed
                $booking->delete();
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create payment session',
                    'error' => $responseData['errors'] ?? 'Unknown error'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Verify payment status with PayMongo
     */
    public function verifyPayment($sessionId)
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->withHeaders([
                    'Accept' => 'application/json'
                ])
                ->get($this->apiUrl . '/checkout_sessions/' . $sessionId);
            
            $responseData = $response->json();
            
            if ($response->successful() && isset($responseData['data'])) {
                $paymentStatus = $responseData['data']['attributes']['payment_intent']['attributes']['status'] ?? 'unknown';
                
                return response()->json([
                    'success' => true,
                    'status' => $paymentStatus,
                    'data' => $responseData['data']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to verify payment',
                    'error' => $responseData['errors'] ?? 'Unknown error'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}