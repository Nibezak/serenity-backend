<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Terminationnote extends Model
{

    use HasFactory, Notifiable;

    protected $table = 'terminationnotes';
    protected $fillable = [
        'Note_Type',
        'Patient_Id',
        'Hospital_Id',
        'CreatedBy_Id',
        'Visibility',
        'Status',
        'Appointment_Id',
        'Doctor_id',
        'Reason',
        'ChiefComplain',
        'Diagnosis',
        'diagnostic_justification',
        'TreatmentModality',
        'Outcome',
        'Session_Id',

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
