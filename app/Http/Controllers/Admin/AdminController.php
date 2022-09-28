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
use App\Models\Pintakenote;
use App\Models\PtreatmentPlan;
use App\Models\Progresssnote;
use App\Models\Assigneddocotor;
use App\Models\Diagnosis;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
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
        if (Auth::user()->roles->first()->name == 'Admin') {
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
                $user->session = '1';
                $user->IsCredentialsNonExpired = '1';
                $user->Speciality=$request['Speciality'];
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
            $sms->sendSMS($request['telephone'], $message);

            return response()->json(
                [
                    'message' =>
                        $request['FirstName'] .
                        ' ' .
                        $request['LastName'] .
                        ' Account is successfully created ,Check your email address or Phone number for the Login credentials',
                    // 'user' => $user
                    'data' => collect('staff')
                        ->map(function ($item) use ($request, $user) {
                            return [
                                // 'id' => $item['id'],
                                'created_at' => $user->created_at,
                                'FirstName' => $request['FirstName'],
                                'LastName' => $request['LastName'],
                                'telephone' => $request['telephone'],
                                'email' => $request['email'],
                                'display_name' => Role::where(
                                    'id',
                                    '=',
                                    $request['RoleId']
                                )->value('display_name'),
                            ];
                        })
                        ->all(),

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
        if (!Auth::user()->roles->first()->name == 'Admin') {
            return response()->json(
                [
                    'errors' => 'Unauthorized User',
                ],
                401
            );
        }

        return response()->json(
            [
                'data' => User::orderBy('created_at', 'desc')
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
        if (Auth::user()->roles->first()->name == 'Admin') {
            // $myRoleId = json_decode(Auth::user()->roles->first()['Clinician'], true);

          $HospitalStaffRoles=Hospital::select('IsClinician','IsReceptionist','IsFinance')
          ->where('Doneby','=',Auth::user()->id)
          ->where('id','=',auth()->user()->Hospital_Id)->get();

          $row= $HospitalStaffRoles[0]['IsClinician'].','.$HospitalStaffRoles[0]['IsReceptionist'].','.$HospitalStaffRoles[0]['IsFinance'];
           $idsArr = explode(',',$row);

        //    DB::table('roles')->select('id', 'display_name')->whereIn('id',$idsArr)->get()->except('110'),

            return response()->json(
                [
                    'data' =>  Role::select('id', 'display_name')
                    ->get()
                    ->except('110'),

                ],
                200
            );
        }
        return response()->json(['message' => 'Unauthorized user'], 401);
    }

    //get hospital roles
    public function gethospitalroles(Request $request){

        if (Auth::user()->roles->first()->name == 'Admin') {

            DB::Table('hospital')
            ->where('id', '=', auth()->user()->Hospital_Id)
            ->update([
                'DoneBy' => Auth::user()->id,
                'IsClinician' =>$request['clinicianId'] ,
                'IsReceptionist' => $request['receptionId'],
                'IsFinance' =>  $request['financeId'],

            ]);
            return response()->json(['message' => 'Successfully saved your hospital Staff roles'], 201);



        }
        return response()->json(['errors' => 'Unauthorized user'], 401);



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
                'gender' => 'required',
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
            $patient->Languages = $request['Languages'];
            $patient->Employment = $request['Employment'];
            $patient->profileimageUrl = 'https://i.imgur.com/BKB2EQi.png';
            $patient->PatientCode = 'P-' . Str::random(8);
            $patient->gender = $request['gender'];
            $patient->Createdby_Id = auth()->user()->id;
            $patient->Guardian_Name=$request['Guardian_Name'];
            $patient->Guardian_Phone=$request['GuardianPhone'];
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
        $var = Auth::user()->roles->first()->name;
        if ($var == 'Admin' || $var == 'Reception') {
            return response()->json(
                [
                    'data' => Patient::where('AssignedDoctor_Id', '=', null)
                        ->with([
                            'doctor:id,Title,FirstName,LastName,telephone',
                            'LastAppointment',
                            'NextAppointment',
                            'doneby:id,FirstName,LastName,email,telephone,ProfileImageUrl',
                        ])

                        ->where('Hospital_Id', '=', auth()->user()->Hospital_Id)
                        ->orderBy('created_at', 'desc')
                        ->get(),
                ],
                200
            );
        } elseif ($var == 'Clinician') {
            return response()->json(
                [
                    'data' => Patient::where(
                        'Hospital_Id',
                        '=',
                        auth()->user()->Hospital_Id
                    )
                        ->where('AssignedDoctor_Id', '=', auth()->user()->id)
                        ->where('Status', '=', 'Active')
                        ->with([
                            'doctor:id,Title,FirstName,LastName,telephone',
                            'LastAppointment',
                            'NextAppointment',
                            'doneby:id,FirstName,LastName,email,telephone,ProfileImageUrl',
                        ])
                        ->orderBy('created_at', 'desc')
                        ->get(),
                ],
                200
            );
        }
        return response()->json(['message' => 'Unauthorized user'], 401);
    }

    public function fetchourAllpatients()
    {
        $var = Auth::user()->roles->first()->name;
        if ($var == 'Admin') {
            return response()->json(
                [
                    'data' => Patient::where(
                        'Hospital_Id',
                        '=',
                        auth()->user()->Hospital_Id
                    )
                        ->with([
                            'doctor:id,Title,FirstName,LastName,telephone',
                            'LastAppointment',
                            'NextAppointment',
                            'doneby:id,FirstName,LastName,email,telephone,ProfileImageUrl',
                        ])
                        ->orderBy('created_at', 'desc')
                        ->get(),
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
                ->where('Hospital_Id', '=', auth()->user()->Hospital_Id)
                ->first();
            if ($user === null) {
                // user doesn't exist
                return response()->json(
                    ['message' => 'This patient does not exists in our hospital'],
                    404
                );
            };

            $diagnosisIntake=Pintakenote::select('Diagnosis')->where('Patient_Id','=',$id)->value('Diagnosis');
            $diagnosistreatmentplan=PtreatmentPlan::select('Diagnosis_Id')->where('Patient_Id','=',$id)->value('Diagnosis_Id');
            $diagnosisprogressnote=Progresssnote::select('Diagnosis')->where('Patient_Id','=',$id)->value('Diagnosis');



            return response()->json(
                [
                    'data' => Patient::where('id', '=', $id)
                        ->with([
                            'doctor:id,Title,FirstName,LastName,telephone',
                            'LastAppointment',
                            'NextAppointment',
                            'doneby:id,FirstName,LastName,email,telephone,ProfileImageUrl',
                        ])
                        ->where('Hospital_Id', '=', auth()->user()->Hospital_Id)
                        ->get(),
                        'PreviousAssignedDoctor'=>

                        collect(Assigneddocotor::where('Patient_Id','=',$id)->where('Hospital_Id','=',auth()->user()->Hospital_Id)->get())
                        ->map(function ($item) {
                            return [
                                'Assigned_At' =>$item['Date'],
                                'doctor' => User::where('id','=',$item['Doctor_Id'])->where('Hospital_Id','=',auth()->user()->Hospital_Id)->get(),
                              ];
                        })
                        ->all()
                        ,
                        'Diagnosis'=>$diagnosisIntake.$diagnosistreatmentplan.$diagnosisprogressnote,

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
                    'data' => User::orderBy('created_at', 'desc')
                        ->join('roles', 'users.Role_id', '=', 'roles.id')
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
        if (
            Auth::user()->roles->first()->name ==
            ('Admin' || 'Reception' || 'Cashier')
        ) {
            //Validate User Inputs
            $validator = Validator::make($request->all(), [
                'Doctor_Id' => 'required|exists:users,id',
                'PatientId' => 'required|exists:patients,id',
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

                    $record = Assigneddocotor::select('AssignedBy_Id','Date')->where(['Hospital_Id' => auth()->user()->Hospital_Id])
                   ->where('Patient_Id','=',$request['PatientId'])
                   ->where('Doctor_Id','=',$request['Doctor_Id']);
                    if ($record->exists()) {
                        // $record->delete();
                        $doneby=$record->get()[0]['AssignedBy_Id'];
                        $doneAt=$record->get()[0]['Date'];
                        $PatInfo=Patient::find($request['PatientId']);
                        $drInfo=User::find($request['Doctor_Id']);
                        $userInfo=User::find($doneby);
                        return response()->json(
                            [
                                'message' =>
                                $drInfo->Title.' '.$drInfo->FirstName.' '.$drInfo->LastName.' is already assigned to this patient '.$PatInfo->FirstName.' '.$PatInfo->LastName.' , Done by '.$userInfo['Title'].' '.$userInfo['FirstName'].' '.$userInfo['LastName']. ' At '.Carbon::parse($doneAt)->diffForHumans(),
                            ],
                            200
                        );

                    }

                    $dr=new Assigneddocotor();
                    $dr->Hospital_Id=auth()->user()->Hospital_Id;
                    $dr->Doctor_Id=$request['Doctor_Id'];
                    $dr->Patient_Id=$request['PatientId'];
                    $dr->AssignedBy_Id=auth()->user()->id;
                    $dr->Date=Carbon::now()->toDateTimeString();
                    $dr->Status='On Treatment';
                    $dr->save();

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
                [
                    'message' =>
                        'Patient is Inactive or dormant or have not payed, Please consult Hospital Admin or Finance officer for support ',
                ],
                200
            );
        }
        return response()->json(['message' => 'Unauthorized user'], 401);
    }

    public function activatepatient(Request $request)
    {
        if (
            Auth::user()->roles->first()->name ==
            ('Admin' || 'Reception' || 'Cashier')
        ) {
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
                'name' => 'required',
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
            return response()->json(
                [
                    'data' => TypeAppointment::
                        where(
                            'hospital_Id',
                            '=',
                            auth()->user()->Hospital_Id
                        )
                        ->with(['creator:id,FirstName,LastName'])
                        ->get(),
                ],
                200
            );
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
                'Location' => 'required',
                'Schedule' => 'required',
                'Duration' => 'required',
                'Frequency' => 'required',
                'AppointmentNote' => 'required',
            ]);
            if ($validator->fails()) {
                // return response()->json($validator->errors(), 422);

                return response()->json(
                    ['errors' => implode($validator->errors()->all())],
                    422
                );
            }
            $recordAppoint = TypeAppointment::where(
                'id',
                '=',
                $request['AppointmentType_Id']
            )->where('Hospital_Id', '=', auth()->user()->Hospital_Id);

            if (!$recordAppoint->exists()) {
                return response()->json(
                    [
                        'errors' =>
                            'This Appointment type does not exists in our hospital',
                    ],
                    404
                );
            }
            $recordpat = Patient::where(
                'id',
                '=',
                $request['Patient_Id']
            )->where('Hospital_Id', '=', auth()->user()->Hospital_Id);

            if (!$recordpat->exists()) {
                return response()->json(
                    [
                        'message' =>
                            'This Patient does not exists in our hospital',
                    ],
                    404
                );
            }

            $patData = Patient::select(
                'FirstName',
                'LastName',
                'MobilePhone',
                'email'
            )
                ->where('id', '=', $request['Patient_Id'])
                ->get();

            $AssignedDoctorId = Patient::select('AssignedDoctor_Id')
                ->where('id', '=', $request['Patient_Id'])
                ->value('AssignedDoctor_Id');

            if ($AssignedDoctorId == null) {
                return response()->json(
                    [
                        'message' =>
                            'Sorry Patient ' .
                            $patData[0]->FirstName .
                            ' ' .
                            $patData[0]->LastName .
                            ' does not have assigned doctor, please first assign the patient with the doctor first',
                    ],
                    404
                );
            }

            $recorddoct = User::where('id', '=', $AssignedDoctorId)->where(
                'Hospital_Id',
                '=',
                auth()->user()->Hospital_Id
            );

            if (!$recorddoct->exists()) {
                return response()->json(
                    [
                        'message' =>
                            'This Doctor does not exists in our hospital',
                    ],
                    404
                );
            }

            $typeApp = TypeAppointment::select('name')
                ->where('Hospital_Id', '=', auth()->user()->Hospital_Id)
                ->where('id', '=', $request['AppointmentType_Id'])
                ->get();

            $doctorData = User::select(
                'FirstName',
                'LastName',
                'Title',
                'Hospital_Id'
            )
                ->where('id', '=', $AssignedDoctorId)
                ->get();

            $hospitalName = Hospital::select(
                'PracticeName',
                'District',
                'Sector',
                'Cell',
                'Village'
            )
                ->where('id', '=', $doctorData[0]->Hospital_Id)
                ->get();

            $appointment = new Appointment();
            $appointment->AppointmentType_Id = $request['AppointmentType_Id'];
            $appointment->Patient_Id = $request['Patient_Id'];
            $appointment->Doctor_Id = $AssignedDoctorId;
            $appointment->Location = $request['Location'];
            $appointment->ScheduledTime = $request['Schedule'];
            $appointment->Duration = $request['Duration'];
            $appointment->Frequency = $request['Frequency'];
            $appointment->CreatedBy_Id = auth()->user()->id;
            $appointment->Status = 'Active';
            $appointment->AppointmentAlert = $request['AppointmentNote'];
            $appointment->Hospital_Id = auth()->user()->Hospital_Id;
            $appointment->calendarGridType=$request['calendarGridType'];

            $sms = new TransferSms();
            if ($request['Location'] == 'online') {
                $link = 'https://meet.letsreason.co/EMR-Session-test';
                $appointment->link = $link;

                $message =
                    'Hello ' .
                    $patData[0]->FirstName .
                    ' ' .
                    $patData[0]->LastName .
                    ' Your ' .
                    $typeApp[0]->name .
                    ' Appointment at  ' .
                    $hospitalName[0]->PracticeName .
                    ' ' .
                    $doctorData[0]->Title .
                    ' ' .
                    $doctorData[0]->FirstName .
                    ' ' .
                    $doctorData[0]->LastName .
                    ' has been scheduled successfully , Date: ' .
                    $request['Schedule'] .
                    ' Location: ' .
                    $request['Location'] .
                    ' and Video Link is:  ' .
                    $link;

                $sms->sendSMS($patData[0]->MobilePhone, $message);
            } else {
                $msg =
                    'Hello ' .
                    $patData[0]->FirstName .
                    ' ' .
                    $patData[0]->LastName .
                    ' Your ' .
                    $typeApp[0]->name .
                    ' Appointment at  ' .
                    $hospitalName[0]->PracticeName .
                    ' Located at ' .
                    $hospitalName[0]->District .
                    ' ,' .
                    $hospitalName[0]->Sector .
                    ',' .
                    $hospitalName[0]->Cell .
                    ' with ' .
                    $doctorData[0]->Title .
                    ' ' .
                    $doctorData[0]->FirstName .
                    ' ' .
                    $doctorData[0]->LastName .
                    ' has been scheduled successfully , Date: ' .
                    $request['Schedule'] .
                    ' Venue: ' .
                    $request['Location'];

                 $sms->sendSMS($patData[0]->MobilePhone, $msg);

                $appointment->link = 'null';
            }

             $appointment->save();

            // $PatientnextAppointment = Appointment::select('id', 'ScheduledTime')
            //     ->where('Hospital_Id', '=', auth()->user()->Hospital_Id)
            //     ->where('Patient_Id', '=', $request['Patient_Id'])
            //     ->whereDate('ScheduledTime', '>', Carbon::now())
            //     ->orderBy('ScheduledTime', 'ASC')
            //     ->first();


            // $PatientlastAppointment = Appointment::select('id', 'ScheduledTime')
            //     ->where('Hospital_Id', '=', auth()->user()->Hospital_Id)
            //     ->where('Patient_Id', '=', $request['Patient_Id'])
            //     ->whereDate('ScheduledTime', '<', Carbon::now())
            //     ->orderBy('ScheduledTime', 'DESC')
            //     ->first();
            // if ($PatientlastAppointment == null) {
            //     DB::Table('patients')
            //         ->where('Hospital_Id', '=', auth()->user()->Hospital_Id)
            //         ->where('id', '=', $request['Patient_Id'])
            //         ->update([
            //             'lastappoint' => $appointment->id,
            //         ]);
            // }

            // DB::Table('patients')
            //     ->where('Hospital_Id', '=', auth()->user()->Hospital_Id)
            //     ->where('id', '=', $request['Patient_Id'])
            //     ->update([
            //         // 'nextappoint' => $PatientnextAppointment->id,
            //     ]);

            if ($appointment) {
                return response()->json(
                    [
                        'message' =>
                            $typeApp[0]->name .
                            ' Appointment of ' .
                            $patData[0]->FirstName .
                            ' ' .
                            $patData[0]->LastName .
                            ' has been created Successfully ',
                    ],
                    201
                );
            }else{
            return response()->json(
                [
                    'message' =>
                        'Ooops Something went wrong on our side, we are fixing it ASAP',
                ],
                401
            );}
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
                401
            );
        }

        return response()->json(
            [
                'data' => Appointment::where(
                    'Hospital_Id',
                    '=',
                    Auth::user()->Hospital_Id
                )
                    ->with([
                        'patient:id,email,FirstName,LastName,profileimageUrl,MobilePhone,PatientCode',
                        'appointmenttype:id,name',
                        'doctor:id,Title,FirstName,LastName',
                    ])
                    ->orderBy('ScheduledTime', 'asc')
                    ->get(),
            ],
            200
        );
    }

    public function getappointmentbyid($appointmentId)
    {
        if (Auth::check()) {
            $user = Appointment::where('id', '=', $appointmentId)->first();
            if ($user === null) {
                // Appointment doesn't exist
                return response()->json(
                    ['message' => 'This Appointment does not exists'],
                    201
                );
            }

            return response()->json(
                [
                    'data' => Appointment::orderBy('ScheduledTime', 'asc')
                        ->where('Hospital_Id', '=', Auth::user()->Hospital_Id)
                        ->where('id', '=', $appointmentId)
                        ->with([
                            'doctor:id,email,telephone,Title,FirstName,LastName',
                            'patient',
                            'appointmenttype:id,name',
                        ])
                        ->get(),
                ],
                201
            );
        }
        return response()->json(['message' => 'Unauthorized User '], 401);
    }

    public function getonepatientappointments($patientId)
    {
        if (Auth::check()) {
            $patApp = Appointment::where(
                'Patient_Id',
                '=',
                $patientId
            )->first();
            $pat = Patient::where('id', '=', $patientId)->first();
            if ($pat === null) {
                // Patient checks
                return response()->json(
                    ['message' => 'This Patient does not exists our system'],
                    201
                );
            } elseif ($patApp === null) {
                return response()->json(
                    ['message' => 'This Patient does not have any appointment'],
                    201
                );
            }

            return response()->json(
                [
                    'data' => Appointment::orderBy('ScheduledTime', 'asc')
                        ->where('Hospital_Id', '=', Auth::user()->Hospital_Id)
                        ->where('Patient_Id', '=', $patientId)
                        ->get(),
                ],
                200
            );
        }
        return response()->json(['message' => 'Unauthorized user'], 201);
    }

    public function creatediagnosis(Request $request)
    {
        if (Auth::user()->roles->first()->name == 'Admin') {
            //Validate User Inputs
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:diagnosis',
                'icd10' => 'required|unique:diagnosis',
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
            $diagnsosis->icd10 = $request['icd10'];
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
        $var = Auth::user()->roles->first()->name;
        if ($var == 'Admin' || $var == 'Clinician') {
            return response()->json(
                [
                    'data' => Diagnosis
                        // where(
                        //     'Hospital_Id',
                        //     '=',
                        //     auth()->user()->Hospital_Id
                        // )
                        ::with(['createdby:id,FirstName,LastName'])
                        ->get(),
                ],
                200
            );
        }
        return response()->json(['message' => 'Unauthorized user'], 401);
    }

    public function fetchonedoctor($id)
    {
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
                        'Hospital_Id'
                    )
                        ->with(
                            'hospital:id,PracticeName,TypeOrganization,BusinessPhone,BusinessEmail,TypeOrganization'
                        )
                        ->where('id', '=', $id)
                        ->where('Hospital_Id', '=', auth()->user()->Hospital_Id)
                        ->get(),
                ],
                200
            );
        }
        return response()->json(['message' => 'Unauthorized user'], 401);
    }

    public function editpatientprofile(Request $request,$PatientId){
        if (Auth::check()) {
     if (Patient::find($PatientId) === null) {
         // user doesn't exist
         return response()->json(
             ['message' => 'This patient does not exists'],
             404
         ); }
     $input = $request->all();

     Patient::find($PatientId)->update($input);

   if(  $request->hasFile('profileimageUrl')) {
        $file = $request->file('profileimageUrl');
        $fileName = $file->getClientOriginalName() ;
        $destinationPath = public_path().'/Patient/Profile/Images' ;
        $file->move($destinationPath,$fileName);

        Patient::find($PatientId)->update(['profileimageUrl'=>'http://45.76.141.125/Patient/Profile/Images/'.$fileName]);  }



     return response()->json(
        ['message' => 'Patient profile is updated successfully'],
        200 );

    }
    return response()->json(['message' => 'Unauthorized user '], 401);



    }



}

