<?php

namespace App\Http\Controllers;

use App\Models\Slot;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    //

    public function setclinicianavailability(Request $request){

        if (Auth::check()) {


           //Validate User Inputs
           $validator = Validator::make($request->all(), [
            'start' => 'required',
            'end' => 'required',
            'day' => 'required',
        ]);
        if ($validator->fails()) {
          return response()->json(
                ['errors' => implode($validator->errors()->all())],
                422
            );
        }

        $slot=new Slot();
        $slot->start=$request['start'];
        $slot->end=$request['end'];
        $slot->day=$request['day'];
        $slot->User_Id=auth()->user()->id;
        $slot->Hospital_Id=auth()->user()->Hospital_Id;
        $slot->Createdby_Id=auth()->user()->id;
         $slot->save();


        return response()->json(['message' => 'Time Availability slot is saved successfully'], 201);

    }    return response()->json(['message' => 'Unauthorized user'], 401);


    }


    public function getclinicianslotavailability($Doctor_Id){
        return response()->json(['data' => Slot::where('Hospital_Id','=',auth()->user()->Hospital_Id)
        ->where('User_Id','=',$Doctor_Id)
        ->with('owner','doneby')
        ->orderBy('start', 'asc')
        ->get()
        ], 200);

    }

}
