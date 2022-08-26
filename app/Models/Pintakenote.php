<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Pintakenote extends Model
{
    use HasFactory, Notifiable;


    protected $table = 'pintakenote';
    protected $fillable = [

        'Note_Type',
        'Patient_Id',
        'Hospital_Id',
        'CreatedBy_Id',
        'Visibility',
        'Status',
        'Appointment_Id',
        'Doctor_Id',
        'Signator_Id',
        'RiskAssessment',
        'PresentingProblem',
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
        'ObjectiveContent',
        'Identification',
        'HistoryOfPresentProblem',
        'PsychiatricHistory',
        'TraumaHistory',
        'FamilyPsychiatricHistory',
        'MedicalConditionsHistory',
        'CurrentMedications',
        'SubstanceUse',
        'FamilyHistory',
        'SocialHistory',
        'SpiritualFactors',
        'DevelopmentalHistory',
        'EducationalVocationalHistory',
        'LegalHistory',
        'Snap',
        'OtherImportantInformation',
        'Plan',
        'Diagnosis',
        'DiagnosticJustification',
        'DateTimeScheduled',
        'DateTimeOccured',

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
            return $this->belongsTo('App\Models\User','Doctor_Id');
        }

        public function signator()
        {
            return $this->belongsTo('App\Models\User','Signator_Id');
        }

        public function patient()
        {
            return $this->belongsTo('App\Models\Patient','Patient_Id');
        }


}
