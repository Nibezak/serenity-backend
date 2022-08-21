<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\OTP;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use Validator;
use App\Models\Hospital;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\TransferSms;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth:api', ['except' => ['login', 'register']]);
    // }
    public function __construct()
    {
        $this->middleware('jwt.verify', [
            'except' => [
                'login',
                'register',
                'getPasswordToken',
                'updatePassword',
            ],
        ]);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        //Validate User Inputs
        $validator = Validator::make($request->all(), [
            'TypeOrganization' => 'required',
            'PracticeName' => 'required|string|unique:hospital',
            'BusinessPhone' => 'required|string|between:2,20',
            'BusinessEmail' => 'required|string|between:2,20|unique:hospital',
            'FirstName' => 'required|string|between:2,20',
            'LastName' => 'required|string|between:2,20',
            'Telephone' => 'required|string|between:9,14',
            'email' => 'required|string|unique:users',
        ]);
        if ($validator->fails()) {
            // return response()->json($validator->errors()->toJson(), 400);

            return response()->json(
                ['errors' => implode($validator->errors()->all())],
                422
            );
        }
        //Generate default Password
        $defaultManagerPswd = Str::random(10);

        $hospital = new Hospital();
        $hospital->TypeOrganization = $request['TypeOrganization'];
        $hospital->PracticeName = $request['PracticeName'];
        $hospital->BusinessPhone = $request['BusinessPhone'];
        $hospital->BusinessEmail = $request['BusinessEmail'];
        $hospital->Province = $request['Province'];
        $hospital->District = $request['District'];
        $hospital->Sector = $request['Sector'];
        $hospital->Cell = $request['Cell'];
        $hospital->Village = $request['Village'];
        $hospital->TinNumber = 'null';
        $hospital->logo = 'null';
        $hospital->save();

        $role = Role::find('110');

        if ($role) {
            $user = new User();
            $user->Role_id = '110';
            $user->hospital_id = $hospital->id;
            $user->FirstName = $request['FirstName'];
            $user->LastName = $request['LastName'];
            $user->Email = $request['email'];
            $user->Telephone = $request['Telephone'];
            $user->gender = $request['gender'];
            $user->ProfileImageUrl = 'https://i.imgur.com/BKB2EQi.png';
            $user->Address = null;
            $user->LicenseNumber = null;
            $user->Title = $request['Title'];
            $user->password = bcrypt($defaultManagerPswd);
            $user->LastLoginDate = date('Y-m-d H:i:s');
            $user->JoinDate = date('Y-m-d H:i:s');
            $user->IsActive = '1';
            $user->IsNotLocked = '1';
            $user->IsAccountNonExpired = '1';
            $user->IsAccountNonLocked = '1';
            $user->IsCredentialsNonExpired = '1';
            $user->session='null';
            $user->save();

            $user->attachRole($role);
        }

        $message =
            'Hello  ' .
            $request['FirstName'] .
            ' - Your ' .
            $request['PracticeName'] .
            '\'s Account credentials are email ' .
            $request['email'] .
            ' and Password is ' .
            $defaultManagerPswd;
        $sms = new TransferSms();
        $sms->sendSMS($request['Telephone'], $message);

        return response()->json(
            [
                'message' =>
                    $request['PracticeName'] .
                    ' Hospital Account successfully created, Please Check your Admin email ' .
                    $request['email'] .
                    ' or Your Phone number ' .
                    $request['Telephone'] .
                    ' For login credentials of your Hospital',
                // 'user' => $user,
                'pswd-dvt-purpose-only' => $defaultManagerPswd,
            ],
            200
        );
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
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
            if (Auth::user()->roles->first()->name == 'Admin') {
                if (
                    User::select('IsAccountNonLocked')
                        ->where('email', '=', $request['email'])
                        ->value('IsAccountNonLocked') != 'VerifiedBy_Phone'
                ) {

                    DB::Table('users')
                    ->where('email', '=', $request['email'])
                    ->update([
                        'session' => 'true',
                    ]);
                    //Get messsage receiver telephone
                    $receiverPhone = User::select('telephone')
                        ->where('email', '=', $request['email'])
                        ->value('telephone');

                    //Generate Random OTP CODE & send it to the user

                    $otp_code = mt_rand(100000, 999999);
                    $message = 'Your LetsReason Login OTP is ' . $otp_code;
                    $sms = new TransferSms();
                    //$sms->sendSMS($receiverPhone,$message);

                    // save Otp
                    $record = OTP::where(['email' => $request['email']]);
                    if ($record->exists()) {
                        $record->delete();
                    }
                    OTP::create([
                        'code' => $otp_code,
                        'date' => date('Y-m-d H:i:s'),
                        'status' => 'Active',
                        'email' => $request['email'],
                    ]);

                    return response()->json(
                        [
                            'errors' =>
                                'Your account is not verified please First Check your email address or Phone number to verify your account !!!'

                        ],
                        401
                    );
                }



                $MinuteCounter = 60 - date('i', time());

                if($MinuteCounter == 0){
                    DB::Table('users')
                    ->where('email', '=', $request['email'])
                    ->update([
                        'session' => 'false',
                    ]);

                }

                //get hospital name of loggedin User
                $HospitalnameLoggedin = Hospital::select('PracticeName')
                    ->where('id', '=', Auth::user()->Hospital_Id)
                    ->value('PracticeName');

                return response()->json(
                    [
                        'message' =>
                            'Welcome ' .
                            Auth::user()->roles->first()->display_name .
                            ' of ' .
                            $HospitalnameLoggedin,
                        'token' => $checkauth,
                        'user' => Auth::user(),

                    ],
                    200
                );


            } else {
                return response()->json(
                    ['message' => 'UnAuthorized User'],
                    401
                );
            }
        } else {
            return response()->json(['errors' => 'Invalid Credentials'], 401);
        }

        //  return $this->createNewToken($token);
    }

    /**
     * reset user password.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPasswordToken(Request $request)
    {
        //Validate inputs
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
        ]);

        if ($validator->fails()) {
            // return response()->json($validator->errors(), 401);

            return response()->json(
                ['errors' => implode($validator->errors()->all())],
                422
            );
        }

        $token = Str::random(124);

        $record = DB::table('password_resets')->where([
            'email' => $request['email'],
        ]);
        if ($record->exists()) {
            $record->delete();
        }
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        return response()->json(
            [
                'message' =>
                    'We have E-mailed your password reset link! ' . $token,
            ],
            200
        );
    }

    /**
     * update reset password
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(Request $request, $token)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'min:6',
            'password_confirmation' =>
                'required_with:password|same:password|min:6',
        ]);

        if ($validator->fails()) {
            // return response()->json($validator->errors(), 422);

            return response()->json(
                ['errors' => implode($validator->errors()->all())],
                422
            );
        }

        $updatePassword = DB::table('password_resets')
            ->where([
                'token' => $token,
            ])
            ->first();

        if (!$updatePassword) {
            return response()->json(
                ['mesage' => 'Invalid Email Reset token!'],
                422
            );
        }

        $emaili = DB::table('password_resets')
            ->select('email')
            ->where(['token' => $token])
            ->value('email');

        $user = User::where('email', $emaili)->update([
            'password' => bcrypt($request['password']),
        ]);

        DB::table('password_resets')
            ->where(['email' => $emaili])
            ->delete();

        return response()->json([
            'message' => 'Your password has been changed!',
        ]);
    }

    /**
     * Change User Password.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changepassword(Request $request)
    {
        if (Auth::Check()) {
            $validator = Validator::make($request->all(), [
                'password' => 'required',
                'NewPassword' => 'required',
                'password_confirmation' =>
                'required_with:password|same:NewPassword|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json(
                    ['errors' => implode($validator->errors()->all())],
                    422
                );
            }

            if (Hash::check($request['password'], auth()->user()->password)) {
                // Right password
                $user = User::where('email', auth()->user()->email)->update([
                    'password' => bcrypt($request['NewPassword']),
                ]);

                return response()->json(
                    ['message' => 'Your password has been changed!'],
                    200
                );
            } else {
                // Wrong one
                return response()->json(
                    ['message' => 'You have entered wrong old Password!'],
                    401
                );
            }
        }
        return response()->json(['message' => 'Unauthenticated!'], 401);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        if (Auth::Check()) {
            auth()->logout();
            return response()->json([
                'message' => 'User successfully signed out',
            ]);
        }
        return response()->json(['message' => 'Unauthenticated!'], 401);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return response()->json(
            ['data' => $this->createNewToken(auth()->refresh())],
            201
        );

        // return $this->createNewToken(auth()->refresh());
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return response()->json(auth()->user());
        // return response()->json(
        //     [
        //         'data' => auth()
        //             ->user()
        //             ->ignore(Auth::id()),
        //         'Acount_Created_At' => auth()
        //             ->user()
        //             ->created_at->diffForHumans(),
        //     ],
        //     200
        // );
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' =>
                auth()
                    ->factory()
                    ->getTTL() * 6000,
            'user' => auth()->user(),
        ]);
    }

    /**
     * validate the Login OTP.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function validateotp(Request $request)
    {

        if (Auth::check()) {
        //validate inputs
        $validator = Validator::make($request->all(), [
            'code' => 'required|exists:otp',
        ]);
        if ($validator->fails()) {


            return response()->json(
                ['errors' => implode($validator->errors()->all())],
                422
            );
        }




        //compare input with code value from the table
        $CodeDb = OTP::select('code')
            ->where(['email' => auth()->user()->email])
            ->value('code');


        if (strcasecmp($CodeDb, $request['code']) == 0) {
            DB::Table('users')
                ->where('email', '=', auth()->user()->email)
                ->where('Hospital_Id', '=', auth()->user()->Hospital_Id)
                ->update([
                    'IsAccountNonLocked' => 'VerifiedBy_Phone',
                    'email_verified_at' =>  \Carbon\Carbon::now()->toDateTimeString(),
                ]);

            return response()->json(
                ['message' => 'Your account has been verified Successfully'],
                200
            );
        }
        return response()->json(['message' => 'Invalid OTP Code'], 401);



    }
    return response()->json(['message' => 'Unauthorized'], 401);

    }

    /**
     * send the Login OTP.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendotp(Request $request)
    {
        if (Auth::check()) {
            //validate inputs

            $validator = Validator::make($request->all(), [
                'email' => 'required|exists:users',
            ]);
            if ($validator->fails()) {
                // return response()->json($validator->errors()->toJson(), 400);

                return response()->json(
                    ['errors' => implode($validator->errors()->all())],
                    422
                );
            }

            if (
                User::select('IsAccountNonLocked')
                    ->where('email', '=', $request['email'])
                    ->value('IsAccountNonLocked') != 'VerifiedBy_Phone'
            ) {
                //Get messsage receiver telephone
                $receiverPhone = User::select('telephone')
                    ->where('email', '=', $request['email'])
                    ->value('telephone');

                //Generate Random OTP CODE & send it to the user for verification

                $otp_code = mt_rand(100000, 999999);
                $message = 'Your LetsReason Login OTP is ' . $otp_code;
                $sms = new TransferSms();
                $sms->sendSMS($receiverPhone,$message);

                // save Otp
                $record = OTP::where(['email' => $request['email']]);
                if ($record->exists()) {
                    $record->delete();
                }
                OTP::create([
                    'code' => $otp_code,
                    'date' => date('Y-m-d H:i:s'),
                    'status' => 'Active',
                    'email' => $request['email'],
                ]);

                return response()->json(
                    [
                        'message' =>
                            'Please check your phone inbox for Letsreason Login OTP !!! ' .
                            $otp_code,
                    ],
                    201
                );
            }
            return response()->json(
                [
                    'message' => 'Your account is verified already !!!',
                ],
                201
            );
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }
}
