<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Super\SuperDashboardController;
use App\Http\Controllers\Note\NoteController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\Mobile\MAuthController;



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



//Auth Routes here is where you can login,logout,refresh token,user profile and validate OTP Api Routes for your application.

Route::group(
    [
        'middleware' => 'api',
        'prefix' => 'V1',
    ],
    function ($router) {

        Route::get('/test-email', [AuthController::class, 'checkmail']);


        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/user-profile', [AuthController::class, 'userProfile']);
        Route::post('/validate-OTP', [AuthController::class, 'validateotp']);
        Route::post('/send-OTP', [AuthController::class, 'sendotp']);

        Route::post('/forgot-password', [
            AuthController::class,
            'getPasswordToken',
        ]);
        Route::post('/reset-password/{token}', [
            AuthController::class,
            'updatePassword',
        ]);
        Route::post('/change-password', [
            AuthController::class,
            'changepassword',
        ]);
    }
);






//Routes for Hospital Actvities
Route::group(
    [
        'middleware' => 'api',
        'prefix' => 'staff',
    ],
    function ($router) {
            Route::get('/show/roles', [AdminController::class, 'retrieveRoles']);
        Route::post('/new-user', [AdminController::class, 'createNewUser']);
        Route::get('/our-staff', [AdminController::class, 'fetchourstaff']);

        Route::post('/patient/register', [
            AdminController::class,
            'createnewpatient',
        ]);


        Route::post('/roles/get-hospital-roles', [
            AdminController::class,
            'savehospitalroles',
        ]);



        Route::post('/patient/edit-profile/{PatientId}', [
            AdminController::class,
            'editpatientprofile',
        ]);

        Route::get('/patient/our-patient', [
            AdminController::class,
            'fetchourActivepatients',
        ]);

        Route::get('/Admin/all-patient', [
            AdminController::class,
            'fetchourAllpatients',
        ]);

        Route::get('/patient/view/{id}', [
            AdminController::class,
            'fetchonepatient',
        ]);


        Route::get('/doctor/view/{id}', [
            AdminController::class,
            'fetchonedoctor',
        ]);

        Route::post('/patient/Assign-Doctor', [
            AdminController::class,
            'assigndocotortopatient',
        ]);
        Route::post('/patient/Activate-Patient-Account', [
            AdminController::class,
            'activatepatient',
        ]);

        Route::get('/Hospital/View/our-doctor', [
            AdminController::class,
            'viewourhospitaldoctor',
        ]);

        Route::post('/Hospital/Add-Hospital-Service', [
            AdminController::class,
            'addhospitalservice',
        ]);
        Route::get('/Hospital/View/Hospital-Service', [
            AdminController::class,
            'viewhospitalservice',
        ]);

        Route::post('/Hospital/create-appointment', [
            AdminController::class,
            'createappointment',
        ]);
        Route::get('/view/all-appointment', [
            AdminController::class,
            'viewallappointments',
        ]);

        Route::post('/Hospital/view/doctor-appointment', [
            AdminController::class,
            'viewmyappointments',
        ]);

        Route::get('/Hospital/view/appointment-byid/{appointmentId}', [
            AdminController::class,
            'getappointmentbyid',
        ]);

        Route::get('/Hospital/patient/one-appointment/{patientId}', [
            AdminController::class,
            'getonepatientappointments',
        ]);

        Route::post('/create-diagnosis', [
            AdminController::class,
            'creatediagnosis',
        ]);
        Route::get('/view-diagnosis', [
            AdminController::class,
            'fetchdiagnosis',
        ]);


        Route::post('/Hospital/register-department', [
            AdminController::class,
            'savedepartment',
        ]);


        Route::get('/Hospital/view-department', [
            AdminController::class,
            'fetchdepartment',
        ]);

        Route::post('/Hospital/register-insurance', [
            AdminController::class,
            'saveinsurance',
        ]);

        Route::get('/Hospital/fetch-insurance', [
            AdminController::class,
            'fetchhospitalinsurance',
        ]);


        Route::get('/patient/fetch-sessions/{PatientId}', [
            AdminController::class,
            'fetchpatientactivesession',
        ]);


        Route::get('/doctor/fetch-sessions/{PatientId}', [
            AdminController::class,
            'fetchdoctoractivesession',
        ]);

        Route::get('/doctor/fetch-session-by-id/{sessionId}', [
            AdminController::class,
            'fetchonesessionbyid',
        ]);


        Route::get('/patient/view-insurance/{PatientId}', [
            AdminController::class,
            'fetchinsurancepatient',
        ]);




        Route::post('/patient/end-session/{drId}', [
            AdminController::class,
            'endpatientsession',
        ]);
        Route::get('/patient/session/Followup/{drId}', [
            AdminController::class,
            'fetchallfollowupsession',
        ]);


        Route::get('/archive/sessions', [
            AdminController::class,
            'fetcharchivesessions',
        ]);


    }
);

