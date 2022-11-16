<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class NoteObjective extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'noteobjective';
    protected $fillable = [
    'content',
    'Patient_Id',
    'Hospital_Id',
    'CreatedBy_Id',
    'Status',
    'Notetype',
    'EstimatedComplation',
    'TreatmentStartegyID',

    ];

    public function patient()
    {
        return $this->belongsTo('App\Models\Patient','Patient_Id');
    }
    public function hospital()
    {
        return $this->belongsTo('App\Models\TypeAppointment', 'Hospital_Id');
    }

    public function createdby()
    {
        return $this->belongsTo('App\Models\User', 'CreatedBy_Id');
    }



}
