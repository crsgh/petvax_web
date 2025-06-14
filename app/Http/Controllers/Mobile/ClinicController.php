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

        // Get clinics ordered by distance if coordinates provided
        if ($latitude && $longitude) {
            $clinics = Clinic::select(DB::raw('*, 
                ( 6371 * acos( cos( radians(?) ) *
                    cos( radians( latitude ) ) *
                    cos( radians( longitude ) - radians(?) ) +
                    sin( radians(?) ) *
                    sin( radians( latitude ) )
                ) ) AS distance'))
                ->addBinding($latitude, 'select')
                ->addBinding($longitude, 'select')
                ->addBinding($latitude, 'select')
                ->where('status', 'active')
                ->orderBy('distance', 'asc')
                ->get();
        } else {
            // If no coordinates, return all active clinics
            $clinics = Clinic::where('status', 'active')->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $clinics
        ]);
    }
}
