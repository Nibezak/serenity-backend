<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hospital;
use App\Models\Slot;
use App\Models\User;
use App\Models\Diagnosis;

class HospitalController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.verify');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function getHospitals(){

        $hospitals = Hospital::all();
        return response()->json(
            [
                'message'=>"success",
                'hospitals'=>$hospitals,
            ],200);

     }

// get available doctors
public function getAvailableDoctors(){
    $availability = [];
    $doctors = User::where('role_id',112)->get();

    foreach ($doctors as $key => $value) {
        $doctors[$key]->availability = Slot::where('User_Id',$value->id)->get();
    }
    return response()->json(
        [
            'message'=>"success",
            'doctors'=>$doctors,
        ],200);
}

// get mental disorders
public function getAllDisorders(){
    $diagnosis = Diagnosis::all();
    return response()->json(
        [
            'message'=>"success",
            'disorders'=>$diagnosis,
        ],200);

}
}
