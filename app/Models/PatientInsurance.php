<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class PatientInsurance extends Model
{
    use HasFactory, Notifiable;
    protected $table = 'patientinsurance';
    protected $fillable = [
    'InsuranceCode',
    'Name',
    'Compliment',
    'CreatedBy_Id',
    'Patient_Id',
    ];



    public function doneby()
    {
        return $this->belongsTo('App\Models\User','CreatedBy_Id');
    }


    public function patient()
    {
        return $this->belongsTo('App\Models\Patient','Patient_Id');
    }



}
