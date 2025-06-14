<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Clinic;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    function show($id)
    {
        $service = Service::findOrFail($id);
        
        return response()->json([
            'status' => 'success',
            'data' => $service
        ]);
    }

    public function servicesByClinic($clinicId)
    {
        $services = Service::where('clinic_id', $clinicId)->get();

        return response()->json([
            'status' => 'success',
            'data' => $services
        ]);
    }
}
