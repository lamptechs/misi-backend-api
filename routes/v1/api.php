<?php


use App\Http\Controllers\V1\AdminController;
use App\Http\Controllers\V1\AppointmentController;
use App\Http\Controllers\V1\Therapist\TherapistController;
use App\Http\Controllers\V1\Therapist\TherapistDegreeController;
use App\Http\Controllers\V1\Therapist\TherapistServiceController;
use App\Http\Controllers\V1\BloodGroupController;
use App\Http\Controllers\V1\CountryController;
use App\Http\Controllers\V1\OccupationController;
use App\Http\Controllers\V1\PatientController;
use App\Http\Controllers\V1\ServiceCategoryController;
use App\Http\Controllers\V1\ServiceSubCategoryController;
use App\Http\Controllers\V1\StateController;
use App\Http\Controllers\V1\TherapistTypeController;
use App\Http\Controllers\V1\TicketDepartmentController;
use App\Http\Controllers\V1\TicketController;
use App\Http\Controllers\V1\DegreeController;
use App\Http\Controllers\V1\QuestionController;
use App\Http\Controllers\V1\GroupController;
use App\Http\Controllers\V1\Therapist\TherapistScheduleController;
use App\Http\Controllers\V1\PibFormulaController;
use App\Http\Controllers\V1\PitFormulaController;
use App\Http\Controllers\V1\PibScaleController;
use App\Http\Controllers\V1\PitScaleController;
use App\Http\Controllers\V1\TicketHistoryActivityController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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

/**
 * Admin Login Section
 */
Route::get('admin/login', [AdminController::class, "showLogin"]);
Route::post('admin/login', [AdminController::class, "login"]);
Route::post('admin/logout', [AdminController::class, "logout"]);
Route::get('admin/adminview', [AdminController::class, "index"]);
Route::get('/show', [AdminController::class, 'show']);
Route::post('admin/store', [AdminController::class, "store"]);
Route::post('/admin/update/{id}', [AdminController::class, 'update']);
Route::post('/admin/delete/{id}', [AdminController::class, 'destroy']);

/**
 * Protect the Route Throw API Token
 */
