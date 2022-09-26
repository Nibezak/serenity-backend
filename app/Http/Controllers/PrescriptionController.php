<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Diagnosis;
use App\Models\User;
use App\Models\Drug;
use App\Models\Patient;
use App\Models\Prescription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
class PrescriptionController extends Controller

{



    public function __construct()
    {
        $this->middleware('jwt.verify', [
            'except' => [

            ],
        ]);
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
     * create new prescription
     *
     * @return \Illuminate\Http\Response
     */
    public function saveprescription( Request $request)
    {
        //

        if (Auth::user()->roles->first()->name == 'Admin') {
            //validate inputs
            $validator = Validator::make($request->all(), [
                'Patient_Id' => 'required|exists:patients,id',
                'DrugId'=> 'required|exists:drugs,id',
                'MedicalAdvices'=>'required',
                'Description'=>'required',
            ]);
            if ($validator->fails()) {
                // return response()->json($validator->errors()->toJson(), 400);

                return response()->json(
                    ['errors' => implode($validator->errors()->all())],
                    422
                );
            }

            $assignedDr = Patient::select('AssignedDoctor_Id')
            ->where('Hospital_Id', '=', auth()->user()->Hospital_Id)
            ->where('id', '=', $request['Patient_Id'])
            ->value('AssignedDoctor_Id');


           $presc=new Prescription();

           $presc->Patient_Id=$request['Patient_Id'];
           $presc->Hospital_Id=auth()->user()->Hospital_Id;
           $presc->Doctor_Id=$assignedDr;
           $presc->Diagnosis=json_encode($request['Diagnosis']);
           $presc->Drug_Id=$request['DrugId'];
           $presc->Medical_Advices=$request['MedicalAdvices'];
           $presc->Description=$request['Description'];
           $presc->RecordedBy_Id=auth()->user()->id;
           $presc->save();
           $patinfo = Patient::select('MobilePhone','FirstName','LastName')->find($request['Patient_Id']);

           return response()->json(
            [
                'message' =>'Sucessfully created new prescription of '.$patinfo['FirstName'].' '.$patinfo['LastName'] . ' Contact [ '.$patinfo['MobilePhone'].' ]',
            ],
            201
        );



        }
        return response()->json(['message' => 'Unauthorized user'], 401);



    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function getalldrugs(){
        return response()->json(
            [
                'drugs' =>
                Drug::all(),
            ],
            200
        );
    }


}