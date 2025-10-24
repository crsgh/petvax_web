<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class ClinicController extends Controller
{
    public function index()
    {
        if(auth()->user()->role_id != 1) {
            return view('clinic-profile',[
                'clinic' => Clinic::where('id', auth()->user()->clinic_id)->first(),
                'notifications' => match(auth()->user()->role_id) {
                1 => collect([]),
                2, 3 => Notification::where('clinic_id', auth()->user()->clinic_id)->where('is_read', 0)->get(),
                default => Notification::where('user_id', auth()->id())->where('is_read', 0)->get(),
            },
            ]); 
        }
        return view('clinics', [
            'clinics' => Clinic::all(),
            'notifications' => match(auth()->user()->role_id) {
                1 => collect([]),
                2, 3 => Notification::where('clinic_id', auth()->user()->clinic_id)->where('is_read', 0)->get(),
                default => Notification::where('user_id', auth()->id())->where('is_read', 0)->get(),
            },
        ]);
    }

    public function upsert(Request $request, $id = null)
    {
        try {
            // if (!is_array($request->tags)) {
            //     $request->merge(['tags' => json_encode( explode(',', $request->tags))]);
            // }
            $validatedData = $request->validate([
                'clinic_name' => 'required|max:255',
                'clinic_address' => 'required|max:255',
                'clinic_phone' => 'required|max:255',
                'clinic_email' => 'required|email|max:255|unique:clinics,email,' . $id,
                'operating_days' => 'nullable|array',
                'opening_time' => 'nullable',
                'closing_time' => 'nullable',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'tags' => 'nullable|string',
                'clinic_status' => 'nullable|in:active,inactive',
            ]);
            $clinic = $id == null ? new Clinic() : Clinic::findOrFail($id);
            
            if ($request->hasFile('clinic_image')) {
                $clinic->image = $request->file('clinic_image')->store('clinics', 'public');
            }
            $clinic->name = $validatedData['clinic_name'];
            $clinic->address = $validatedData['clinic_address'];
            $clinic->contact = $validatedData['clinic_phone'];
            $clinic->email = $validatedData['clinic_email'];
            $clinic->operation_days = json_encode($validatedData['operating_days'] ?? ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
            $clinic->opening_time = $validatedData['opening_time'] ?? '08:00:00';
            $clinic->closing_time = $validatedData['closing_time'] ?? '18:00:00';
            $clinic->latitude = $validatedData['latitude'] ?? '14.5995';
            $clinic->longitude = $validatedData['longitude'] ?? '120.9842';
            $clinic->tags = $validatedData['tags'] ?? null;
            $clinic->status = $validatedData['clinic_status'] ?? 'active';
            $clinic->description = "";

            $clinic->save();

            // Generate random password if new clinic
            if ($id == null && $request->signup) {
                 $password = Str::random(8);
               
                $user = \App\Models\User::create([
                    'name' => $validatedData['clinic_name'] . ' Admin',
                    'email' => $validatedData['clinic_email'],
                    'password' => bcrypt($password),
                    'role_id' => 2,
                    'clinic_id' => $clinic->id 
                ]);
                
                // Send password email to clinic
                $htmlContent = '
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Welcome to PetVax</title>
  <style>
    body {
      font-family: \'Segoe UI\', sans-serif;
      background-color: #f9f9f9;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 600px;
      background-color: #ffffff;
      margin: 40px auto;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 12px rgba(0,0,0,0.08);
    }
    h1 {
      color: #2c3e50;
      text-align: center;
    }
    .highlight {
      background-color: #eaf7f3;
      border-left: 6px solid #2ecc71;
      padding: 15px;
      font-size: 18px;
      margin: 20px 0;
      border-radius: 6px;
    }
    p {
      color: #555;
      line-height: 1.6;
    }
    .button {
      display: inline-block;
      background-color:rgb(0, 255, 21);
      color: #ffffff;
      padding: 12px 20px;
      text-decoration: none;
      border-radius: 8px;
      margin-top: 20px;
      font-weight: bold;
    }
    .footer {
      margin-top: 30px;
      text-align: center;
      color: #999;
      font-size: 14px;
    }
    .logo {
      display: block;
      margin: 0 auto 20px;
      max-width: 120px;
    }
  </style>
</head>
<body>
  <div class="container">
    <img src="https://petvax.shop/logo.png" alt="PetVax Logo" class="logo" />
    <h1>Welcome to PetVax! 🐾</h1>
    <p>Thank you for registering your veterinary clinic with <strong>PetVax</strong>, your modern platform for comprehensive pet health and service management.</p>
    
    <p>We’re excited to have you join our growing network of trusted clinics helping pet parents access care faster and easier.</p>

    <div class="highlight">
      <strong>Your temporary password:</strong> <br/>
      <code style="font-size: 20px;">' . $password . '</code>
    </div>

    <p>Please use this password to log in for the first time. For your security, we recommend changing it immediately after your initial login.</p>

    <a href="https://petvax.shop/login" class="button">Login to Your Account</a>

    <hr style="margin: 30px 0;" />

    <h3>What you can do next:</h3>
    <ul>
      <li>✅ Complete your profile</li>
      <li>📍 Enable location in your mobile app</li>
    </ul>

    <p>Need help getting started? Feel free to reach out anytime — we\'re always here for you.</p>

    <p>Welcome once again to PetVax — where modern pet care begins! 🐶🐱</p>

    <div class="footer">
      <p>PetVax Clinic Support Team</p>
      <p><a href="https://www.petvax.shop">www.petvax.shop</a> | 📧 techiesfit@gmail.com </p>
    </div>
  </div>
</body>
</html>
';

Mail::html($htmlContent, function ($message) use ($validatedData) {
    $message->to($validatedData['clinic_email'])
            ->subject("Welcome to PetVax – Your Clinic Account Details");
});
            }

            if ($request->signup){
                return back()->with('success', 'Clinic saved successfully');
            }
            else{
                return response()->json(['message' => 'Clinic saved successfully'], 200);
            }

            // add record
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->signup) {
                return back()->withErrors($e->errors())->withInput();
            } else {
                return response()->json(['errors' => $e->errors()], 422);
            }
        } catch (\Exception $e) {
            if ($request->signup) {
                return back()->with('error', 'Failed to save clinic: ' . $e->getMessage())->withInput();
            } else {
                return response()->json(['error' => 'Failed to save clinic: ' . $e->getMessage()], 500);
            }
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $clinic = Clinic::findOrFail($id);
            $clinic->delete();
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Clinic deleted successfully']);
            }
            
            return redirect()->route('clinics')->with('success', 'Clinic deleted successfully');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to delete clinic: ' . $e->getMessage()], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to delete clinic: ' . $e->getMessage());
        }
    }
}