//Routes for hospital Notes


Route::group(
    [
        'middleware' => 'api',
        'prefix' => 'Note',
    ],
    function ($router) {

        Route::post('/get-note-details-by-id', [
            NoteController::class,
            'fetchnotedetailsbyid',
        ]);


        Route::post('/Manager/create-treatment-strategy', [
            NoteController::class,
            'createtreatmentstrategy',
        ]);
        Route::get('/Manager/view-treatment-strategy', [
            NoteController::class,
            'fetchreatmentstrategy',
        ]);

        Route::post('/Manager/create-frequency-treatment', [
            NoteController::class,
            'createfrequencytreatment',
        ]);
        Route::get('/Manager/view-frequency-treatment', [
            NoteController::class,
            'fetchfrequencytreatment',
        ]);

        Route::post('/Manager/create-note-psychotherapy-treatment-plan', [
            NoteController::class,
            'addptreatmentplan',
        ]);

        Route::post('/Manager/create-miscellaneous-Note', [
            NoteController::class,
            'createmiscellaneousnote',
        ]);
         Route::post('/Manager/view-miscellaneous-Note', [NoteController::class, 'fetchmiscnote']);



         Route::post('/Manager/create-Contact-Note', [NoteController::class, 'createContactNote']);

         Route::post('/Manager/view-patient-Contact-Note', [NoteController::class, 'viewContactNote']);

         Route::post('/Manager/create-Process-Note', [NoteController::class, 'createProcessNote']);


         Route::post('/Manager/view-Process-Note', [NoteController::class, 'viewProcessNote']);



         Route::post('/Manager/create-Consulation-Note', [NoteController::class, 'createConsulationnote']);


         Route::post('/Manager/create-Intake-Note', [NoteController::class, 'saveintakenote']);

         Route::post('/Manager/create-progress-note', [NoteController::class, 'saveprogressnote']);

         Route::post('/Manager/create-missed-apointment-note', [NoteController::class, 'savemissedappointmentnote']);

         Route::post('/Manager/create-termination-note', [NoteController::class, 'saveterminationnote']);


         Route::post('/Manager/get-all-notes', [NoteController::class, 'getallnotes']);

         Route::get('/Patient/Fetch-treatmentplan-notes/{PatientID}', [
            NoteController::class,
            'getalltreatmentplannote',
        ]);


        Route::get('/Patient/Fetch-intake-notes/{key}', [
            NoteController::class,
            'getallintakenotes',
        ]);




    }
);

//Routes for Super Admin Letsreason
Route::group(
    [
        'middleware' => 'api',
        'prefix' => 'Admin',
    ],
    function ($router) {
        Route::get('/show/types-of-organization', [
            SuperDashboardController::class,
            'gettypesOrg',
        ]);
    }
);



Route::group(
    [
        'middleware' => 'api',
        'prefix' => 'prescription',
    ],
    function ($router) {

         Route::post('/patient/create', [PrescriptionController::class, 'saveprescription']);
         Route::get('/drug/all', [PrescriptionController::class, 'getalldrugs']);

         Route::get('/patient/all/{PatientId}', [PrescriptionController::class, 'getallpatientprescription']);


    }
);



Route::group(
    [
        'middleware' => 'api',
        'prefix' => 'billing',
    ],
    function ($router) {

         Route::get('/patient/all/{PatientId}', [BillingController::class, 'getallpatientbilling']);


    }
);




Route::group(
    [
        'middleware' => 'api',
        'prefix' => 'doctor',
    ],
    function ($router) {

         Route::post('/slot/set-availability', [DoctorController::class, 'setclinicianavailability']);
         Route::get('/slot/get-availability/{Doctor_Id}', [DoctorController::class, 'getclinicianslotavailability']);


    }
);









// App endpoints here


Route::group(
    [
        'middleware' => 'api',
        'prefix' => 'app',
    ],
    function ($router) {

         Route::post('/auth/login', [AuthController::class, 'login']);
         Route::get('/auth/logout', [AuthController::class, 'logout']);

         Route::post('/patient/register', [MAuthController::class, 'savepatient']);


    }
);
