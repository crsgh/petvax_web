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
        $limit = $request->input('limit', 10);

        $query = Clinic::leftJoin('clinic_ratings', 'clinics.id', '=', 'clinic_ratings.clinic_id')
            ->select('clinics.*')
            ->selectRaw('AVG(clinic_ratings.rating) as stars_average')
            ->where('clinics.status', 'active');

        if ($latitude && $longitude) {
            $query->addSelect(DB::raw('(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) as distance'))
                ->addBinding([$latitude, $longitude, $latitude], 'select')
                ->orderBy('distance');
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
            ->limit($limit)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $clinics
        ]);
    }
}
