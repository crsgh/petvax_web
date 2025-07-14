<?php
use App\Http\Controllers\{
    ChangePasswordController,
    HomeController,
    InfoUserController,
    RegisterController,
    ResetController,
    SessionsController,
	DashboardController,
	ClinicController,
	ServiceController,
	BookingController,
	InventoryController,
	MedicalHistoryController,
	UserController,
	PetController,
	CategoryController,
	RatingController,
	ActivityRecordController,
	BreedController,
	SpecieController,
	ScheduleController,
	NotificationController,
	OTPController,
	RuleBaseController,
};
use Illuminate\Support\Facades\{Route, Password};
use App\Models\{
    Role,
    User,
    Pet,
    Clinic,
    Service,
    Booking,
    MedicalHistory,
    InventoryItem,
	Category,
	Notification,
	ClinicRating,
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::post('/clinics/{id?}', [ClinicController::class, 'upsert'])->name('upsert-clinic');

Route::group(['middleware' => 'auth'], function () {

    Route::get('/', [HomeController::class, 'home']);

	Route::get('/owner', function() {
		$clinics = Clinic::where('status', 'active')->get()	;
        $clinics = $clinics->map(function($clinic) {
			$rating = ClinicRating::where('clinic_id', $clinic->id);
			$clinic->reviews_count = $rating->count();
            $clinic->stars = $rating->avg('rating') ?? 0;
            return $clinic;
        });

		return view('pet-owner',[
			'clinics' => $clinics,
			'services' => Service::all(),
			'pets' => Pet::where('owner_id', auth()->id())->get(),
			'vets' => User::where('role_id', 4)->get(),
			
			'notifications' => match(auth()->user()->role_id) {
                1 => collect([]),
                2, 3 => Notification::where('clinic_id', auth()->user()->clinic_id)->where('is_read', 0)->get(),
                default => Notification::where('user_id', auth()->id())->where('is_read', 0)->get(),
            },
			
		]);
	});

	Route::post('/owner/{id?}', [BookingController::class, 'upsert'])->name('upsert-owner-bookings');

	Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
 
	Route::group(['prefix' => 'clinics'], function () {
		Route::get('/',[ClinicController::class, 'index'])->name('clinics');
		//Route::post('/{id?}', [ClinicController::class, 'upsert'])->name('upsert-clinic');
		Route::get('/{id}/delete', [ClinicController::class, 'delete'])->name('delete-clinic');
	});

	Route::group(['prefix' => 'owners'], function () {
		Route::get('/',[UserController::class, 'owners'])->name('owners');
		Route::post('/{id?}', [UserController::class, 'upsertOwner'])->name('upsert-owners');
		Route::get('/{id}/delete', [UserController::class, 'deleteOwner'])->name('delete-owners');
	});

	Route::group(['prefix' => 'staffs'], function () {
		Route::get('/',[UserController::class, 'staffs'])->name('staffs');
		Route::post('/{id?}', [UserController::class, 'upsertStaff'])->name('upsert-staffs');
		Route::get('/{id}/delete', [UserController::class, 'deleteStaff'])->name('delete-staffs');
	});
	
	Route::group(['prefix' => 'pets'], function () {
		Route::get('/', [PetController::class,'index'])->name('pets');
		Route::post('/{id?}', [PetController::class, 'upsert'])->name('upsert-pet');
		Route::get('/{id}/delete', [PetController::class, 'delete'])->name('delete-pet');
		
	});

	Route::group(['prefix' => 'breeds'], function () {
		Route::get('/', [PetController::class, 'breeds'])->name('breeds');
		Route::post('/{id?}', [PetController::class, 'upsertBreed'])->name('upsert-breed');
		Route::get('/{id}/delete', [PetController::class, 'deleteBreed'])->name('delete-breed');
	});

	Route::group(['prefix' => 'species'], function () {
		Route::get('/', [PetController::class, 'species'])->name('species');
		Route::post('/{id?}', [PetController::class, 'upsertSpecie'])->name('upsert-specie');
		Route::get('/{id}/delete', [PetController::class, 'deleteSpecie'])->name('delete-specie');
	});

	Route::group(['prefix' => 'categories'], function () {
		Route::get('/', [CategoryController::class, 'index'])->name('categories');
		Route::post('/{id?}', [CategoryController::class, 'upsert'])->name('upsert-category');
		Route::get('/{id}/delete', [CategoryController::class, 'delete'])->name('delete-category');
	});
	
	Route::get('/clinic-ratings', [RatingController::class, 'index'])->name('clinic-ratings');
	Route::get('/activity-records', [ActivityRecordController::class, 'index'])->name('activity-records');
	Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
	Route::get('/schedules/duplicate/{id}/{day}', [ScheduleController::class, 'duplicate'])->name('duplicate-schedule');
	


	Route::group(['prefix' => 'services'], function () {
		Route::get('/', [ServiceController::class,'index'])->name('services');
		Route::post('/{id?}', [ServiceController::class, 'upsert'])->name('upsert-services');
		Route::get('/{id}/delete', [ServiceController::class, 'delete'])->name('delete-services');
	});

	Route::group(['prefix' => 'bookings'], function () {
		Route::get('/', [BookingController::class,'index'])->name('bookings');
		Route::post('/action', [BookingController::class, 'action'])->name('update-bookings');
		Route::post('/{id}/cancel', [BookingController::class, 'cancel'])->name('cancel-bookings');
		Route::post('/{id?}', [BookingController::class, 'upsert'])->name('upsert-bookings');
		Route::get('/{id}/delete', [BookingController::class, 'delete'])->name('delete-bookings');
		Route::get('/{id}/approve', [BookingController::class, 'approve'])->name('approve-bookings');
		Route::get('/{id}/decline', [BookingController::class, 'decline'])->name('decline-bookings');
		Route::post('/{id}/decline', [BookingController::class, 'decline'])->name('decline-bookings');
		Route::post('/complete/{id}', [BookingController::class, 'complete'])->name('complete-bookings');
	});
	Route::get('/sales-report', [BookingController::class, 'salesReport'])->name('sales-report');
	Route::group(['prefix' => 'inventory'], function () {
		Route::get('/', [InventoryController::class,'index'])->name('inventory');
		Route::post('/{id?}', [InventoryController::class, 'upsert'])->name('upsert-inventory');
		Route::get('/{id}/delete', [InventoryController::class, 'delete'])->name('delete-inventory');
	});

	Route::group(['prefix' => 'services-schedule'], function () {
		Route::get('/', [ScheduleController::class,'index'])->name('services-schedule');
		Route::post('/{id?}', [ScheduleController::class, 'upsert'])->name('upsert-services-schedule');
		Route::get('/{id}/delete', [ScheduleController::class, 'delete'])->name('delete-services-schedule');
	});

	Route::group(['prefix' => 'medical-histories'], function () {
		Route::get('/', [MedicalHistoryController::class,'index'])->name('medical-histories');
		Route::post('/follow-up', [MedicalHistoryController::class, 'followUp'])->name('follow-up-medical-history');
		Route::post('/{id?}', [MedicalHistoryController::class, 'upsert'])->name('upsert-medical-histories');
		
		Route::get('/{id}/delete', [MedicalHistoryController::class, 'delete'])->name('delete-medical-histories');
	});

	Route::group(['prefix' => 'ratings'], function () {
		Route::get('/', [RatingController::class,'index'])->name('ratings');
		Route::post('/{id?}', [RatingController::class, 'upsert'])->name('upsert-ratings');
		Route::get('/{id}/delete', [RatingController::class, 'delete'])->name('delete-ratings');
	});

	Route::group(['prefix' => 'rule-base'], function () {
		Route::get('/', [RuleBaseController::class, 'index'])->name('rule-base');
		Route::post('/{id?}', [RuleBaseController::class, 'upsert'])->name('upsert-rule-base');
		Route::get('/{id}/delete', [RuleBaseController::class, 'destroy'])->name('delete-rule-base');
	});

    Route::get('static-sign-in', function () {
		return view('static-sign-in');
	})->name('sign-in');

    Route::get('static-sign-up', function () {
		return view('static-sign-up');
	})->name('sign-up');

    Route::get('/logout', [SessionsController::class, 'destroy'])->name('logout');
	Route::get('/user-profile', [InfoUserController::class, 'create']);
	Route::post('/user-profile', [InfoUserController::class, 'store']);
	Route::post('/settings/password', [SessionsController::class, 'updatePassword']);
    Route::get('/login', function () {
		return view('dashboard');
	})->name('sign-up');
});



Route::group(['middleware' => 'guest'], function () {
    Route::get('/register', [RegisterController::class, 'create']);
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login', [SessionsController::class, 'create']);
    Route::post('/session', [SessionsController::class, 'store']);
	Route::get('/login/forgot-password', [ResetController::class, 'create']);
	Route::post('/forgot-password', [ResetController::class, 'sendEmail']);
	Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])->name('password.reset');
	Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])->name('password.update');

});

Route::get('/login', function () {
    return view('session/login-session');
})->name('login');