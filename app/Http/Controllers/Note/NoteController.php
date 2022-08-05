<?php

namespace App\Http\Controllers\Note;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Treatmentstrategy;
use Validator;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('jwt.verify');
    }

    public function createtreatmentstrategy(Request $request)
    {
        if (Auth::check()) {
            //Validate User Inputs
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:treatmentstrategy',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }


            $treatments=new Treatmentstrategy();
            $treatments->name = $request['name'];
            $treatments->Hospital_Id = auth()->user()->Hospital_Id;
            $treatments->CreatedBy_Id =auth()->user()->id;
            $treatments->Status = 'Active';
            $treatments->save();

            return response()->json(['message' => 'Successfully Created new Treatment Strategy '], 200);

        }
        return response()->json(['message' => 'Unauthorized'], 401);

    }

    public function fetchreatmentstrategy(){

        if (Auth::check()) {

            return response()->json(['data' =>
            Treatmentstrategy::where('Hospital_Id','=',auth()->user()->Hospital_Id)
            // ->with(['createdby'])
            ->get()

            ], 200);

        }
        return response()->json(['message' => 'Unauthorized'], 401);

    }


}
