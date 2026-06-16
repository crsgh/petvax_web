<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClinicController extends Controller
{
    public function index(Request $request)
    {
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $limit = $request->has('limit') ? (int) $request->input('limit') : null;

        $clinics = Clinic::with('ratings')
            ->where('status', 'active')
            ->get()
            ->map(function ($clinic) use ($latitude, $longitude) {
                $clinic->stars_average = $clinic->ratings->isNotEmpty()
                    ? round($clinic->ratings->avg('rating'), 2)
                    : null;

                if ($latitude && $longitude) {
                    $clinic->distance = round($this->haversineDistance(
                        $latitude, $longitude, $clinic->latitude, $clinic->longitude
                    ), 2);
                }

                unset($clinic->ratings);

                return $clinic;
            });

        if ($latitude && $longitude) {
            $clinics = $clinics->sortBy('distance')->values();
        }

        if ($limit) {
            $clinics = $clinics->take($limit)->values();
        }

        return response()->json([
            'status' => 'success',
            'data' => $clinics
        ]);
    }

    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadiusKm = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        return $earthRadiusKm * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
