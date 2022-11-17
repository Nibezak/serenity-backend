<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Miscnote extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'miscnote';
    protected $fillable = [
    'Note_Type',
    'Hospital_Id',
    'Patient_Id',
    'Doctor_Id',
    'DateTime',
    'NoteContent',
    'Signator_Id',
    'Visibility',
    'Status',
    'CreatedBy_Id',
    'Appoint_Id',
    'Session_Id'

    ];


    public function hospital()
    {
        return $this->belongsTo('App\Models\Hospital','Hospital_Id');
    }

    public function appointment()
    {
        return $this->belongsTo('App\Models\Hospital','Appoint_Id');
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



    public function patient()
{
    return $this->belongsTo('App\Models\Patient','Patient_Id');
}



}
