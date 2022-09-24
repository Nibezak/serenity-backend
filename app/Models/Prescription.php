<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Prescription extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'prescriptions';
    protected $fillable = [

    'Patient_Id',
    'Hospital_Id',
    'Doctor_Id',
    'Diagnosis',
    'Drug_Id',
    'Medical_Advices',
    'Description',
    'RecordedBy_Id',

    ];


    public function hospital()
    {
        return $this->belongsTo('App\Models\Hospital','Hospital_Id');
    }
    public function doctor()
    {
        return $this->belongsTo('App\Models\User','Doctor_Id');
    }
    public function doneby()
    {
        return $this->belongsTo('App\Models\User','RecordedBy_Id');
    }
    public function patient()
    {
        return $this->belongsTo('App\Models\Patient','Patient_Id');
    }







}
