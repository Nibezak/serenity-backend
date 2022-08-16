<?php

namespace App\Http\Controllers\Note;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Treatmentstrategy;
use App\Models\Frequencytreatment;
use App\Models\PtreatmentPlan;
use App\Models\NoteObjective;
use App\Models\Miscnote;
use App\Models\Processnote;
use App\Models\Consulationnote;
use App\Models\Contactnote;
use Carbon\Carbon;
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
                // return response()->json($validator->errors()->toJson(), 400);

                return response()->json(
                    ['errors' => implode($validator->errors()->all())],
                    422
                );
            }

            $treatments = new Treatmentstrategy();
            $treatments->name = $request['name'];
            $treatments->Hospital_Id = auth()->user()->Hospital_Id;
            $treatments->CreatedBy_Id = auth()->user()->id;
            $treatments->Status = 'Active';
            $treatments->save();

            return response()->json(
                ['message' => 'Successfully Created new Treatment Strategy '],
                200
            );
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function fetchreatmentstrategy()
    {
        if (Auth::check()) {
            return response()->json(
                [
                    'data' => Treatmentstrategy::where(
                        'Hospital_Id',
                        '=',
                        auth()->user()->Hospital_Id
                    )
                        ->with(['createdby:id,FirstName,LastName'])
                        ->get(),
                ],
                200
            );
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function createfrequencytreatment(Request $request)
    {
        if (Auth::check()) {
            //Validate User Inputs
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:frequencytreatment',
            ]);
            if ($validator->fails()) {
                // return response()->json($validator->errors()->toJson(), 400);

                return response()->json(
                    ['errors' => implode($validator->errors()->all())],
                    422
                );
            }

            $frequencytr = new Frequencytreatment();
            $frequencytr->name = $request['name'];
            $frequencytr->Hospital_Id = auth()->user()->Hospital_Id;
            $frequencytr->CreatedBy_Id = auth()->user()->id;
            $frequencytr->Status = 'Active';
            $frequencytr->save();

            return response()->json(
                ['message' => 'Successfully Created new Frequency Treatment '],
                200
            );
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    public function fetchfrequencytreatment()
    {
        if (Auth::check()) {
            return response()->json(
                [
                    'data' => Frequencytreatment::where(
                        'Hospital_Id',
                        '=',
                        auth()->user()->Hospital_Id
                    )
                        ->with(['createdby:id,FirstName,LastName'])
                        ->get(),
                ],
                200
            );
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function addptreatmentplan(Request $request)
    {
        if (Auth::check()) {
            //Validate User Inputs
            $validator = Validator::make($request->all(), [
                'Note_Type' => 'required',
                'Diagnosis_Id' => 'required|array',
                'Presenting_Problem' => 'required',
                'Treatment_Goals' => 'required',
                'Frequency_Treatment_Id' => 'required|array',
                'Patient_Id' => 'required',
                'Signator_Id' => 'required',
                'Doctor_id' => 'required',
                'Treatmentstrategy_Id' => 'required|array',
                'Objective_content' => 'required',
                'EstimatedCompletion' => 'required',
                'Diagnositic_Justification' => 'required',
            ]);
            if ($validator->fails()) {
                // return response()->json($validator->errors()->toJson(), 400);

                return response()->json(
                    ['errors' => implode($validator->errors()->all())],
                    422
                );
            }

            //create note objective
            $note_Obj = new NoteObjective();
            $note_Obj->content = $request['Objective_content'];
            $note_Obj->Patient_Id = $request['Patient_Id'];
            $note_Obj->Hospital_Id = auth()->user()->Hospital_Id;
            $note_Obj->CreatedBy_Id = auth()->user()->id;
            $note_Obj->Status = 'Active';
            $note_Obj->Notetype = $request['Note_Type'];
            $note_Obj->EstimatedComplation = $request['EstimatedCompletion'];
            $note_Obj->TreatmentStartegyID = json_encode(
                $request['Treatmentstrategy_Id']
            );

            $note_Obj->save();

            $ptreatmentplan = new PtreatmentPlan();
            $ptreatmentplan->Note_type = $request['Note_Type'];
            $ptreatmentplan->Diagnositic_Justification =
                $request['Diagnositic_Justification'];
            $ptreatmentplan->Diagnosis_Id = json_encode(
                $request['Diagnosis_Id']
            );

            $ptreatmentplan->Presenting_Problem =
                $request['Presenting_Problem'];
            $ptreatmentplan->Treatment_Goals = $request['Treatment_Goals'];
            $ptreatmentplan->Objective_Id = json_encode($note_Obj->id);
            $ptreatmentplan->Frequency_Treatment_Id = json_encode(
                $request['Treatmentstrategy_Id']
            );
            $ptreatmentplan->Patient_Id = $request['Patient_Id'];
            $ptreatmentplan->Hospital_Id = auth()->user()->Hospital_Id;
            $ptreatmentplan->CreatedBy_Id = auth()->user()->id;
            $ptreatmentplan->Status = 'Active';
            $ptreatmentplan->Signator_Id = $request['Signator_Id'];
            $ptreatmentplan->Date = Carbon::now()->format('d/m/Y');
            $ptreatmentplan->Time = Carbon::now()->format('H:i:m');
            $ptreatmentplan->Doctor_id = $request['Doctor_id'];
            $ptreatmentplan->Treatmentstrategy_Id = json_encode(
                $request['Treatmentstrategy_Id']
            );
            $ptreatmentplan->save();

            return response()->json(
                [
                    'message' =>
                        'Successfully Created new ' . $request['Note_Type'],
                ],
                200
            );
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function createmiscellaneousnote(Request $request)
    {
        if (Auth::check()) {
            //Validate User Inputs
            $validator = Validator::make($request->all(), [
                'Note_Type' => 'required',
                'PatientId' => 'required',
                'SignatorId' => 'required',
                'DoctorId' => 'required|',
                'AppointmentID' => 'required',
                'DateTime' => 'required',
                'NoteContent' => 'required',
                'Visibility' => 'required',
            ]);
            if ($validator->fails()) {
                // return response()->json($validator->errors()->toJson(), 400);

                return response()->json(
                    ['errors' => implode($validator->errors()->all())],
                    422
                );
            }

            $miscnote = new Miscnote();
            $miscnote->Note_Type = $request['Note_Type'];
            $miscnote->Hospital_Id = auth()->user()->Hospital_Id;
            $miscnote->Patient_Id = $request['PatientId'];
            $miscnote->Doctor_Id = $request['DoctorId'];
            $miscnote->DateTime = $request['DateTime'];
            $miscnote->NoteContent = $request['NoteContent'];
            $miscnote->Signator_Id = $request['SignatorId'];
            $miscnote->Visibility = $request['Visibility'];
            $miscnote->Status = 'Active';
            $miscnote->CreatedBy_Id = auth()->user()->id;
            $miscnote->Appoint_Id = $request['AppointmentID'];
            $miscnote->save();
            return response()->json(
                ['message' => 'Successfully created ' . $request['Note_Type']],
                200
            );
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function fetchmiscnote(Request $request)
    {
        if (Auth::check()) {
            //Validate User Inputs
            $validator = Validator::make($request->all(), [
                'Patient_Id' => 'required|exists:miscnote',
            ]);
            if ($validator->fails()) {
                // return response()->json($validator->errors()->toJson(), 400);

                return response()->json(
                    ['errors' => implode($validator->errors()->all())],
                    422
                );
            }

            return response()->json(
                [
                    'data' => Miscnote::where(
                        'Hospital_Id',
                        '=',
                        auth()->user()->Hospital_Id
                    )
                        ->where('Patient_Id', '=', $request['Patient_Id'])
                        ->with([
                            'doctor:id,FirstName,LastName',
                            'signator:id,Title,Firstname,LastName',
                            'patient:id,FirstName,LastName,Dob,MobilePhone',
                        ])
                        ->get(),
                ],
                200
            );
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function createContactNote(Request $request)
    {
        if (Auth::check()) {
            //Validate User Inputs
            $validator = Validator::make($request->all(), [
                'Note_Type' => 'required',
                'PatientId' => 'required',
                'SignatorId' => 'required',
                'DoctorId' => 'required|',
                'DateTime' => 'required',
                'Visibility' => 'required',
                'Contact_name' => 'required',
                'Relationship_to_patient' => 'required',
                'Method_communication' => 'required',
                'Reason_communication' => 'required',
                'Time_spent' => 'required',
                'Communication_details' => 'required',
            ]);
            if ($validator->fails()) {
                // return response()->json($validator->errors()->toJson(), 400);

                return response()->json(
                    ['errors' => implode($validator->errors()->all())],
                    422
                );
            }

            $contactnote = new Contactnote();

            $contactnote->Note_Type = $request['Note_Type'];
            $contactnote->Hospital_Id = auth()->user()->Hospital_Id;
            $contactnote->Patient_Id = $request['PatientId'];
            $contactnote->Doctor_Id = $request['DoctorId'];
            $contactnote->DateTime = $request['DateTime'];
            $contactnote->ContactName = $request['Contact_name'];
            $contactnote->RelationshipToPatient =
                $request['Relationship_to_patient'];
            $contactnote->MethodCommunication =
                $request['Method_communication'];
            $contactnote->ReasonCommunication =
                $request['Reason_communication'];
            $contactnote->TimeSpent = $request['Time_spent'];
            $contactnote->CommunicationDetails =
                $request['Communication_details'];
            $contactnote->Signator_Id = $request['SignatorId'];
            $contactnote->Visibility = $request['Visibility'];
            $contactnote->Status = 'Active';
            $contactnote->CreatedBy_Id = auth()->user()->id;
            $contactnote->save();

            return response()->json(
                [
                    'message' =>
                        'Successfully created new ' . $request['Note_Type'],
                ],
                200
            );
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function viewContactNote(Request $request)
    {
        if (Auth::check()) {
            //Validate User Inputs
            $validator = Validator::make($request->all(), [
                'Patient_Id' => 'required|exists:contactnote',
            ]);
            if ($validator->fails()) {
                // return response()->json($validator->errors()->toJson(), 400);

                return response()->json(
                    ['errors' => implode($validator->errors()->all())],
                    422
                );
            }

            return response()->json(
                [
                    'data' => Contactnote::where(
                        'Hospital_Id',
                        '=',
                        auth()->user()->Hospital_Id
                    )
                        ->where('Patient_Id', '=', $request['Patient_Id'])
                        ->with([
                            'doctor:id,FirstName,LastName',
                            'signator:id,Title,Firstname,LastName',
                            'doneby:id,Title,FirstName,LastName',
                        ])
                        ->get(),
                ],
                200
            );
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function createProcessNote(Request $request)
    {
        if (Auth::check()) {
            //Validate User Inputs
            $validator = Validator::make($request->all(), [
                'NoteType' => 'required',
                'PatientId' => 'required',
                'DoctorId' => 'required',
                'AppointmentId' => 'required',
                'ProcessNote' => 'required',
                'Visibility' => 'required',
                'DateTime_Scheduled' => 'required',
                'DateTime_Occured' => 'required',
            ]);
            if ($validator->fails()) {
                // return response()->json($validator->errors()->toJson(), 400);

                return response()->json(
                    ['errors' => implode($validator->errors()->all())],
                    422
                );
            }

            $procnote = new Processnote();
            $procnote->Note_Type = $request['NoteType'];
            $procnote->Hospital_Id = auth()->user()->Hospital_Id;
            $procnote->Patient_Id = $request['PatientId'];
            $procnote->Doctor_Id = $request['DoctorId'];
            $procnote->DateTime_Scheduled = $request['DateTime_Scheduled'];
            $procnote->DateTime_Occured = $request['DateTime_Occured'];
            $procnote->Visibility = $request['Visibility'];
            $procnote->Status = 'Active';
            $procnote->CreatedBy_Id = auth()->user()->id;
            $procnote->Appointment_Id = $request['AppointmentId'];
            $procnote->ProcessNote = $request['ProcessNote'];
            $procnote->save();

            return response()->json(
                [
                    'message' =>
                        'Successfully created new ' . $request['NoteType'],
                ],
                200
            );
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function viewProcessNote(Request $request)
    {
        if (Auth::check()) {
            //Validate User Inputs
            $validator = Validator::make($request->all(), [
                'Patient_Id' => 'required|exists:processnote',
            ]);
            if ($validator->fails()) {
                // return response()->json($validator->errors()->toJson(), 400);

                return response()->json(
                    ['errors' => implode($validator->errors()->all())],
                    422
                );
            }

            return response()->json(
                [
                    'data' => Processnote::where(
                        'Hospital_Id',
                        '=',
                        auth()->user()->Hospital_Id
                    )
                        ->where('Patient_Id', '=', $request['Patient_Id'])
                        ->with([
                            'doctor:id,FirstName,LastName',
                            'doneby:id,Title,FirstName,LastName',
                        ])
                        ->get(),
                ],
                200
            );
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function createConsulationnote(Request $request)
    {
        if (Auth::check()) {
            //Validate User Inputs
            $validator = Validator::make($request->all(), [
                'Note_Type' => 'required',
                'Diagnosis_Id' => 'required|array',
                'AppointmentId' => 'required',
                'PatientID' => 'required',
                'DoctorID'=>'required',
                'DateTime_Scheduled' => 'required',
                'DateTime_Occured' => 'required',
                'Visibility' => 'required',
                'diagnostic_justification' => 'required',
                'Note_Content' => 'required',
                'Signator_Id'=>'required',
            ]);
            if ($validator->fails()) {
                // return response()->json($validator->errors()->toJson(), 400);

                return response()->json(
                    ['errors' => implode($validator->errors()->all())],
                    422
                );
            }

            $consnote = new Consulationnote();
            $consnote->Note_Type = $request['Note_Type'];
            $consnote-> Hospital_Id= auth()->user()->Hospital_Id;
            $consnote-> Patient_Id= $request['PatientID'];
            $consnote-> Doctor_Id= $request['DoctorID'];
            $consnote-> Appointment_Id= $request['AppointmentId'];
            $consnote-> DateTime_Scheduled= $request['DateTime_Scheduled'];
            $consnote-> DateTime_Occured= $request['DateTime_Occured'];
            $consnote->Visibility=$request['Visibility'];
            $consnote->Status='Active';
            $consnote->CreatedBy_Id=auth()->user()->id;
            $consnote->Diagnsosis_Id=json_encode(
                $request['Diagnosis_Id']
            );
            $consnote->diagnostic_justification=$request['diagnostic_justification'];
            $consnote->Note_Content=$request['Note_Content'];
            $consnote->Signator_Id=$request['Signator_Id'];
            $consnote->save();

            return response()->json(['message' => 'Successfully created a new '.$request['Note_Type']], 200);



        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }
}
