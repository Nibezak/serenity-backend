<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Appointment extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'appointments';
    protected $fillable = [

    'AppointmentType_Id',
    'Patient_Id',
    'Doctor_Id',
    'Location',
    'ScheduledTime',
    'Duration',
    'Frequency',
    'AppointmentAlert',
    'CreatedBy_Id',
    'Hospital_Id',
    'Status',
    'link',


    ];

    

    public function appointmenttype()
    {
        return $this->belongsTo('App\Models\TypeAppointment','AppointmentType_Id');
    }
    public function hospital()
    {
        return $this->belongsTo('App\Models\Hospital','Hospital_Id');
    }

    public function patient()
    {
        return $this->belongsTo('App\Models\Patient','Patient_Id');
    }



    public function doctor()
    {
        return $this->belongsTo('App\Models\User','Doctor_Id');
    }


    public function DoneBy()
    {
        return $this->belongsTo('App\Models\User','CreatedBy_Id');
    }



}
