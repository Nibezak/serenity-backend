<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
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
            return response()->json($validator->errors(), 422);
        }

        $defaultManagerPswd = Str::random(10);

        $role = new Role();
        $role->name = $request['RoleId'];
        $role->display_name = Role::select('name')
            ->where('roles.id', '=', $request['RoleId'])
            ->value('name'); // optional
        $role->description = null; // optional
        $role->save();

        $user = new User();
        $user->Role_id = $role->id;
        $user->FirstName = $request['FirstName'];
        $user->LastName = $request['LastName'];
        $user->Email = $request['email'];
        $user->Telephone = $request['telephone'];
        $user->gender = $request['gender'];
        $user->ProfileImageUrl = null;
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
        $user->save();

        $user->attachRole($role);

        $hospitalname = Hospital::select('PracticeName')
            ->where('MangerId', '=', auth()->user()->id)
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
        //$sms->sendSMS($request['telephone'],$message);

        return response()->json(
            [
                'message' =>
                    $request['FirstName'] .
                    ' ' .
                    $request['LastName'] .
                    ' Account is successfully created ,Check your email address or Phone number for the Login credentials',
                // 'user' => $user
                'test' => $message,
            ],
            200
        );
    }

    //Get all our hospital staff
    public function fetchourstaff()
    {
        if (!Auth::user()->roles->first()->name == 'Admin') {
            return true;
        }

         return response()->json(['data'=>User::select('FirstName', 'LastName', 'telephone')
         ->where('Role_id','=',Auth::user()->roles->first()->id)
         ->get()], 200);


    }

    //Fetch hospitall staff roles
    public function retrieveRoles()
    {
        if (Auth::user()->roles->first()->name == 'Admin') {
            $myRoleId = json_decode(Auth::user()->roles->first()['id'], true);

            return response()->json(['data'=>Role::select('id','display_name')->get()->except($myRoleId)], 200);
        }
    }
}
