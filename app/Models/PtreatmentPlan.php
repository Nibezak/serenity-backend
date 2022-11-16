<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class PtreatmentPlan extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'ptreatmentplan';
    protected $fillable = [

        'Note_Type',
        'Diagnosis_Id',
        'Diagnositic_Justification',
        'Presenting_Problem',
        'Treatment_Goals',
        'Objective_Id',
        'Frequency_Treatment_Id',
        'Patient_Id',
        'Hospital_Id',
        'CreatedBy_Id',
        'Status',
        'Signator_Id',
        'Date',
        'Time',
        'Doctor_id',
        'Treatmentstrategy_Id'


        ];


        // public function diagnsosis()
        // {
        //     return $this->belongsToMany('App\Models\Diagnosis','Diagnosis_Id');
        // }


        // public function treatmentstartegy()
        // {
        //     return $this->belongsToMany('App\Models\Treatmentstrategy','Treatmentstrategy_Id');
        // }


        // public function objective()
        // {
        //     return $this->hasMany('App\Models\NoteObjective','Objective_Id');
        // }

        public function hospital()
        {
            return $this->belongsTo('App\Models\Hospital','Hospital_Id');
        }
        public function doctor()
        {
            return $this->belongsTo('App\Models\User','Doctor_id');
        }
        public function doneby()
        {
            return $this->belongsTo('App\Models\User','CreatedBy_Id');
        }
        public function signator()
        {
            return $this->belongsTo('App\Models\User','Signator_Id');
        }

        // public function frequency_treatment()
        // {
        //     return $this->belongsToMany('App\Models\Frequencytreatment','Frequency_Treatment_Id');
        // }

        public function patient()
    {
        return $this->belongsTo('App\Models\Patient','Patient_Id');
    }


}
