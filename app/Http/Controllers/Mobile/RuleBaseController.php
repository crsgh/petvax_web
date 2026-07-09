<?php
namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\ClinicRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RuleBase;


class RuleBaseController extends Controller
{
    public function getRulebase(){
        $rulebases = RuleBase::all();
        return response()->json([
            'status' => 'success',
            'data' => $rulebases
        ]);
    }
}