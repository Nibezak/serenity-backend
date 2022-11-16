<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Diagnosis extends Model
{
    use HasFactory, Notifiable;


    protected $table = 'diagnosis';
    protected $fillable = ['name','code', 'Hospital_Id', 'CreatedBy_Id', 'Status','icd10'];

    public function hospital()
    {
        return $this->belongsTo('App\Models\TypeAppointment', 'Hospital_Id');
    }

    public function createdby()
    {
        return $this->belongsTo('App\Models\User', 'CreatedBy_Id');
    }


}
