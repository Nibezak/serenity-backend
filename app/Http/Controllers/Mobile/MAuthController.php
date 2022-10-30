<?php

namespace App\Http\Controllers\Mobile;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\TransferSms;

class MAuthController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('jwt.verify', [
            'except' => ['savepatient', 'login'],
        ]);
    }

    public function login(Request $request)
    {
        if ($request->has('fingerprintId')) {
            $record = User::where([
                'fingerprintId' => $request['fingerprintId'],
            ]);
            if (!$record->exists()) {
                return response()->json(
                    [
                        'message' => 'Invalid Fingerprint.',
                    ],
                    200
                );
            }
            $user = User::where(
                'fingerprintId',
                '=',
                $request['fingerprintId']
            )->get();




             $auth = Auth::attempt([
                'email' => $user[0]['email'],
                'password' => $user[0]['password'],
            ]);



            if ($auth) {
                return response()->json(
                    [
                        'message' => 'Patient is logged in Successfully.',
                        'User_Role' => Auth::user()->roles->first()
                            ->display_name,
                        'token' => $auth,
                        'user' => Auth::user(),
                    ],
                    200
                );
            }
        }
        //Validate inputs
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                ['errors' => implode($validator->errors()->all())],
                422
            );
        }

        $checkauth = Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if ($checkauth) {
            return response()->json(
                [
                    'message' => 'Patient is logged in Successfully.',
                    'User_Role' => Auth::user()->roles->first()->display_name,
                    'token' => $checkauth,
                    'user' => Auth::user(),
                ],
                200
            );
        } else {
            return response()->json(['message' => 'Invalid Credentials'], 200);
        }
    }

    public function savepatient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|unique:users',
            'phoneNumber' => 'required',
            'password'=> 'required',
            'fingerprintId' => 'required|unique:users',
        ]);
        if ($validator->fails()) {
            return response()->json(
                ['message' => implode($validator->errors()->all())],
                200
            );
        }

        $role = Role::find(117);

        if ($role) {
            $user = new User();
            $user->Role_id = 117;
            $user->FirstName = $request['firstName'];
            $user->LastName = $request['lastName'];
            $user->Email = $request['email'];
            $user->Telephone = $request['phoneNumber'];
            $user->Title = '';
            $user->LastLoginDate = date('Y-m-d H:i:s');
            $user->JoinDate = date('Y-m-d H:i:s');
            $user->IsActive = '1';
            $user->IsNotLocked = '1';
            $user->IsAccountNonExpired = '1';
            $user->IsAccountNonLocked = '1';
            $user->session = '1';
            $user->IsCredentialsNonExpired = '1';
            $user->fingerprintId = $request['fingerprintId'];
            $user->password = bcrypt($request['password']);
            $user->created_from = 'mobile';

            $user->save();

            $user->attachRole($role);

            $patient = new Patient();
            $patient->FirstName = $request['firstName'];
            $patient->LastName = $request['lastName'];
            $patient->MobilePhone = $request['phoneNumber'];
            $patient->email = $request['email'];
            $patient->Dob = '';
            $patient->Province = '';
            $patient->District = '';
            $patient->Sector = '';
            $patient->Cell = '';
            $patient->Village = '';
            $patient->gender = '';
            $patient->PatientCode='P-' . Str::random(8);
            $patient->Createdby_Id=$user->id;
            $patient->save();
        }

    if($patient){
        $message="Hello ".$request['firstName'].", Your Account has been created successfully, Your Credentials are ".$request['email'].' and Password is '.$request['password'];
        $sms = new TransferSms();
        $sms->sendSMS($request['telephone'], $message);
        return response()->json([
            'message' => 'Your Patient account has been created successfully'

        ],201);

    }else{
        return response()->json([
            'message' => 'Sorry, Patient account has not been created. issues are on our side. we are fixing it ASAP'

        ],200);
    }
    }
}
