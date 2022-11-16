<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Assigneddocotor extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'assigneddoctor';
    protected $fillable = [

     'Hospital_Id',
     'Doctor_Id',
     'Patient_Id',
     'AssignedBy_Id',
     'Date',
     'Status',

    ];


    public function hospital()
    {
        return  $this->belongsTo('App\Models\Hospital','Hospital_Id');
    }
    public function doctor()
    {
        return $this->belongsTo('App\Models\User','Doctor_Id');
    }
    public function doneby()
    {
        return $this->belongsTo('App\Models\User','AssignedBy_Id');
    }

    public function patient()
    {
        return $this->belongsTo('App\Models\User','Patient_Id');
    }
}
