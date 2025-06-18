<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Notification;
use App\Models\ActivityRecord;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['clinic', 'staff', 'client', 'pet', 'service'])->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $bookings
        ]);
    }

    public function store(Request $request)
    {
        try {
            // $validated = $request->validate([
            // 'clinic_id' => 'required|exists:clinics,id',
            // 'staff_id' => 'nullable|exists:users,id',
            // 'pet_id' => 'required|exists:pets,id',
            // 'client_id' => 'required|exists:users,id',
            // 'service_id' => 'required|exists:services,id',
            // 'appointment_datetime' => 'required|date',
            // 'status' => 'required|string',
            // 'notes' => 'nullable|string',
            // 'total_amount' => 'nullable|numeric',
            // 'payment_method' => 'nullable|string',
            // 'is_paid' => 'nullable|boolean',
            // 'payment_reference' => 'nullable|string',
            // 'payment_proof' => 'nullable|file|image|max:5120',
            // ]);

            // Handle payment_proof image upload
            if ($request->hasFile('payment_proof')) {
                $fileUrl = $this->uploadImage($request->file('payment_proof'), 'payment_proofs');
                $request->merge(['payment_proof' => $fileUrl]);
            }

            $booking = Booking::create($request->all());

            // send notif to clinic
            // Notification::create([
            //     'user_id' => $validated['client_id'],
            //     'clinic_id' => $validated['clinic_id'],
            //     'title' => 'New Booking',
            //     'message' => 'You have a new booking for your clinic',
            //     'type' => 'booking',
            //     'is_read' => 0,
            //     'for_user' => 0,
            //     'pet_id' => null,
            // ]);

            Notification::create([
                'user_id' => $booking->client_id,
                'clinic_id' => $booking->clinic_id,
                'title' => 'Booking Submitted',
                'message' => $booking->payment_proof
                    ? 'New Booking with payment proof has been submitted for your clinic.'
                    : 'New Booking has been submitted for your clinic.',
                'type' => 'booking',
                'is_read' => 0,
                'for_user' => false,
                'pet_id' => $booking->pet_id,
            ]);

            return response()->json([
            'status' => 'success',
            'message' => 'Booking created successfully',
            'data' => $booking->load(['clinic', 'staff', 'pet', 'client', 'service'])
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
            'status' => 'error',
            'message' => 'Failed to create booking.',
            'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getBookingsByUser($userId, Request $request)
    {
        $query = Booking::with(['clinic', 'staff', 'client', 'pet', 'service'])
            ->where('client_id', $userId)
            ->orderBy('appointment_datetime', 'desc');

        if ($request->has('limit')) {
            $query->limit($request->limit);
        }

        $bookings = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $bookings
        ]);
    }

    public function getBookingsByVeterinarian($vetId, Request $request)
    {
        $query = Booking::with(['clinic', 'staff', 'client', 'pet', 'service'])
            ->where('staff_id', $vetId)
            ->orderBy('appointment_datetime', 'desc');

        if ($request->has('limit')) {
            $query->limit($request->limit);
        }

        $bookings = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $bookings
        ]);
    }

    public function cancel($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->status = 'cancelled';
        $booking->save();

        Notification::create([
            'user_id' => $booking->client_id,
            'clinic_id' => $booking->clinic_id,
            'title' => 'Booking Cancelled',
            'message' => 'A customer has cancelled their booking.',
            'type' => 'booking',
            'is_read' => 0,
            'for_user' => false,
            'pet_id' => $booking->pet_id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Booking cancelled successfully',
            'data' => $booking->load(['clinic', 'staff', 'client', 'pet', 'service'])
        ]);
    }

    public function updateBookingStatus($id, $status)
    {
        $booking = Booking::findOrFail($id);
        
        // Validate status is one of the allowed values
        $allowedStatuses = ['pending', 'confirmed', 'cancelled', 'completed'];
        if (!in_array(strtolower($status), $allowedStatuses)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid booking status provided'
            ], 422);
        }

        $booking->status = strtolower($status);
        $booking->save();

        $message = ucfirst($booking->status) . ' booking successfully';

        Notification::create([
            'user_id' => $booking->client_id,
            'clinic_id' => $booking->clinic_id,
            'title' => 'Booking Status Updated',
            'message' => 'The status of your booking has been updated to ' . ucfirst($booking->status) . '.',
            'type' => 'booking',
            'is_read' => 0,
            'for_user' => auth()->check() && auth()->user()->role_id == 5,
            'pet_id' => $booking->pet_id,
        ]);

        if (auth()->check() && auth()->user()->role_id != 5) {
            
            ActivityRecord::create([
                'user_id' => auth()->id(),
                'activity' => 'Updated booking status to ' . ucfirst($booking->status) . ' for booking ID ' . $booking->id,
                'booking_id' => $booking->id,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $booking->load(['clinic', 'staff', 'client', 'pet', 'service'])
        ]);
    }

    public function show($id)
    {
        $booking = Booking::with(['clinic', 'staff', 'client', 'pet', 'service'])->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $booking
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $booking = Booking::findOrFail($id);

            $validated = $request->validate([
                'clinic_id' => 'required|exists:clinics,id',
                'staff_id' => 'nullable|exists:users,id',
                'pet_id' => 'required|exists:pets,id',
                'client_id' => 'required|exists:users,id',
                'appointment_datetime' => 'required|date',
                'status' => 'required|string',
                'notes' =>'nullable|string',
                'total_amount' =>'nullable|numeric',
                'payment_method' => 'nullable|string',
                'is_paid' => 'nullable|boolean',
                'service_id' => 'required|exists:services,id',
            ]);

            $booking->update($validated);

            Notification::create([
                'user_id' => $booking->client_id,
                'clinic_id' => $booking->clinic_id,
                'title' => 'Booking Updated',
                'message' => 'Your booking details have been updated.',
                'type' => 'booking',
                'is_read' => 0,
                'for_user' => true,
                'pet_id' => $booking->pet_id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Booking updated successfully',
                'data' => $booking->load(['clinic', 'assignedStaff', 'pet', 'owner'])
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update booking.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();



        return response()->json([
            'status' => 'success',
            'message' => 'Booking deleted successfully'
        ]);
    }

    public function checkSlotAvailability(Request $request)
    {

        
        try {
            $validated = $request->validate([
            'date' => 'required|date',
            'time' => 'required|string',
            'clinic_id' => 'required|exists:clinics,id',
            'service_id' => 'required|exists:services,id',
            ]);

            // Get the day of the week from the date (0 = Sunday, 6 = Saturday)
            $dayOfWeek = strtolower(date('l', strtotime($validated['date']))); // e.g., 'monday'

        

            $schedule = Schedule::where('clinic_id', $validated['clinic_id'])
                ->where('service_id', $validated['service_id'])
                ->where('day', $dayOfWeek)
                ->where('status', 1)
                ->first();

            if (!$schedule) {
                return response()->json([
                    'status' => 'error',
                    'available' => false,
                    'message' => 'No schedule found for this clinic and service.'
                ], 404);
            }

            $scheduleData = json_decode($schedule->time_slots, true);

            $time = $validated['time'];
            if (preg_match('/^(\d):(\d{2})\s*(AM|PM)$/i', $time, $matches)) {
                
                $time = '0' . $matches[1] . ':' . $matches[2] . ' ' . strtoupper($matches[3]);
            }


        

            if (
                
                !in_array($time, $scheduleData)
            ) {
                return response()->json([
                    'status' => 'success',
                    'available' => false,
                    'message' => 'Service is not available on the selected day.'
                ], 422);
            }

         

            // Combine date and time into a datetime string
            $appointmentDatetime = date('Y-m-d H:i:s', strtotime($validated['date'] . ' ' . $validated['time']));

            $exists = Booking::where('clinic_id', $validated['clinic_id'])
            ->where('service_id', $validated['service_id'])
            ->whereDate('appointment_datetime', date('Y-m-d', strtotime($validated['date'])))
            ->whereTime('appointment_datetime', 'like', date('H:i', strtotime($validated['time'])) . '%')
           
            ->exists();

            return response()->json([
            'status' => 'success',
            'available' => !$exists,
            'message' => !$exists ? 'Time slot is available.' : 'Time slot is already taken.'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
            'status' => 'error',
            'available' => false,
            'message' => 'Failed to check slot availability.',
            'error' => $e->getMessage()
            ], 500);
        }
    }
}
