<?php
namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\ClinicRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class RatingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rate' => 'required|integer|min:1|max:5',
            'clinic_id' => 'required|exists:clinics,id',
            'comment' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
            'is_anonymous' => 'sometimes|boolean',
        ]);

        $rating = new ClinicRating();
        $rating->rating = $validated['rate'];
        $rating->clinic_id = $validated['clinic_id'];
        $rating->comment = $validated['comment'] ?? null;
        $rating->is_anonymous = $validated['is_anonymous'] ?? false;
        $rating->user_id =$validated['user_id'];

        if ($request->has('booking_id')) {
            // Update the related booking's 'rate' field instead of 'stars'
            $booking = \App\Models\Booking::find($request->input('booking_id'));
            if ($booking) {
                $booking->stars = $validated['rate'];
                $booking->save();
            }
        }

        $rating->save();

        //  \App\Models\Notification::create([
        //     'user_id'   => $validated['user_id'],
        //     'clinic_id' => $validated['clinic_id'],
        //     'title'     => 'New Clinic Rating',
        //     'message'   => 'Your clinic has received a new rating of ' . $validated['rate'] . ' stars.',
        //     'type'      => 'clinic_rating',
        //     'is_read'   => 0,
        //     'for_user'  => false,
        //     'pet_id'    => null,
        //     ]);
        

        return response()->json([
            'status' => 'success',
            'data' => $rating
        ]);
    }
}