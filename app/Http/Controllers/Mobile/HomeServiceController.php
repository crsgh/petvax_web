<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\HomeService;
use Illuminate\Http\Request;

class HomeServiceController extends Controller
{
    public function upsert(Request $request, $id = null)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,_id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address' => 'nullable|string',
        ]);

        $homeService = $id ? HomeService::find($id) : new HomeService();
        $homeService->booking_id = $validated['booking_id'];
        $homeService->latitude = $validated['latitude'];
        $homeService->longitude = $validated['longitude'];
        $homeService->address = $validated['address'] ;
        $homeService->save();
        return response()->json([
            'success' => true,
            'data' => $homeService,
        ]);
    }

    public function findByBookingId($booking_id)
    {
        $homeService = HomeService::where('booking_id', $booking_id)->first();

        if (!$homeService) {
            return response()->json([
                'success' => false,
                'message' => 'HomeService not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $homeService,
        ]);
    }
}
