<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class TypeAppointment extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'typeappointments';
    protected $fillable = [
    'name',
    'createdBy_Id',
    'hospital_Id',
    ];


    public function creator()
    {
        return $this->belongsTo('App\Models\User','createdBy_Id');
    }

    public function hospital()
    {
        return $this->belongsTo('App\Models\Hospital','hospital_Id');
    }


}
