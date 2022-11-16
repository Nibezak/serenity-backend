<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\TypeAppointment;
use App\Models\User;
use App\Models\Patient;
use App\Models\Hospital;

class BillingController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('jwt.verify');
    }

    public function getallpatientbilling($PatientId){

        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized user'], 401);
        }

        if(!Session::where('Patient_Id','=',$PatientId)->exists()){
            return response()->json(
                ['message' => 'This patient does have pending billing information'],
                200
            );
        }


    $patientsession =Session::where('Status','=','Completed')->where('Patient_Id','=',$PatientId)->get()->makeHidden(['Status','updated_at']);

    return [

        'Patient' =>Patient::find($patientsession[0]['Patient_Id']),
        'Doctor' =>User::find($patientsession[0]['Doctor_Id']),
        'Hospital' => Hospital::find($patientsession[0]['Hospital_Id']),
        'total' =>true,
        'Issue_on'=>($patientsession[0]['created_at'])->diffForHumans(),
        'Prepared_By'=>User::find(auth()->user()->id),
        'data'=>collect($patientsession)
        ->map(function ($item) {
            return  TypeAppointment::find($item['Service_Id'])

            ;
        })
       ,

    ];

    }

}
