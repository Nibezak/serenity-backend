<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TypeAppointment;
use App\Models\Appointment;
use App\Models\Role;
use App\Models\Patient;
use App\Models\Diagnosis;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Validator;
use Illuminate\Support\Str;
use App\TransferSms;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Create a new AdminController instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth:api', ['except' => ['login', 'register']]);
    // }
    public function __construct()
    {
        $this->middleware('jwt.verify');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Add hospital to a manager .
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createNewUser(Request $request)
    {
        if (Auth::user()->roles->first()->name == 'Admin' ) {

        //validate inputs
        $validator = Validator::make($request->all(), [
            'FirstName' => 'required',
            'LastName' => 'required',
            'email' => 'required|unique:users',
            'telephone' => 'required',
            'Address' => 'required',
            'RoleId' => 'required',
            'Title' => 'required',
        ]);
        if ($validator->fails()) {
            // return response()->json($validator->errors()->toJson(), 400);

            return response()->json(
                ['errors' => implode($validator->errors()->all())],
                422
            );
        }

        $defaultManagerPswd = Str::random(10);

        $role = Role::find($request['RoleId']);

        if ($role) {
            $user = new User();
            $user->Role_id = $request['RoleId'];
            $user->FirstName = $request['FirstName'];
            $user->LastName = $request['LastName'];
            $user->Email = $request['email'];
            $user->Telephone = $request['telephone'];
            $user->gender = $request['gender'];
            $user->ProfileImageUrl = 'https://i.imgur.com/BKB2EQi.png';
            $user->Address = null;
            $user->LicenseNumber = null;
            $user->Title = $request['Title'];
            $user->Hospital_Id = auth()->user()->Hospital_Id;
            $user->password = bcrypt($defaultManagerPswd);
            $user->LastLoginDate = date('Y-m-d H:i:s');
            $user->JoinDate = date('Y-m-d H:i:s');
            $user->IsActive = '1';
            $user->IsNotLocked = '1';
            $user->IsAccountNonExpired = '1';
            $user->IsAccountNonLocked = '1';
            $user->session='1';
            $user->IsCredentialsNonExpired = '1';
            $user->save();

            $user->attachRole($role);
        }

        $hospitalname = Hospital::select('PracticeName')
            ->where('id', '=', auth()->user()->id)
            ->value('PracticeName');

        $message =
            'Hello ' .
            $request['FirstName'] .
            ' - ' .
            $hospitalname .
            '\'s Account credentials are email ' .
            $request['email'] .
            ' and Password is ' .
            $defaultManagerPswd;

        $sms = new TransferSms();
        $sms->sendSMS($request['telephone'],$message);

        return response()->json(
            [
                'message' =>
                    $request['FirstName'] .
                    ' ' .
                    $request['LastName'] .
                    ' Account is successfully created ,Check your email address or Phone number for the Login credentials',
                // 'user' => $user
                'test' =>
                    $message .
                    ' Your Role is ' .
                    Role::select('display_name')
                        ->where('id', '=', $request['RoleId'])
                        ->value('display_name'),
            ],
            200
        );

    }
    return response()->json(['message' => 'Unauthorized user'], 401);


    }


    //Get all our hospital staff
    public function fetchourstaff()
    {
        if (Auth::user()->roles->first()->name == 'Admin') {
            return response()->json(
                [
                    'message' => 'Unauthorized User',
                ],
                401
            );
        }

        return response()->json(
            [
                'data' => User::
                orderBy('created_at', 'desc')
                ->join('roles', 'users.Role_id', '=', 'roles.id')
                    ->where(
                        'users.Hospital_Id',
                        '=',
                        auth()->user()->Hospital_Id
                    )

                    ->get([
                        'users.created_at',
                        'users.FirstName',
                        'users.LastName',
                        'users.telephone',
                        'users.email',
                        'roles.display_name',
                    ]),
            ],
            200
        );



    }

    //Fetch hospitall staff roles
    public function retrieveRoles()
    {
        if (Auth::check()) {
            // $myRoleId = json_decode(Auth::user()->roles->first()['Clinician'], true);

            return response()->json(
                [
                    'data' => Role::select('id', 'display_name')
                        ->get()
                        ->except('110'),
                ],
                200
            );
        }
        return response()->json(['message' => 'Unauthorized user'], 401);
    }

    //register new patient
    public function createnewpatient(Request $request)
    {
        if (Auth::check()) {
            //Validate User Inputs
            $validator = Validator::make($request->all(), [
                'FirstName' => 'required',
                'LastName' => 'required',
                'Date_of_Birth' => 'required',
                'Province' => 'required',
                'District' => 'required',
                'Sector' => 'required',
                'Cell' => 'required',
                'Village' => 'required|string|',
                'MobilePhone' => 'required|between:9,14',
                'Email' => 'required|string|unique:patients',
                'Marital_Status' => 'required',
                'Employment' => 'required',
                'Languages' => 'required',
                'gender'=>'required',
            ]);
            if ($validator->fails()) {
                // return response()->json($validator->errors()->toJson(), 400);

                return response()->json(
                    ['errors' => implode($validator->errors()->all())],
                    422
                );
            }

            $patient = new Patient();
            $patient->FirstName = $request['FirstName'];
            $patient->LastName = $request['LastName'];
            $patient->Dob = $request['Date_of_Birth'];
            $patient->Province = $request['Province'];
            $patient->District = $request['District'];
            $patient->Sector = $request['Sector'];
            $patient->Cell = $request['Cell'];
            $patient->Village = $request['Village'];
            $patient->MobilePhone = $request['MobilePhone'];
            $patient->Email = $request['Email'];
            $patient->MartialStatus = $request['Marital_Status'];
            $patient->Hospital_Id = auth()->user()->Hospital_Id;
            $patient->Languages=$request['Languages'];
            $patient->Employment=$request['Employment'];
            $patient->profileimageUrl='https://i.imgur.com/BKB2EQi.png';
            $patient->PatientCode='P'.strtoupper(Str::random(6));
            $patient->gender=$request['gender'];
            $patient->Createdby_Id = auth()->user()->id;
           $patient->save();




            return response()->json(
                [
                    'message' =>
                        'Successfully created new Patient called ' .
                        $request['FirstName'] .
                        ' ' .
                        $request['LastName'],
                ],
                200
            );
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function fetchourActivepatients()
    {

        $var =Auth::user()->roles->first()->name;
        if ( $var == 'Admin' || $var =='Reception') {


        return response()->json(
                [
                    'data' => Patient::
                        where('AssignedDoctor_Id','=',null)
                        ->with(['doctor:id,Title,FirstName,LastName,telephone','LastAppointment','NextAppointment','doneby:id,FirstName,LastName,email,telephone,ProfileImageUrl'])

                        ->where('Hospital_Id', '=', auth()->user()->Hospital_Id)
                        ->orderBy('created_at', 'desc')
                        ->get()
                        ,
                ],
                200
            );





        }

        else if (Auth::user()->roles->first()->name == 'Clinician') {


            return response()->json(
                [
                    'data' => Patient::
                    where('Hospital_Id', '=', auth()->user()->Hospital_Id)
                    ->where('AssignedDoctor_Id','=',auth()->user()->id)
                    ->where('Status','=','Active')
                    ->with(['doctor:id,Title,FirstName,LastName,telephone','LastAppointment','NextAppointment','doneby:id,FirstName,LastName,email,telephone,ProfileImageUrl'])
                    ->orderBy('created_at', 'desc')
                    ->get()
                        ,
                ],
                200
            );


        }
        return response()->json(['message' => 'Unauthorized user'], 401);
    }

    public function fetchonepatient($id)
    {
        if (Auth::check()) {
            //Validate User Inputs
            $user = Patient::where('id', '=', $id)
              ->where('Hospital_Id','=',auth()->user()->Hospital_Id)
            ->first();
            if ($user === null) {
                // user doesn't exist
                return response()->json(
                    ['message' => 'This patient does not exists'],
                    404
                );
            }

            return response()->json(
                [
                    'data' => Patient::
                        where('id', '=', $id)
                        ->with(['doctor:id,Title,FirstName,LastName,telephone','LastAppointment','NextAppointment','doneby:id,FirstName,LastName,email,telephone,ProfileImageUrl'])
                        ->where('Hospital_Id', '=', auth()->user()->Hospital_Id)
                        ->get(),
                ],
                200
            );
        }
        return response()->json(['message' => 'Unauthorized user '], 401);


    }


    public function viewourhospitaldoctor()
    {
        if (Auth::check()) {
            return response()->json(
                [
                    'data' => User::
                    orderBy('created_at', 'desc')
                    ->join(
                        'roles',
                        'users.Role_id',
                        '=',
                        'roles.id'
                    )
                        ->where(
                            'users.Hospital_Id',
                            '=',
                            auth()->user()->Hospital_Id
                        )
                        ->where('roles.name', '=', 'Clinician')
                        ->get(['users.*', 'roles.name']),
                ],
                200
            );
        }
        return response()->json(['message' => 'Unauthorized User'], 401);
    }

    public function assigndocotortopatient(Request $request)
    {
        if (Auth::user()->roles->first()->name == ('Admin'||('Reception')||('Cashier'))) {
            //Validate User Inputs
            $validator = Validator::make($request->all(), [
                'Doctor_Id' => 'required',
                'PatientId' => 'required',
            ]);
            if ($validator->fails()) {
                // return response()->json($validator->errors()->toJson(), 400);

                return response()->json(
                    ['errors' => implode($validator->errors()->all())],
                    422
                );
            }

            if (
                Patient::where('id', '=', $request['PatientId'])->first()
                    ->Status == 'Active'
            ) {
                $check = Patient::where(
                    'id',
                    '=',
                    $request['PatientId']
                )->first();

                if (!is_null($check)) {
                    $result = DB::Table('patients')
                        ->where('id', '=', $request['PatientId'])
                        ->where('Hospital_Id', '=', auth()->user()->Hospital_Id)
                        ->update([
                            'AssignedDoctor_Id' => $request['Doctor_Id'],
                        ]);
                    return $result = [
                        'message' =>
                            'Patient is assigned to Doctor successfully !! ',
                        'success' => true,
                    ];
                } else {
                    return $result = [
                        'message' => 'Patient Not Found !! ',
                    ];
                }
            }
            return response()->json(
                ['message' => 'Patient is Inactive,Please Pay or consult Letsreason Admin for consultation or other support '],
                201
            );
        }
        return response()->json(['message' => 'Unauthorized user'], 401);
    }

    public function activatepatient(Request $request)
    {
        if (Auth::user()->roles->first()->name == ('Admin'||('Reception')||('Cashier'))) {
            //Validate User Inputs
            $validator = Validator::make($request->all(), [
                'PatientId' => 'required',
                'Status' => 'required',
            ]);
            if ($validator->fails()) {
                // return response()->json($validator->errors()->toJson(), 400);

                return response()->json(
                    ['errors' => implode($validator->errors()->all())],
                    422
                );
            }

            $check = Patient::where('id', '=', $request['PatientId'])->first();

            if (!is_null($check)) {
                $result = DB::Table('patients')
                    ->where('id', '=', $request['PatientId'])
                    ->where('Hospital_Id', '=', auth()->user()->Hospital_Id)
                    ->update([
                        'Status' => $request['Status'],
                    ]);
                return $result = [
                    'message' =>
                        'Patient Account is Activate successfully - Now ' .
                        $request['Status'] .
                        ' !!',
                    'success' => true,
                ];
            } else {
                return $result = [
                    'message' => 'Patient Not Found !! ',
                ];
            }
        }
        return response()->json(['message' => 'Unauthorized user'], 401);
    }

    public function addhospitalservice(Request $request)
    {
        if (Auth::user()->roles->first()->name == 'Admin') {
            //Validate User Inputs
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:typeappointments',
            ]);
            if ($validator->fails()) {
                // return response()->json($validator->errors(), 422);

                return response()->json(
                    ['errors' => implode($validator->errors()->all())],
                    422
                );
            }

            $service = new TypeAppointment();

            $service->name = $request['name'];
            $service->createdBy_Id = auth()->user()->id;
            $service->hospital_Id = auth()->user()->Hospital_Id;
            $service->save();

            return response()->json(
                ['message' => 'Created a new Appointment type !!'],
                201
            );
        }
        return response()->json(['message' => 'Unauthorized user'], 401);
    }

    public function viewhospitalservice()
    {
        if (Auth::check()) {

            return response()->json(['data' =>
            TypeAppointment::
            // where(
            //     'hospital_Id',
            //     '=',
            //     auth()->user()->Hospital_Id
            // )
                with(['creator:id,FirstName,LastName'])
                ->get()

            ], 200);

        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function createappointment(Request $request)
    {
        if (Auth::check()) {
            //Validate User Inputs
            $validator = Validator::make($request->all(), [
                'AppointmentType_Id' =>'required',
                'Patient_Id' => 'required',
                'Location' => 'required',
                'ScheduledTime' => 'required',
                'Duration' => 'required',
                'Frequency' => 'required',
                'AppointmentAlert' => 'required',
            ]);
            if ($validator->fails()) {
                // return response()->json($validator->errors(), 422);

                return response()->json(
                    ['errors' => implode($validator->errors()->all())],
                    422
                );
            }
            $recordAppoint = TypeAppointment::
            where('id','=',$request['AppointmentType_Id'])
            ->where('Hospital_Id','=',auth()->user()->Hospital_Id);

            if (!$recordAppoint->exists()) {
                return response()->json(
                    ['errors' =>
                    'This Appointment type does not exists in our hospital'
                    ],
                    404
                );
            }
            $recordpat = Patient::
            where('id','=',$request['Patient_Id'])
            ->where('Hospital_Id','=',auth()->user()->Hospital_Id);

            if (!$recordpat->exists()) {
                return response()->json(
                    ['message' =>
                    'This Patient does not exists in our hospital'
                    ],
                    404
                );
            }

            $patData=Patient::select('FirstName','LastName','MobilePhone','email')
            ->where('id','=',$request['Patient_Id'])
            ->get();


          $AssignedDoctorId=Patient::select('AssignedDoctor_Id')->where('id','=',$request['Patient_Id'])->value('AssignedDoctor_Id');


   if($AssignedDoctorId ==null){
    return response()->json(['message' => 'Sorry Patient '. $patData[0]->FirstName.' '.$patData[0]->LastName.' does not have assigned doctor, please first assign the patient with the doctor first'], 404);
   }


            $recorddoct = User::
            where('id','=',$AssignedDoctorId)
            ->where('Hospital_Id','=',auth()->user()->Hospital_Id);

            if (!$recorddoct->exists()) {
                return response()->json(
                    ['message' =>
                    'This Doctor does not exists in our hospital'
                    ],
                    404
                );
            }




            $typeApp=TypeAppointment::select('name')
            ->where('Hospital_Id','=',auth()->user()->Hospital_Id)
            ->where('id','=',$request['AppointmentType_Id'])
            ->get();

            $doctorData=User::select('FirstName','LastName','Title','Hospital_Id')
            ->where('id','=',$AssignedDoctorId)
            ->get();

            $hospitalName=Hospital::select('PracticeName','District','Sector','Cell','Village')
            ->where('id','=',$doctorData[0]->Hospital_Id)
            ->get();




            $appointment = new Appointment();
            $appointment->AppointmentType_Id = $request['AppointmentType_Id'];
            $appointment->Patient_Id = $request['Patient_Id'];
            $appointment->Doctor_Id =$AssignedDoctorId;
            $appointment->Location = $request['Location'];
            $appointment->ScheduledTime = $request['ScheduledTime'];
            $appointment->Duration = $request['Duration'];
            $appointment->Frequency = $request['Frequency'];
            $appointment->CreatedBy_Id = auth()->user()->id;
            $appointment->Status = 'Active';
            $appointment->AppointmentAlert = $request['AppointmentAlert'];
            $appointment->Hospital_Id = auth()->user()->Hospital_Id;


            $sms = new TransferSms();
           if($request['Location'] == 'online'){
           $link='https://meet.jit.si/Letsreason-test';
            $appointment->link=$link;



            $message='Hello '.$patData[0]->FirstName.' '.$patData[0]->LastName.' Your '.$typeApp[0]->name. ' Appointment at  '.$hospitalName[0]->PracticeName.' Located at '.$hospitalName[0]->District.' ,'.$hospitalName[0]->Sector.','.$hospitalName[0]->Cell.' with '.$doctorData[0]->Title.' '.$doctorData[0]->FirstName.' '.$doctorData[0]->LastName.' has been scheduled successfully , Date: '
            .$request['ScheduledTime'].' Location: '.$request['Location']. ' and Video Link is:  '.$link;


            $sms->sendSMS($patData[0]->MobilePhone,$message);


          }else{
          $msg='Hello '.$patData[0]->FirstName.' '.$patData[0]->LastName.' Your '.$typeApp[0]->name. ' Appointment at  '.$hospitalName[0]->PracticeName.' Located at '.$hospitalName[0]->District.' ,'.$hospitalName[0]->Sector.','.$hospitalName[0]->Cell.' with '.$doctorData[0]->Title.' '.$doctorData[0]->FirstName.' '.$doctorData[0]->LastName.' has been scheduled successfully , Date: '
          .$request['ScheduledTime'].' Venue: '.$request['Location'];

             $sms->sendSMS($patData[0]->MobilePhone,$msg);

             $appointment->link='null';}

           $appointment->save();

            $PatientnextAppointment=Appointment::select('id','ScheduledTime')
            ->where('Hospital_Id','=',auth()->user()->Hospital_Id)
            ->where('Patient_Id','=',$request['Patient_Id'])
           ->whereDate('ScheduledTime', '>', Carbon::now())
            ->orderBy('ScheduledTime', 'ASC')
            ->first();
           ;

           $PatientlastAppointment=Appointment::select('id','ScheduledTime')
           ->where('Hospital_Id','=',auth()->user()->Hospital_Id)
           ->where('Patient_Id','=',$request['Patient_Id'])
          ->whereDate('ScheduledTime', '<', Carbon::now())
           ->orderBy('ScheduledTime', 'DESC')
           ->first();
          ;

          if($PatientlastAppointment ==null){
            DB::Table('patients')
             ->where('Hospital_Id','=',auth()->user()->Hospital_Id)
             ->where('id','=',$request['Patient_Id'])
             ->update([
                 'lastappoint'=>$appointment->id,
             ]);
        }



             DB::Table('patients')
           ->where('Hospital_Id','=',auth()->user()->Hospital_Id)
           ->where('id','=',$request['Patient_Id'])
                    ->update([
                        'nextappoint'=>$PatientnextAppointment->id,
                    ]);



            if($appointment){
            return response()->json(
                ['message' =>
                $typeApp[0]->name.' Appointment of '.$patData[0]->FirstName.' '
                .$patData[0]->LastName.' has been created Successfully '
                ],
                201
            );} return response()->json(['message' => 'Ooops Something went wrong on our side, we are fixing it ASAP'], 401);



        }
        return response()->json(['message' => 'Unauthorized user'], 401);
    }

    public function viewallappointments(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(
                [
                    'message' => 'Unauthorized User',
                ],
                200
            );
        }

        return response()->json(['data' =>

        Appointment::
         where('Hospital_Id','=',Auth::user()->Hospital_Id)
        ->with(['patient:id,email,FirstName,LastName,profileimageUrl,MobilePhone,PatientCode','appointmenttype:id,name'])
        ->orderBy('ScheduledTime', 'asc')
        ->get(),


        ], 200);
    }


public function getappointmentbyid($appointmentId){
    if (Auth::check()) {

        $user = Appointment::where('id', '=', $appointmentId)->first();
        if ($user === null) {
            // Appointment doesn't exist
            return response()->json(
                ['message' => 'This Appointment does not exists'],
                201
            );
        }

        return response()->json(['data' =>

        Appointment::
        orderBy('ScheduledTime', 'asc')
        ->where('Hospital_Id','=',Auth::user()->Hospital_Id)
        ->where('id','=',$appointmentId)
        ->with(['doctor:id,email,telephone,Title,FirstName,LastName','patient','appointmenttype:id,name'])
        ->get(),


        ], 201);




}
return response()->json(['message' => 'Unauthorized User '], 401);

}


public function getonepatientappointments($patientId){

    if (Auth::check()) {

        $patApp = Appointment::where('Patient_Id', '=', $patientId)->first();
        $pat=Patient::where('id', '=', $patientId)->first();
        if ($pat === null) {
            // Patient checks
            return response()->json(
                ['message' => 'This Patient does not exists our system'],
                201
            );
        }else if($patApp === null){
            return response()->json(
                ['message' => 'This Patient does not have any appointment'],
                201
            );
        }

        return response()->json(['data' =>

        Appointment::
        orderBy('ScheduledTime', 'asc')
        ->where('Hospital_Id','=',Auth::user()->Hospital_Id)
        ->where('Patient_Id','=',$patientId)
        ->get(),


        ], 200);



    }
    return response()->json(['message' => 'Unauthorized user'], 201);



}

    public function creatediagnosis(Request $request)
    {
        if (Auth::user()->roles->first()->name == 'Admin') {
            //Validate User Inputs
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:diagnosis',
            ]);
            if ($validator->fails()) {
                // return response()->json($validator->errors()->toJson(), 400);

                return response()->json(
                    ['errors' => implode($validator->errors()->all())],
                    422
                );
            }

            $diagnsosis = new Diagnosis();
            $diagnsosis->name = $request['name'];
            $diagnsosis->code = 'F' . trim(strtoupper(Str::random(6)));
            $diagnsosis->Hospital_Id = auth()->user()->Hospital_Id;
            $diagnsosis->CreatedBy_Id = auth()->user()->id;
            $diagnsosis->Status = 'Active';
            $diagnsosis->save();

            return response()->json(
                ['message' => 'Successfully Created new Diagnsosis '],
                200
            );
        }
        return response()->json(['errors' => 'Unauthorized user'], 401);

    }

    public function fetchdiagnosis()
    {
        $var =Auth::user()->roles->first()->name;
        if ( $var == 'Admin' || $var =='Clinician') {
            return response()->json(
                [
                    'data' => Diagnosis::
                    // where(
                    //     'Hospital_Id',
                    //     '=',
                    //     auth()->user()->Hospital_Id
                    // )
                        with(['createdby:id,FirstName,LastName'])
                        ->get(),
                ],
                200
            );
        }
        return response()->json(['message' => 'Unauthorized user'], 401);
    }

    public function fetchonedoctor($id){

        if (Auth::check()) {
            //Validate User Inputs
            $user = User::where('id', '=', $id)->first();
            if ($user === null) {
                // user doesn't exist
                return response()->json(
                    ['message' => 'This Doctor does not exists'],
                    404
                );
            }

            return response()->json(
                [
                    'data' => User::select(
                        'id',
                        'FirstName',
                        'LastName',
                        'telePhone',
                        'email',
                        'Title',
                        'Hospital_Id',

                    )
                        ->with('hospital:id,PracticeName,TypeOrganization,BusinessPhone,BusinessEmail,TypeOrganization')
                        ->where('id', '=', $id)
                        ->where('Hospital_Id', '=', auth()->user()->Hospital_Id)
                        ->get(),
                ],
                200
            );
        }
        return response()->json(['message' => 'Unauthorized user'], 401);


    }



}