Route::middleware(["auth:admin"])->group(function(){

        //Ticket History Activity Section
        Route::prefix('tickethistory')->group(function(){
            Route::get('/', [TicketHistoryActivityController::class, 'index']);
            Route::get('/show', [TicketHistoryActivityController::class, 'show']);
            Route::post('/store', [TicketHistoryActivityController::class, 'store']);
            Route::post('/update/{id}', [TicketHistoryActivityController::class, 'update']);
            Route::post('/delete/{id}', [TicketHistoryActivityController::class, 'destroy']);
        });

        //Pit Scale Section
        Route::prefix('pitscale')->group(function(){
            Route::get('/', [PitScaleController::class, 'index']);
            Route::get('/show', [PitScaleController::class, 'show']);
            Route::post('/store', [PitScaleController::class, 'store']);
            Route::post('/update/{id}', [PitScaleController::class, 'update']);
            Route::post('/delete/{id}', [PitScaleController::class, 'destroy']);
        });

        //Pib Scale Section
        Route::prefix('pibscale')->group(function(){
            Route::get('/', [PibScaleController::class, 'index']);
            Route::get('/show', [PibScaleController::class, 'show']);
            Route::post('/store', [PibScaleController::class, 'store']);
            Route::post('/update/{id}', [PibScaleController::class, 'update']);
            Route::post('/delete/{id}', [PibScaleController::class, 'destroy']);
        });


    //Pit Formula Section
    Route::prefix('pit')->group(function(){
        Route::get('/', [PitFormulaController::class, 'index']);
        Route::get('/show', [PitFormulaController::class, 'show']);
        Route::post('/store', [PitFormulaController::class, 'store']);
        Route::post('/update/{id}', [PitFormulaController::class, 'update']);
        Route::post('/delete/{id}', [PitFormulaController::class, 'destroy']);
    });

    //Pib Formula Section
    Route::prefix('pib')->group(function(){
        Route::get('/', [PibFormulaController::class, 'index']);
        Route::get('/show', [PibFormulaController::class, 'show']);
        Route::post('/store', [PibFormulaController::class, 'store']);
        Route::post('/update/{id}', [PibFormulaController::class, 'update']);
        Route::post('/delete/{id}', [PibFormulaController::class, 'destroy']);
    });
    //Patient Create
    Route::prefix('patient')->group(function(){
        Route::get('', [PatientController::class, 'index']);
        Route::get('/show', [PatientController::class, 'show']);
        Route::post('/store', [PatientController::class, 'store']);
        Route::post('/update/{id}', [PatientController::class, 'update']);
        Route::post('/delete/{id}', [PatientController::class, 'destroy']);
    });

    //Group Create

    Route::get('/groups',[GroupController::class,'index']);
    Route::get('/groups/show', [GroupController::class, 'show']);
    Route::post('/groups/store',[GroupController::class,'store']);
    Route::post('/groups/update/{id}',[GroupController::class,'update']);
    Route::post('/groups/delete/{id}',[GroupController::class,'destroy']);
    //Group Create
    // Route::prefix('groups')->group(function(){
    //     Route::get('', [GroupController::class, 'index']);
    //     Route::get('/show', [GroupController::class, 'show']);
    //     Route::post('/store', [GroupController::class, 'store']);
    //     Route::post('/update/{id}', [GroupController::class, 'update']);
    //     Route::post('/delete/{id}', [GroupController::class, 'destroy']);
    // });
    //Therapist Type
    Route::get('/therapist_type', [TherapistTypeController::class, 'index']);
    Route::post('/therapist_type/store', [TherapistTypeController::class, 'store']);
    Route::post('/therapist_type/update/{id}', [TherapistTypeController::class, 'update']);
    Route::post('/therapist_type/delete/{id}', [TherapistTypeController::class, 'destroy']);

    //Ticket Department
    Route::prefix('ticket_department')->group(function(){
        Route::get('/', [TicketDepartmentController::class, 'index']);
        Route::get('/show', [TicketDepartmentController::class, 'show']);
        Route::post('/store', [TicketDepartmentController::class, 'store']);
        Route::post('/update', [TicketDepartmentController::class, 'update']);
        Route::post('/delete/{id}', [TicketDepartmentController::class, 'destroy']);
    });

    //Ticket
    Route::prefix('ticket')->group(function(){
        Route::get('/', [TicketController::class, 'index']);
        Route::get('/show', [TicketController::class, 'show']);
        Route::post('/store', [TicketController::class, 'store']);
        Route::post('/update', [TicketController::class, 'update']);
        Route::post('/assignedupdate', [TicketController::class, 'assignedupdate']);
        Route::post('/ticketstatus', [TicketController::class, 'assignedticketstatus']);
        Route::post('/delete/{id}', [TicketController::class, 'destroy']);
    });


    //Therapist Section
    Route::prefix('therapist')->group(function(){
        Route::get('/', [TherapistController::class, 'index']);
        Route::get('/show', [TherapistController::class, 'show']);
        Route::post('/store', [TherapistController::class, 'store']);
        Route::post('/update/{id}', [TherapistController::class, 'update']);
        Route::post('/delete/{id}', [TherapistController::class, 'destroy']);
    });
    //Therapist Service
    Route::prefix('therapistService')->group(function(){
        Route::get('/', [TherapistServiceController::class, 'index']);
        Route::get('/show', [TherapistServiceController::class, 'show']);
        Route::post('/store', [TherapistServiceController::class, 'store']);
        Route::post('/update/{id}', [TherapistServiceController::class, 'update']);
        Route::post('/delete/{id}', [TherapistServiceController::class, 'destroy']);
    });

    //Therapist Degree
    Route::prefix('therapist_degree')->group(function(){
        Route::get('/', [TherapistDegreeController::class, 'index']);
        Route::get('/show', [TherapistDegreeController::class, 'show']);
        Route::post('/store', [TherapistDegreeController::class, 'store']);
        Route::post('/update/{id}', [TherapistDegreeController::class, 'update']);
        Route::post('/delete/{id}', [TherapistDegreeController::class, 'destroy']);
    });


    //Therapist Schedule
    Route::prefix('therapist-schedule')->group(function () {
        Route::get('/list', [TherapistScheduleController::class, 'index']);
        Route::get('/create', [TherapistScheduleController::class, 'create']);
        Route::post('/create', [TherapistScheduleController::class, 'store']);
        Route::get('/show', [TherapistScheduleController::class, 'show']);
        Route::post('delete', [TherapistScheduleController::class, 'destroy']);
    });

    //Appointment
    Route::prefix('appointment')->group(function(){
        Route::get('/', [AppointmentController::class, 'index']);
        Route::get('/show', [AppointmentController::class, 'show']);
        Route::post('/store', [AppointmentController::class, 'store']);
        Route::post('/update/{id}', [AppointmentController::class, 'update']);
        Route::post('/delete/{id}', [AppointmentController::class, 'destroy']);
    });

    //Question
    Route::get('/question', [QuestionController::class, 'index']);
    Route::post('/question/store', [QuestionController::class, 'store']);
    Route::post('/question/update/{id}', [QuestionController::class, 'update']);
    Route::post('/question/delete/{id}', [QuestionController::class, 'destroy']);
    // Route::post('/appointment/update/{id}', [AppointmentController::class, 'update']);
    // Route::post('/appointment/delete/{id}', [AppointmentController::class, 'destroy']);

    //Question and scale
    // Route::get('/questionscale', [QuestionScaleController::class, 'index']);
    // Route::post('/questionscale/store', [QuestionScaleController::class, 'store']);
    // Route::post('/appointment/update/{id}', [AppointmentController::class, 'update']);
    // Route::post('/appointment/delete/{id}', [AppointmentController::class, 'destroy']);

    //Pib Formula
    Route::get('/formula', [PibFormulaController::class, 'index']);
    Route::post('/formula/store', [PibFormulaController::class, 'store']);
    // Route::post('/appointment/update/{id}', [AppointmentController::class, 'update']);
    // Route::post('/appointment/delete/{id}', [AppointmentController::class, 'destroy']);

    //Service Category
    Route::get('/service', [ServiceCategoryController::class, 'index']);
    Route::get('/service/show', [ServiceCategoryController::class, 'show']);
    Route::post('/service/store', [ServiceCategoryController::class, 'store']);
    Route::post('/service/update/{id}', [ServiceCategoryController::class, 'update']);
    Route::post('/service/delete/{id}', [ServiceCategoryController::class, 'destroy']);

    //Service SubCategory
    Route::get('/subservice', [ServiceSubCategoryController::class, 'index']);
    Route::get('/subservice/show', [ServiceSubCategoryController::class, 'show']);
    Route::post('/subservice/store', [ServiceSubCategoryController::class, 'store']);
    Route::post('/subservice/update/{id}', [ServiceSubCategoryController::class, 'update']);
    Route::post('/subservice/delete/{id}', [ServiceSubCategoryController::class, 'destroy']);

    //Occupation
    Route::get('/occupation', [OccupationController::class, 'index']);
    Route::get('/occupation/show', [OccupationController::class, 'show']);
    Route::post('/occupation/store', [OccupationController::class, 'store']);
    Route::post('/occupation/update/{id}', [OccupationController::class, 'update']);
    Route::post('/occupation/delete/{id}', [OccupationController::class, 'destroy']);

    //Blood Group
    Route::get('/blood_group', [BloodGroupController::class, 'index']);
    Route::get('/blood_group/show', [BloodGroupController::class, 'show']);
    Route::post('/blood_group/store', [BloodGroupController::class, 'store']);
    Route::post('/blood_group/update/{id}', [BloodGroupController::class, 'update']);
    Route::post('/blood_group/delete/{id}', [BloodGroupController::class, 'destroy']);
    //State
    Route::get('/state', [StateController::class, 'index']);
    Route::get('/state/show', [StateController::class, 'show']);
    Route::post('/state/store', [StateController::class, 'store']);
    Route::post('/state/update/{id}', [StateController::class, 'update']);
    Route::post('/state/delete/{id}', [StateController::class, 'destroy']);

    //Country
    Route::get('/country', [CountryController::class, 'index']);
    Route::post('/country/show', [CountryController::class, 'show']);
    Route::post('/country/store', [CountryController::class, 'store']);
    Route::post('/country/update/{id}', [CountryController::class, 'update']);
    Route::post('/country/delete/{id}', [CountryController::class, 'destroy']);

    //Degree
    Route::get('/degree', [DegreeController::class, 'index']);
    Route::post('/degree/show', [DegreeController::class, 'show']);
    Route::post('/degree/store', [DegreeController::class, 'store']);
    Route::post('/degree/update/{id}', [DegreeController::class, 'update']);
    Route::post('/degree/delete/{id}', [DegreeController::class, 'destroy']);

    //PIB
    Route::get('/index', [PibFormulaController::class, 'index']);
});


