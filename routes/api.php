<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Role;
use App\Models\User;
use App\Models\Pet;
use App\Models\Clinic;
use App\Models\Service;
use App\Models\Booking;

use App\Http\Controllers\Mobile\AuthController;
use App\Http\Controllers\Mobile\ClinicController;
use App\Http\Controllers\Mobile\AppointmentController;
use App\Http\Controllers\Mobile\ServiceController;
use App\Http\Controllers\Mobile\PetController;
use App\Http\Controllers\Mobile\BookingController;
use App\Http\Controllers\Mobile\NotificationController;
use App\Http\Controllers\Mobile\ScheduleController;
use App\Http\Controllers\Mobile\RatingController;
use App\Http\Controllers\Mobile\MedicalHistoryController;
use App\Http\Controllers\Mobile\HomeServiceController;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\Mobile\RuleBaseController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/mail', [OTPController::class, 'sendMail']);

Route::post('/verify', [OTPController::class, 'verify']);

Route::post('/check-email', [AuthController::class, 'checkEmail']);

Route::post('/update-profile', [AuthController::class, 'updateProfile']);


Route::get('/rule-base',[RuleBaseController::class,'getRulebase']);


// Payment callback routes
Route::get('/payment/success', function (Request $request) {
    $booking = Booking::findOrFail($request->id);

    $booking->is_paid = 1;
    $booking->save();

    return view('payment',['success' => true]);
});

Route::get('/payment/closed/{id}', function (Request $request) {
    $booking = Booking::findOrFail($request->id);
    if(!$booking->is_paid){
        $booking->delete();
    }
    return response()->json([
        'success' => true,
        'message' => 'Booking is deleted',
    ]);
});


Route::get('/payment/{id}', function (Request $request) {
    $booking = Booking::findOrFail($request->id);
    return response()->json([
        'success' => $booking->is_paid ? true : false,
        'message' => 'Payment is paid',
    ]);
});



Route::get('/payment/failed', function (Request $request) {
    return response()->json([
        'status' => 'failed', 
        'message' => 'Payment failed',
        'data' => $request->all()
    ]);
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/clinics/{id}/pets', function ($id) {
    return Pet::where("clinic_id", $id)
        ->whereNull('deleted_at')
        ->orderBy('created_at', 'desc')
        ->get();
});

Route::get('/clinics/{id}/services', function ($id) {
    return Service::where("clinic_id", $id)->get();
});

Route::get('/clinics/{id}/staffs', function ( $id) {
    return User::where("clinic_id", $id)->where("role_id",4)->get();
});

Route::get('/clinics/{id}', function ($id) {
    return Clinic::findOrFail($id);
});

Route::get('/clinics/{id}/details', function ($id) {
    $clinic = Clinic::findOrFail($id);
    $services = Service::where('clinic_id', $id)->get();
    
    return response()->json([
        'id' => $clinic->id,
        'name' => $clinic->name,
        'address' => $clinic->address,
        'contact_number' => $clinic->contact,
        'operating_hours' => $clinic->operating_hours ?? '9:00 AM - 5:00 PM',
        'image' => asset('storage/' . $clinic->image),
        'services' => $services
    ]);
});

// User Authentication Routes
Route::post('/login', [AuthController::class, 'login']);

Route::post('/signup', [AuthController::class, 'signup']);

Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail']);

Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Clinic Routes
Route::get('/clinic/all', [ClinicController::class, 'index']);

// Appointment Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::post('/add-booking', [AppointmentController::class, 'store']);
});

// Service Routes
Route::get('/service/{id}', [ServiceController::class, 'show']);
Route::get('/service/clinic/{id}', [ServiceController::class, 'servicesByClinic']);

Route::prefix('pet')->group(function () {
    Route::get('/all', [PetController::class, 'index']);
    Route::get('/details', [PetController::class, 'getDetails']);
    Route::post('/edit/{id}', [PetController::class, 'update']);
    Route::get('/delete/{id}', [PetController::class, 'destroy']);
    Route::get('/owner/{id}', [PetController::class, 'getByOwner']);
    
    Route::post('/add', [PetController::class, 'store']);
   
    Route::post('/remove', [PetController::class, 'destroy']);

}); 

Route::prefix('medical-history')->group(function () {
    Route::get('/{id}', [MedicalHistoryController::class, 'getByPet']);
});


Route::prefix('booking')->group(function () {
    Route::get('/all', [BookingController::class, 'index']);
    
    Route::post('/add', [BookingController::class, 'store']);
    Route::get('/user/{id}', [BookingController::class, 'getBookingsByUser']);
     Route::get('/veterinarian/{id}', [BookingController::class, 'getBookingsByVeterinarian']);
    Route::put('/edit/{id}', [BookingController::class, 'update']);
    Route::delete('/delete/{id}', [BookingController::class, 'destroy']);
    Route::get('/update/{id}/{status}', [BookingController::class, 'updateBookingStatus']);
    
    Route::get('/{id}', [BookingController::class, 'show']);

});

Route::prefix('rate')->group(function () {
    Route::post('/add', [RatingController::class, 'store']);
});

Route::prefix('notification')->group(function () {
    Route::get('/create', [NotificationController::class, 'store']);
    Route::get('/user/{id}', [NotificationController::class, 'getNotificationsByUser']);
    Route::get('/read/all/{userId}', [NotificationController::class, 'readAllNotification']);
    Route::get('/read/{id}', [NotificationController::class, 'readById']);
});

Route::post('/check-slot', [BookingController::class, 'checkSlotAvailability']);
Route::post('/check-schedule', [ScheduleController::class, 'checkScheduleAvailability']);
Route::get('/notifications/read/{id}', [NotificationController::class, 'read']);
Route::prefix('homeservice')->group(function () {
    Route::post('/add/{id?}', [HomeServiceController::class, 'upsert']);
    Route::get('/find/{bookingId}', [HomeServiceController::class, 'findByBookingId']);
});

