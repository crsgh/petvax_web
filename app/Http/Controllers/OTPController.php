<?php

namespace App\Http\Controllers;
use App\Models\OTP;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class OTPController extends Controller
{
    public function sendMail  (Request $request) {
    // Generate a random 4 digit OTP
    $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    $user = User::where('email' , $request->email)->first();

    // Don't leak whether an account exists; just fail cleanly if there is none.
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'No account is associated with this email address.'
        ], 404);
    }

    // Create OTP record with 10 minutes expiry
    OTP::create([
        'user_id' => $user->id,
        'otp_code' => $otp,
        'expires_at' => now()->addMinutes(10),
        'is_verified' => false
    ]);

    $subject = $request->type == 'forgot-password' ? 'PetVax Password Reset Code' : 'PetVax Verification Code';
    $body = "Hi " . ($user->name ?? 'there') . ",\n\n"
          . "Your PetVax " . ($request->type == 'forgot-password' ? 'password reset' : 'verification') . " code is:\n\n"
          . "    " . $otp . "\n\n"
          . "This code expires in 10 minutes. If you didn't request it, you can ignore this email.\n\n"
          . "- PetVax";

    // A slow or failing SMTP send must not 500 the serverless function.
    try {
        Mail::raw($body, function ($message) use ($request, $subject) {
            $message->to($request->email)
                    ->subject($subject);
        });
    } catch (\Throwable $e) {
        Log::error('OTP email send failed: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'We could not send the email right now. Please try again in a moment.'
        ], 502);
    }

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

    // Update password if new password is provided
    if ($request->has('new_password')) {
        $user->update([
            'password' => bcrypt($request->new_password)
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'OTP verified successfully' . ($request->has('new_password') ? ' and password updated' : '')
    ]);
    }
}
