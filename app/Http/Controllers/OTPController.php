<?php

namespace App\Http\Controllers;
use App\Models\OTP;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OTPController extends Controller
{
    public function sendMail  (Request $request) {
    // Generate a random 4 digit OTP
    $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    $user = User::where('email' , $request->email)->first();
    
    // Create OTP record with 10 minutes expiry
    OTP::create([
        'user_id' => $user->id,
        'otp_code' => $otp,
        'expires_at' => now()->addMinutes(10),
        'is_verified' => false
    ]);
    Mail::raw($request->type == 'forgot-password' ? 'Password Reset OTP ' . $otp : ($request->type == 'signup' ? 'Email Verification OTP ' . $otp : $request->type), function ($message) use ($request) {
        $message->to($request->email)
                ->subject("OTP");
    });
    return response()->json([
        'success' => true,
        'message' => 'Email sent successfully!'
    ]);
}
    public function verify (Request $request) {
    // Find the OTP record
    $user = User::where('email' , $request->email)->first();
    $otpRecord = OTP::where('user_id', $user->id)
                   ->where('otp_code', $request->otp)
                   ->where('is_verified', false)
                   ->first();

    if (!$otpRecord) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid OTP code'
        ], 400);
    }

    // Check if OTP is expired
    if (now()->isAfter($otpRecord->expires_at)) {
        return response()->json([
            'success' => false,
            'message' => 'OTP has expired. Please request a new one.'
        ], 400);
    }

    // Mark OTP as verified
    $otpRecord->update([
        'is_verified' => true
    ]);

    return response()->json([
        'success' => true,
        'message' => 'OTP verified successfully'
    ]);
    }
}
