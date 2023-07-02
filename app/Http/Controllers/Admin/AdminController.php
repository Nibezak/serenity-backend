<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\Insurance;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TypeAppointment;
use App\Models\Appointment;
use App\Models\Role;
use App\Models\Patient;
use App\Models\Pintakenote;
use App\Models\PatientInsurance;
use App\Models\PtreatmentPlan;
use App\Models\Progresssnote;
use App\Models\Assigneddocotor;
use App\Models\Diagnosis;
use App\Models\Session;
use App\Models\Department;
use App\Models\Contactnote;
use App\Models\Consulationnote;
use App\Models\Terminationnote;
use App\Models\Missedappointmentnote;
use App\Models\Miscnote;
use App\Models\Processnote;
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
        if (Auth::user()->roles->first()->name ==  ('Admin' || 'superAdmin' )
        ) {
            //validate inputs
            $validator = Validator::make($request->all(), [
                'FirstName' => 'required',
                'LastName' => 'required',
                'email' => 'required|unique:users',
                'telephone' => 'required',
                'Address' => 'required',
                'RoleId' => 'required',
                'Title' => 'required',
                'DepartmentId'=>'required|exists:departments,id'

            ]);
            if ($validator->fails()) {
                // return response()->json($validator->errors()->toJson(), 400);

                return response()->json(
                    ['errors' => implode($validator->errors()->all())],
                    422
                );
            }

            // $defaultManagerPswd = Str::random(10);
            $defaultManagerPswd = 'password';

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
                $user->Department_Id=$request['DepartmentId'];

                $user->save();

                $user->roles()->attach($role);
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
        if (!Auth::user()->roles->first()->name == ('Admin' || 'superAdmin' )) {
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
        if (Auth::user()->roles->first()->name == ('Admin' || 'superAdmin' )) {

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

        if (Auth::user()->roles->first()->name == ('Admin' || 'superAdmin' )) {

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
                'gender' => 'required',
                'Employment' => 'required',
                'Languages' => 'required',
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
            $patient->Status = 'Active'; // === To Be changed === //
            $patient->Createdby_Id = auth()->user()->id;
            if($request->has('Guardian_Name')){
                $patient->Guardian_Name=$request['Guardian_Name'];
                $patient->Guardian_Phone=$request['GuardianPhone'];
            }
            else {
                $patient->Guardian_Name= 'not defined';
                $patient->Guardian_Phone='not defined';
            }
            $patient->save();
            if ($request->has('insurance')) {

                PatientInsurance::insert(collect($request['insurance'])
            ->map(function ($item)  use ($patient){
                return [
                    'InsuranceCode' => $item['InsuranceCode'],
                    'Name' => $item['Name'],
                    'Compliment' => $item['Compliment'],
                    'CreatedBy_Id' => $patient->Createdby_Id,
                    'Patient_Id' => $patient->id,
                    'created_at'=>$patient->created_at,
                    'updated_at'=>$patient->updated_at,
                 ];
            })
            ->all());

            }

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
        if ($var == 'Admin' || $var == 'Reception'||$var=='superAdmin') {
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
        } elseif ($var == 'Employee') {

        $datapat=Patient::where(
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
            ->get();
            return response()->json(
                [
                    'data' =>

                    collect($datapat)
                ->map(function ($item) {
                    return [
                        'id'=>$item['id'],
                        'PatientCode' => $item['PatientCode'],
                        'FirstName' =>$item['FirstName'],
                        'LastName' =>$item['LastName'],
                        'MobilePhone'=>$item['MobilePhone'],
                        'email'=>$item['email'],
                        'Status'=>$item['Status'],
                        'doctor' =>$item['doctor'],
                        'HomePhone'=>$item['HomePhone'],
                        'WorkPhone'=>$item['WorkPhone'],
                        'Dob'=>$item['Dob'],
                        'GenderIdentity'=>$item['GenderIdentity'],
                        'AccountNumber'=>$item['AccountNumber'],
                        'Address'=>$item['Address'],
                        'BloodType'=>$item['BloodType'],
                        'Height'=>$item['Height'],
                        'Weight'=>$item['Weight'],
                        'MartialStatus'=>$item['MartialStatus'],
                        'AdministrativeSex'=>$item['AdministrativeSex'],
                        'SexualOrientation'=>$item['SexualOrientation'],
                        'Employment'=>$item['Employment'],
                        'Languages'=>$item['Languages'],
                        'Createdby_Id'=>$item['Createdby_Id'],
                        'Nationality'=>$item['Nationality'],
                        'SSN'=>$item['SSN'],
                        'Province'=>$item['Province'],
                        'District'=>$item['District'],
                        'Sector'=>$item['Sector'],
                        'Cell'=>$item['Cell'],
                        'Village'=>$item['Village'],
                        'StreetCode'=>$item['StreetCode'],
                        'Hospital_Id'=>$item['Hospital_Id'],
                        'AssignedDoctor_Id'=>$item['AssignedDoctor_Id'],
                        'created_at'=>$item['created_at'],
                        'updated_at'=>$item['updated_at'],
                        'profileimageUrl'=>$item['profileimageUrl'],
                        'gender'=>$item['gender'],
                        'Guardian_Name'=>$item['Guardian_Name'],
                        'Guardian_Phone'=>$item['Guardian_Phone'],
                        'last_appointment' =>$item['last_appointment'],
                        'next_appointment' =>$item['next_appointment'],
                        'doneby' =>$item['doneby'],
                        'all_patient_appointment'=>Appointment::where('Patient_Id','=',$item['id'])->where('Hospital_Id','=',auth()->user()->Hospital_Id)->get()->first(),

                      ];
                })
                ->all(),
                ],
                200
            );
        }
        return response()->json(['message' => 'Unauthorized user'], 401);
    }

    public function fetchourAllpatients()
    {
        $var = Auth::user()->roles->first()->name;
        if ($var == 'Admin' ||$var == 'superAdmin') {
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
                    ['message' => 'This Client does not exists in our System'],
                    404
                );
            };

            $diagnosisIntake=Pintakenote::select('Diagnosis')->where('Patient_Id','=',$id)->where('Hospital_Id','=',auth()->user()->Hospital_Id)->get();
            $diagnosistreatmentplan=PtreatmentPlan::select('Diagnosis_Id')->where('Patient_Id','=',$id)->where('Hospital_Id','=',auth()->user()->Hospital_Id)->get();
            $diagnosisprogressnote=Progresssnote::select('Diagnosis')->where('Patient_Id','=',$id)->where('Hospital_Id','=',auth()->user()->Hospital_Id)->get();


            $intake = "";
            $treatment="";
            $progress="";
            foreach($diagnosisIntake as $key => $value ){
                $intake .= $value['Diagnosis'].",";
            }
            foreach($diagnosistreatmentplan as $key => $value2 ){
                $treatment .= $value2['Diagnosis_Id'].",";
            }
            foreach($diagnosisprogressnote as $key => $value3 ){
                $progress .= $value3['Diagnosis'].",";
            }


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
                        'Diagnosis'=>implode(',', array_unique(explode(',', str_replace( array('[',']','"') , ''  ,$intake.$treatment.$progress)))),

                ],
                200
            );
        }
        // return response()->json(['message' => 'Unauthorized user '], 401);

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
                        ->where('roles.name', '=', 'Employee')
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
            ('Admin' || 'Reception' || 'Cashier'||'superAdmin')
        ) {
            //Validate User Inputs
            $validator = Validator::make($request->all(), [
                'Doctor_Id' => 'required|exists:users,id',
                'PatientId' => 'required|exists:patients,id',
                'Service_Id' =>'required|exists:typeappointments,id',
            ]);
            if ($validator->fails()) {
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


                    $sess=new Session();
                    $sess->StartedBy_Id=auth()->user()->id;
                    $sess->hospital_Id=auth()->user()->Hospital_Id;
                    $sess->Patient_Id=$request['PatientId'];
                    $sess->Doctor_Id=$request['Doctor_Id'];
                    if($request->has('Insurance_Id')){
                    $sess->Insurance_Id=$request['Insurance_Id'];
                    }
                    $sess->Service_Id=$request['Service_Id'];
                    $sess->save();

                    $result = DB::Table('patients')
                        ->where('id', '=', $request['PatientId'])
                        ->where('Hospital_Id', '=', auth()->user()->Hospital_Id)
                        ->update([
                            'AssignedDoctor_Id' => $request['Doctor_Id'],
                        ]);

                    return $result = [
                        'message' =>
                            'Client is assigned to Employee successfully !!  ',
                        'success' => true,
                        'Session_Id'=>$sess->id,
                    ];
                } else {
                    return $result = [
                        'message' => 'Client Not Found !! ',
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


  //activate patient

    public function activatepatient(Request $request)
    {
        if (
            Auth::user()->roles->first()->name ==
            ('Admin' || 'Reception' || 'Cashier'||'superAdmin')
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
        if (Auth::user()->roles->first()->name == ('Admin' || 'superAdmin' )) {
            //Validate User Inputs
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'amount'=>'required',
                'description'=>'required',
                'currency'=>'required',

            ]);
            if ($validator->fails()) {
                // return response()->json($validator->errors(), 422);

                return response()->json(
                    ['errors' => implode($validator->errors()->all())],
                    422
                );
            }
            if(TypeAppointment::where('Hospital_Id','=',auth()->user()->Hospital_Id)->where('name','=',$request['name'])->exists()){

                return response()->json(
                    ['message' => 'This service exists in our System'],
                    200
                );
            }

            $service = new TypeAppointment();

            $service->name = $request['name'];
            $service->Description=$request['description'];
            $service->Currency=$request['currency'];
            $service->Amount=$request['amount'];
            $service->createdBy_Id = auth()->user()->id;
            $service->hospital_Id = auth()->user()->Hospital_Id;
            $service->save();

            return response()->json(
                ['message' => 'Created a new hospital service successfully !!'],
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
                'Patient_Id' => 'required|exists:patients,id',
                'Location' => 'required',
                'start' => 'required',
                'end' => 'required',
                'Duration' => 'required',
                'Frequency' => 'required',
                // 'title' => 'required',
                'Doctor_Id' => 'required|exists:users,id',
                'Service_Id' =>'required|exists:typeappointments,id',
                // 'sessionType'=>'required'

            ]);
            if ($validator->fails()) {

              return response()->json(
                    ['errors' => implode($validator->errors()->all())],
                    422
                );
            }
// $doctorData = User::find($request['Doctor_Id']);
            $patData = Patient::select(
                'FirstName',
                'LastName',
                'MobilePhone',
                'email'
            )
                ->where('id', '=', $request['Patient_Id'])
                ->first();

            // $AssignedDoctorId = Patient::select('AssignedDoctor_Id')
            //     ->where('id', '=', $request['Patient_Id'])
            //     ->value('AssignedDoctor_Id');

            // if ($AssignedDoctorId == null) {
            //     return response()->json(
            //         [
            //             'message' =>
            //                 'Sorry Patient ' .
            //                 $patData[0]->FirstName .
            //                 ' ' .
            //                 $patData[0]->LastName .
            //                 ' does not have assigned doctor, please first assign the patient with the doctor first',
            //         ],
            //         404
            //     );
            // }

            $typeApp = TypeAppointment::select('name')
                // ->where('Hospital_Id', '=', auth()->user()->Hospital_Id)
                ->where('id', '=', $request['Service_Id'])
                ->first();

            $doctorData = User::select(
                'FirstName',
                'LastName',
                'Title',
                'Hospital_Id'
            )
                ->where('id', '=', $request['Doctor_Id'])
                ->first();

            $hospitalName = Hospital::select(
                'PracticeName',
                'District',
                'Sector',
                'Cell',
                'Village'
            )
                ->where('id', '=', $doctorData->Hospital_Id)
                ->first();

                $sess=new Session();
                $sess->StartedBy_Id=auth()->user()->id;
                $sess->hospital_Id=auth()->user()->Hospital_Id;
                $sess->Patient_Id=$request['Patient_Id'];
                $sess->Doctor_Id=$request['Doctor_Id'];
                if($request->has('Insurance_Id')){
                $sess->Insurance_Id=$request['Insurance_Id'];
                }
                $sess->Service_Id=$request['Service_Id'];
                $sess->type=$request['sessionType'];
                $sess->save();

            $appointment = new Appointment();
            $appointment->AppointmentType_Id = $request['Service_Id'];
            $appointment->Patient_Id = $request['Patient_Id'];
            $appointment->Doctor_Id =$request['Doctor_Id'];
            $appointment->Location = $request['Location'];
            $appointment-> start= $request['start'];
            $appointment-> end= $request['end'];
            $appointment-> title= $request['title'];
            $appointment->Duration = $request['Duration'];
            $appointment->Frequency = $request['Frequency'];
            $appointment->CreatedBy_Id = auth()->user()->id;
            $appointment->Status = 'Active';
            $appointment->Hospital_Id = auth()->user()->Hospital_Id;
            $appointment->calendarGridType=$request['calendarGridType'];
            $appointment->Session_Id=$sess->id;

            if ($request['sessionType'] == 'followUp') {
                    $dr=new Assigneddocotor();
                    $dr->Hospital_Id=auth()->user()->Hospital_Id;
                    $dr->Doctor_Id=$request['Doctor_Id'];
                    $dr->Patient_Id=$request['Patient_Id'];
                    $dr->AssignedBy_Id=auth()->user()->id;
                    $dr->Date=Carbon::now()->toDateTimeString();
                    $dr->Status='Follow up Session';
                    $dr->save();

                    DB::Table('patients')
                        ->where('id', '=', $request['Patient_Id'])
                        ->where('Hospital_Id', '=', auth()->user()->Hospital_Id)
                        ->update([
                            'AssignedDoctor_Id' => $request['Doctor_Id'],
                            'Status'=>'Active',
                        ]);
                        DB::Table('sessions')
                        ->where('id', '=', $sessionId)
                        ->update([
                            'Status' => 'Completed',
                        ]);
            }

            $sms = new TransferSms();
            if ($request['Location'] == 'online') {
                $link = 'https://meet.Serenity.co/EMR-Session-test/'.MD5($sess->id);
                $appointment->link = $link;
                $message =
                    'Hello ' .
                    $patData->FirstName .
                    ' ' .
                    $patData->LastName .
                    ' Your ' .
                    $typeApp->name .
                    ' Appointment at  ' .
                    $hospitalName->PracticeName .
                    ' ' .
                    $doctorData->Title .
                    ' ' .
                    $doctorData->FirstName .
                    ' ' .
                    $doctorData->LastName .
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
                    $patData->FirstName .
                    ' ' .
                    $patData->LastName .
                    ' Your ' .
                    $typeApp->name .
                    ' Appointment at  ' .
                    $hospitalName->PracticeName .
                    ' Located at ' .
                    $hospitalName->District .
                    ' ,' .
                    $hospitalName->Sector .
                    ',' .
                    $hospitalName->Cell .
                    ' with ' .
                    $doctorData->Title .
                    ' ' .
                    $doctorData->FirstName .
                    ' ' .
                    $doctorData->LastName .
                    ' has been scheduled successfully , Date: ' .
                    $request['Schedule'] .
                    ' Venue: ' .
                    $request['Location'];
            //  $sms->sendSMS($patData[0]->MobilePhone, $msg);
                $appointment->link = 'null';
            }
             $appointment->save();
            if ($appointment) {
                return response()->json(
                    [
                        'message' =>
                            $typeApp->name .
                            ' Appointment of ' .
                            $patData->FirstName .
                            ' ' .
                            $patData->LastName .
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
                        // ->where('Hospital_Id', '=', Auth::user()->Hospital_Id)
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
                    ['message' => 'This Client does not exists our system'],
                    201
                );
            } elseif ($patApp === null) {
                return response()->json(
                    ['message' => 'This Client does not have any appointment'],
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
        if (Auth::user()->roles->first()->name == ('Admin' || 'superAdmin' )) {
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
        if ($var == 'Admin' || $var == 'Employee'||'superAdmin') {
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
                    ['message' => 'This Employee does not exists'],
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
             ['message' => 'This Client does not exists'],
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

    public function saveinsurance(Request $request){

        $var = Auth::user()->roles->first()->name;
        if ($var == ('Admin' || 'superAdmin' ) ) {

            $validator = Validator::make($request->all(), [
                'Insurance_name' => 'required',
            ]);
            if ($validator->fails()) {
                 return response()->json(
                    ['errors' => implode($validator->errors()->all())],
                    422
                );
            }

            if(Insurance::where('Hospital_Id','=',auth()->user()->Hospital_Id)->where('Name','=',$request['Insurance_name'])->exists()){
                return response()->json(
                    ['errors' => 'Sorry, This insurance is already registered in our hospital'],
                    422
                );
            }

            $insurance = new Insurance ();
            $insurance -> CreatedBy_Id= auth()->user()->id;
            $insurance -> Hospital_Id= auth()->user()->Hospital_Id;
            $insurance -> Name = $request['Insurance_name'];
            $insurance->save();

            return response()->json(['message' => 'Successfully, Added new Insurance that works with our hospital'], 201);



        }

        return response()->json(['message' => 'Unauthorized user'], 401);


    }


public function fetchhospitalinsurance(){
    if (!Auth::check()) {
        return response()->json(
            [
                'message' => 'Unauthorized User',
            ],
            401
        );
    }
    return response()->json(['data' =>Insurance::where('Hospital_Id','=',auth()->user()->Hospital_Id)->with('createdby:id,Title,FirstName,LastName,ProfileImageUrl,telephone') ->get() ->makeHidden(['CreatedBy_Id','Hospital_Id']) ], 200);



}


public function fetchpatientactivesession($PatientId){

    $var = Auth::user()->roles->first()->name;
    if ($var == 'Admin' || $var == 'Employee'||$var=='superAdmin') {

        if (Patient::where('id','=',$PatientId)->where('Hospital_Id','=',auth()->user()->Hospital_Id)->exists()) {

            $allpatientsession= Session::where('Patient_Id','=',$PatientId)
            ->where('Hospital_Id','=',auth()->user()->Hospital_Id)
            ->where('Status','=','Pending')->get()->makeHidden('Status');


        return  collect($allpatientsession)
        ->map(function ($item)  {

        $insurer=PatientInsurance::where('id','=',$item['Insurance_Id'])->get();
         $serviceapointtypeid=Appointment::where('Session_Id','=',$item['id'])->get();
            return [
            'Session_Id'=>$item['id'],
            'Patient'=>$item['patient']['FirstName'].' '.$item['doctor']['LastName'],
           'Doctor' =>
                            $item['doctor']['Title'] .
                            ' ' .
                            $item['doctor']['FirstName'] .
                            ' ' .
                            $item['doctor']['LastName'],
                        'CreatedBy' =>
                            $item['doneby']['Title'] .
                            ' ' .
                            $item['doneby']['FirstName'] .
                            ' ' .
                            $item['doneby']['LastName'],
                        'DoctorImage' => $item['doctor']['ProfileImageUrl'],
                        'CreatorImage' => $item['doneby']['ProfileImageUrl'],
                        'PatientImage' => $item['patient']['profileimageUrl'],
             'Insurance_Name'=>$insurer[0]['Name'],
             'Insurance_Code'=>$insurer[0]['InsuranceCode'],
             'Insurance_Compliment'=>$insurer[0]['Compliment'],
             'services'=>collect($serviceapointtypeid)
             ->map(function ($item) {
                 return [
                     'Service_name' =>TypeAppointment::where('id','=',$item['AppointmentType_Id'])->value('name') ,

                 ];
             })
             ->all()[0]['Service_name'],

              'data'=>$this->fetchonepatient($item['patient']['id'])->original['data'],
             'created_at'=>$item['created_at'],
             'updated_at'=>$item['updated_at'],

            ];
        })
        ->toArray();


        }return response()->json(
            ['message' => 'Patient does not exists in our hospital'],
            200
        );



   }
     return response()->json(['message' => 'Unauthorized user'], 401);

}


    public function fetchinsurancepatient($PatientId){


        return response()->json(['data' => PatientInsurance::where('Patient_Id','=',$PatientId)->get()], 200);


     }




public function savedepartment(Request $request){

    $var = Auth::user()->roles->first()->name;
    if ($var == 'Admin' || $var=='superAdmin') {

        $validator = Validator::make($request->all(), [
            'Department_name' => 'required',
        ]);
        if ($validator->fails()) {
             return response()->json(
                ['errors' => implode($validator->errors()->all())],
                422
            );
        }

        if(Department::where('Hospital_Id','=',auth()->user()->Hospital_Id)->where('Name','=',$request['Department_name'])->exists()){
            return response()->json(
                ['errors' => 'Sorry, This Department is already registered in our hospital'],
                422
            );
        }
        $dpt = new Department ();
        $dpt -> CreatedBy_Id= auth()->user()->id;
        $dpt -> Hospital_Id= auth()->user()->Hospital_Id;
        $dpt -> Name = $request['Department_name'];
        $dpt->save();
        return response()->json(['message' => 'Successfully, Added new Department '], 201);

    }
    return response()->json(['message' => 'Unauthorized user'], 401);


   }


  public function fetchdepartment(){

  return Department::where('Hospital_Id','=',auth()->user()->Hospital_Id)->get();

  }


  public function endpatientsession($sessionId){

    if (
        Auth::user()->roles->first()->name ==
        ('Admin' || 'Employee'||'superAdmin')
    ) {

        $ses = Session::where('id', '=', $sessionId)->first();
        if ($ses === null) {
            return response()->json(
                ['errors' => 'Invalid session'],
                422
            );
        }

    DB::Table('sessions')
    ->where('id', '=', $sessionId)
    ->update([
        'Status' => 'Completed',
    ]);
   return  response()->json(['message' => 'Session is ended successfully'],200);


}
return response()->json(['message' => 'UnAuthorized User'],401);

  }


public function fetchdoctoractivesession($drId){


    if (
        Auth::user()->roles->first()->name ==
        ('Admin' || 'Employee'||'superAdmin')
    ) {

        $ses = Session::where('Hospital_Id', '=', auth()->user()->Hospital_Id)
        ->where('Doctor_Id','=',$drId)
        ->first();
        if ($ses === null) {
            return response()->json(
                ['errors' => 'This doctor does not have any active session'],
                422
            );
        }

        return  Session::
        where('Hospital_Id','=',auth()->user()->Hospital_Id)
        ->where('Doctor_Id','=',$drId)
        ->where('Status','=','Pending')
        ->with('doneby','doctor','patient')
        ->get();

    }
    return response()->json(['message' => 'UnAuthorized User'],401);

}



public function fetchallfollowupsession($dr){

    if (
        Auth::user()->roles->first()->name ==
        ('Admin' || 'Employee'||'superAdmin')
    ) {
return Session::
 where('Hospital_Id','=',auth()->user()->Hospital_Id)
->where('Doctor_Id','=',$dr)
->where('type','=','followUp')
->where('Status','=','Pending')
->with('doneby','doctor','patient')
->get();

}
return response()->json(['message' => 'UnAuthorized User'],401);

}

public function fetcharchivesessions(){
    if (
        Auth::user()->roles->first()->name ==
        ('Admin' || 'Employee'||'superAdmin')
    ) {

        return Session::
        where('Hospital_Id','=',auth()->user()->Hospital_Id)
       ->where('Status','=','Completed')
       ->with('doneby','doctor','patient')
       ->get();

    }
    return response()->json(['message' => 'UnAuthorized User'],401);
}


public function fetchonesessionbyid($sessionId){
    if (
        Auth::user()->roles->first()->name ==
        ('Admin' || 'Employee'||'superAdmin' || 'Patient')
    ) {
$session = Session::where('id','=',$sessionId)->first();
$session->patient = Patient::where('id',$session->Patient_Id)->first();
$session->insurance = Insurance::where('id',$session->Insurance_Id)->first();
$session->doctor = User::where('id',$session->Doctor_Id)->first();
$session->doneby = $session->doneby()->first();
// $session->notes = Note::where();
$session->Contactnote = Contactnote::where('Session_Id',$sessionId)->first();
$session->Consulationnote =Consulationnote::where('Session_Id',$sessionId)->first();
$session->Pintakenote =Pintakenote::where('Session_Id',$sessionId)->first();
$session->PtreatmentPlan = PtreatmentPlan::where('Session_Id',$sessionId)->first();
$session->Terminationnote =Terminationnote::where('Session_Id',$sessionId)->first();
$session->Missedappointmentnote = Missedappointmentnote::where('Session_Id',$sessionId)->first();
$session->Miscnote = Miscnote::where('Session_Id',$sessionId)->first();
$session->Processnote= Processnote::where('Session_Id',$sessionId)->first();
$session->Progresssnote = Progresssnote::where('Session_Id',$sessionId)->first();
// fetchonesessionbyid
return response()->json($session);
        // return Session::where('id','=',$sessionId)->with(['patient','doneby','insurance','doctor'])->get();
    }
    return response()->json(['message' => 'UnAuthorized User'],401);



}



}

