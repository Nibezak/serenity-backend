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
        //

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
        //  $sms->sendSMS($request['telephone'],$message);

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

    //Get all our hospital staff
    public function fetchourstaff()
    {
        if (!Auth::user()->roles->first()->name == 'Admin') {
            return response()->json(
                [
                    'message' => 'Unauthorized User',
                ],
                200
            );
        }

        return response()->json(
            [
                'data' => User::join('roles', 'users.Role_id', '=', 'roles.id')
                    ->where(
                        'users.Hospital_Id',
                        '=',
                        auth()->user()->Hospital_Id
                    )

                    ->get([
                        'users.FirstName',
                        'users.LastName',
                        'users.telephone',
                        'users.email',
                        'roles.display_name',
                    ]),
            ],
            200
        );

        return 'okay';
    }

    //Fetch hospitall staff roles
    public function retrieveRoles()
    {
        if (Auth::user()->roles->first()->name == 'Admin') {
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
            $patient->PatientCode='P'.strtoupper(Str::random(5));
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
        if (Auth::check()) {
            return response()->json(
                [
                    'data' => Patient::
                    orderBy('created_at', 'desc')
                     ->select(
                        'id',
                        'FirstName',
                        'LastName',
                        'MobilePhone',
                        'email',
                        'Dob',
                        'AssignedDoctor_Id',
                        'gender',
                        'profileimageurl'
                    )
                        ->with(['doctor:id,Title,FirstName,LastName,telephone,email'])

                        ->where('Hospital_Id', '=', auth()->user()->Hospital_Id)
                        ->get()
                        ,
                ],
                200
            );
        }
        return response()->json(['errors' => 'Unauthorized'], 401);
    }

    public function fetchonepatient($id)
    {
        if (Auth::check()) {
            //Validate User Inputs
            $user = Patient::where('id', '=', $id)->first();
            if ($user === null) {
                // user doesn't exist
                return response()->json(
                    ['message' => 'This patient does not exists'],
                    201
                );
            }

            return response()->json(
                [
                    'data' => Patient::select(
                        'id',
                        'FirstName',
                        'LastName',
                        'MobilePhone',
                        'email',
                        'Dob',
                        'AssignedDoctor_Id',
                        'gender',
                        'profileimageurl',
                    )
                        ->with(['doctor:id,Title,FirstName,LastName,telephone'])
                        ->where('id', '=', $id)
                        ->where('Hospital_Id', '=', auth()->user()->Hospital_Id)
                        ->get(),
                ],
                200
            );
        }
        return response()->json(['message' => 'Unauthorized'], 401);


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
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function assigndocotortopatient(Request $request)
    {
        if (Auth::check()) {
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
                        'msg' =>
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
                ['message' => 'Patient is Inactive,Please Pay  '],
                401
            );
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function activatepatient(Request $request)
    {
        if (Auth::check()) {
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
                    'msg' =>
                        'Patient Status is updated successfully - Now ' .
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
    }

    public function addhospitalservice(Request $request)
    {
        if (Auth::check()) {
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
                200
            );
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function viewhospitalservice()
    {
        if (Auth::check()) {

            return response()->json(['data' =>
            TypeAppointment::where(
                'hospital_Id',
                '=',
                auth()->user()->Hospital_Id
            )
                ->with(['creator:id,FirstName,LastName', 'hospital'])
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


                'AppointmentType_Id' => 'required',
                'Patient_Id' => 'required',
                'Doctor_Id' => 'required',
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
            $patData=Patient::select('FirstName','LastName','MobilePhone','email')
            ->where('id','=',$request['Patient_Id'])
            ->first();

            $typeApp=TypeAppointment::select('name')
            ->where('Hospital_Id','=',auth()->user()->Hospital_Id)
            ->first();

            $doctorData=User::select('FirstName','LastName','Title','Hospital_Id')
            ->where('id','=',$request['Patient_Id'])
            ->first();

            $hospitalName=Hospital::select('PracticeName','District','Sector','Cell','Village')
            ->where('id','=',$doctorData->Hospital_Id)
            ->first();




            $appointment = new Appointment();
            $appointment->AppointmentType_Id = $request['AppointmentType_Id'];
            $appointment->Patient_Id = $request['Patient_Id'];
            $appointment->Doctor_Id = $request['Doctor_Id'];
            $appointment->Location = $request['Location'];
            $appointment->ScheduledTime = $request['ScheduledTime'];
            $appointment->Duration = $request['Duration'];
            $appointment->Frequency = $request['Frequency'];
            $appointment->CreatedBy_Id = auth()->user()->id;
            $appointment->Status = 'Active';
            $appointment->AppointmentAlert = $request['AppointmentAlert'];
            $appointment->Hospital_Id = auth()->user()->Hospital_Id;

           if($request['Location'] == 'online'){
           $link='https://meet.jit.si/Letsreason-test';
            $appointment->link=$link;


            $message='Hello '.$patData->FirstName.' '.$patData->LastName.' Your '.$typeApp->name. ' Appointment at  '.$hospitalName->PracticeName.' Located at '.$hospitalName->District.' ,'.$hospitalName->Sector.','.$hospitalName->Cell.' with '.$doctorData->Title.' '.$doctorData->FirstName.' '.$doctorData->LastName.' has been scheduled successfully , Date: '
            .$request['ScheduledTime'].' Location: '.$request['Location']. ' and Video Link is:  '.$link;
            $sms = new TransferSms();
            $sms->sendSMS($patData->MobilePhone,$message);


          }else{
           $appointment->link='null';}
            $appointment->save();



            return response()->json(
                ['message' =>
                $typeApp->name.' Appointment of '.$patData->FirstName.' '
                .$patData->LastName.' has been created Successfully '
                ],
                200
            );

        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function viewallappointments(Request $request)
    {
        if (!Auth::user()->roles->first()->name == 'Admin') {
            return response()->json(
                [
                    'message' => 'Unauthorized User',
                ],
                200
            );
        }

        return response()->json(['data' =>

        Appointment::
        orderBy('ScheduledTime', 'asc')
        ->where('Hospital_Id','=',Auth::user()->Hospital_Id)
        ->with(['patient:id,email,FirstName,LastName,profileimageUrl,MobilePhone,PatientCode','appointmenttype:id,name'])
        ->get(),


        ], 200);
    }

    public function creatediagnosis(Request $request)
    {
        if (Auth::check()) {
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
        return response()->json(['errors' => 'Unauthorized'], 401);
    }

    public function fetchdiagnosis()
    {
        if (Auth::check()) {
            return response()->json(
                [
                    'data' => Diagnosis::where(
                        'Hospital_Id',
                        '=',
                        auth()->user()->Hospital_Id
                    )
                        ->with(['createdby:id,FirstName,LastName'])
                        ->get(),
                ],
                200
            );
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function fetchonedoctor($id){

        if (Auth::check()) {
            //Validate User Inputs
            $user = User::where('id', '=', $id)->first();
            if ($user === null) {
                // user doesn't exist
                return response()->json(
                    ['message' => 'This Doctor does not exists'],
                    201
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
        return response()->json(['message' => 'Unauthorized'], 401);


    }



}