/***********************************************************************************
 * Therapist API Routes
 ***********************************************************************************/
Route::get('therapist/login', [TherapistController::class, "showLogin"]);
Route::post('therapist/login', [TherapistController::class, "login"]);
Route::post('therapist/logout', [TherapistController::class, "logout"]);
/**
 * Therapist Authentication
 */
Route::middleware(["auth:therapist"])->prefix("therapist")->group(function(){
    
    Route::get('/profile', [TherapistController::class, 'getProfile']);
    Route::post('profile/update', [TherapistController::class, 'updateProfile']);
   
    /**
     * Therapist Tickets
     */
    Route::prefix('ticket')->group(function(){
        Route::get('/', [TicketController::class, 'index']);
        Route::get('/show', [TicketController::class, 'show']);
        Route::post('/store', [TicketController::class, 'store']);
        Route::post('/update', [TicketController::class, 'update']);
        Route::post('/assignedupdate', [TicketController::class, 'assignedupdate']);
        Route::post('/ticketstatus', [TicketController::class, 'canclledTicket']);
        Route::post('/delete/{id}', [TicketController::class, 'destroy']);
    });
    
    Route::prefix('appointment')->group(function(){
        Route::get('/', [AppointmentController::class, 'index']);
        Route::get('/show', [AppointmentController::class, 'show']);
        Route::post('/store', [AppointmentController::class, 'store']);
        Route::post('/update/{id}', [AppointmentController::class, 'update']);
        Route::post('ticketstatus', [AppointmentController::class, 'appointmentstatus']);
        Route::post('/delete/{id}', [AppointmentController::class, 'destroy']);
    });
    
});


