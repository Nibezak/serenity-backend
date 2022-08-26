<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Areaofrisk extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'areaofrisk';
    protected $fillable = [
      'Hospital_Id',
      'Patient_Id',
      'CreatedBy_Id',
      'Area_of_risk',
      'Visibility',
      'Status',
      'Levelofrisk',
      'Intenttoact',
      'Plantoact',
      'Meanstoact',
      'RisksFactors',
      'ProtectiveFactors',
      'AdditionalDetails',


    ];
    public function hospital()
    {
        return $this->belongsTo('App\Models\Hospital','Hospital_Id');
    }
    public function doneby()
    {
        return $this->belongsTo('App\Models\User','CreatedBy_Id');
    }

    public function appointment()
    {
        return $this->belongsTo('App\Models\Appointment','Appointment_Id');
    }


    public function patient()
    {
    return $this->belongsTo('App\Models\Patient','Patient_Id');
    }


}
