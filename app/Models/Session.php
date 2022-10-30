<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;


class Session extends Model
{
    use HasFactory, Notifiable;
    protected $table = 'sessions';
    protected $fillable = [
    'StartedBy_Id',
    'hospital_Id',
    'Patient_Id',
    'Doctor_Id',
    'Insurance_Id',
    'Status',
    'Service_Id',
    'type',
    ];

    public function doneby()
    {
        return $this->belongsTo('App\Models\User','StartedBy_Id');
    }
    public function hospital()
    {
        return $this->belongsTo('App\Models\Hospital','hospital_Id');
    }

    public function doctor()
    {
        return $this->belongsTo('App\Models\User','Doctor_Id');
    }

    public function patient()
    {
        return $this->belongsTo('App\Models\Patient','Patient_Id');
    }


    public function insurance()
    {
        return $this->belongsTo('App\Models\PatientInsurance','Insurance_Id');
    }




}
