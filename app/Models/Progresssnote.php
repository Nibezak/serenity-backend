<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Progresssnote extends Model
{
    use HasFactory, Notifiable;


    protected $table = 'progressnotes';
    protected $fillable = [

        'Note_Type',
        'Patient_Id',
        'Hospital_Id',
        'CreatedBy_Id',
        'Visibility',
        'Status',
        'Appointment_Id',
        'Doctor_id',
        'RiskAssessment',
        'Orientation',
        'GeneralAppearance',
        'Dress',
        'MotorActivity',
        'InterviewBehavior',
        'Speech',
        'Mood',
        'Affect',
        'Insight',
        'Judgement',
        'Memory',
        'Attention',
        'ThoughtProcess',
        'ThoughtContent',
        'Perception',
        'FunctionalStatus',
        'Diagnosis',
        'DiagnosticJustification',
        'Medications',
        'SymptomDescription',
        'ObjectiveContent',
        'InterventionsUsed',
        'ObjectiveProgress',
        'AdditionalNotes',
        'Plan',
        'Recommendation',
        'PrescribedFrequencyTreatment',

        ];


        public function hospital()
        {
            return $this->belongsTo('App\Models\Hospital','Hospital_Id');
        }

        public function appointment()
        {
            return $this->belongsTo('App\Models\Appointment','Appointment_Id');
        }
        public function doctor()
        {
            return $this->belongsTo('App\Models\User','Doctor_id');
        }

        public function doneby()
        {
            return $this->belongsTo('App\Models\User','CreatedBy_Id');
        }




        public function patient()
        {
            return $this->belongsTo('App\Models\Patient','Patient_Id');
        }


}
