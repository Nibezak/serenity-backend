<?php

namespace App\Http\Controllers\Super;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TypeOrg;

class SuperDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.verify', [
            'except' => [
                'gettypesOrg',
            ],
        ]);
    }
    //
//fecth types of organizations
public function gettypesOrg(){

    return response()->json(['data'=>TypeOrg::all()], 200);
}

}