/***********************************************************************************
 * Patient API Routes
 ***********************************************************************************/
Route::get('patient/login', [PatientController::class, "showLogin"]);
Route::post('patient/login', [PatientController::class, "login"]);
Route::post('patient/logout', [PatientController::class, "logout"]);
/**
 * Patient Authentication
 */
Route::middleware(["auth:patient"])->prefix("patient")->group(function(){
    
    Route::get('/', [PatientController::class, 'index']);
    Route::get('/show', [PatientController::class, 'show']);
    Route::post('/store', [PatientController::class, 'store']);
    Route::post('/update/{id}', [PatientController::class, 'update']);
    Route::post('/delete/{id}', [PatientController::class, 'destroy']);
    
    
    Route::prefix('ticket')->group(function(){
        Route::get('/', [TicketController::class, 'index']);
        Route::get('/show', [TicketController::class, 'show']);
        Route::post('/store', [TicketController::class, 'store']);
        Route::post('/update', [TicketController::class, 'update']);
        Route::post('/assignedupdate', [TicketController::class, 'assignedupdate']);
        Route::post('/ticketstatus', [TicketController::class, 'canclledTicket']);
        Route::post('/delete/{id}', [TicketController::class, 'destroy']);
    });
    
    
    // Appointment
    Route::prefix('appointment')->group(function(){
        Route::get('/', [AppointmentController::class, 'index']);
        Route::get('/show', [AppointmentController::class, 'show']);
        Route::post('/store', [AppointmentController::class, 'store']);
        Route::post('/update/{id}', [AppointmentController::class, 'update']);
        Route::post('ticketstatus', [AppointmentController::class, 'appointmentstatus']);
        Route::post('/delete/{id}', [AppointmentController::class, 'destroy']);
    });
});


