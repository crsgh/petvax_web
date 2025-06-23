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
        $limit = $request->has('limit') ? $request->input('limit') : null;

        $query = Clinic::leftJoin('clinic_ratings', 'clinics.id', '=', 'clinic_ratings.clinic_id')
            ->select('clinics.*')
            ->selectRaw('AVG(clinic_ratings.rating) as stars_average')
            ->where('clinics.status', 'active');

        if ($latitude && $longitude) {
            // Calculate distance using Haversine formula and sort by nearest first
            $query->selectRaw('(
                6371 * acos(
                    cos(radians(?)) * 
                    cos(radians(latitude)) * 
                    cos(radians(longitude) - radians(?)) + 
                    sin(radians(?)) * 
                    sin(radians(latitude))
                )
            ) AS distance', [$latitude, $longitude, $latitude])
            ->orderBy('distance', 'asc'); // Explicitly order by distance ascending
        }

        $clinics = $query->groupBy(
                'clinics.id',
                'clinics.name',
                'clinics.address',
                'clinics.contact',
                'clinics.latitude',
                'clinics.longitude',
                'clinics.status',
                'clinics.created_at',
                'clinics.updated_at',
                'clinics.email',
                'clinics.image',
                'clinics.opening_time',
                'clinics.closing_time',
                'clinics.operation_days',
                'clinics.tags',
                'clinics.description'
            )
            ->when($limit, function($query) use ($limit) {
                return $query->limit($limit);
            })
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $clinics->map(function($clinic) {
                // Round distance to 2 decimal places if it exists
                if (isset($clinic->distance)) {
                    $clinic->distance = round($clinic->distance, 2);
                }
                return $clinic;
            })
        ]);
    }
}
