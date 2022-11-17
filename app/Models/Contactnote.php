<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Contactnote extends Model
{
    use HasFactory, Notifiable;
    protected $table = 'contactnote';
    protected $fillable = [
    'Note_Type',
    'Hospital_Id',
    'Patient_Id',
    'Doctor_Id',
    'DateTime',
    'ContactName',
    'RelationshipToPatient',
    'MethodCommunication',
    'ReasonCommunication',
    'TimeSpent',
    'CommunicationDetails',
    'Signator_Id',
    'Visibility',
    'Status',
    'CreatedBy_Id',
    'Session_Id'

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
        return $this->belongsTo('App\Models\User','CreatedBy_Id');
    }
    public function signator()
    {
        return $this->belongsTo('App\Models\User','Signator_Id');
    }





}
