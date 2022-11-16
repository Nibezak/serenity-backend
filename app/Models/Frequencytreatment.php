<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Frequencytreatment extends Model
{
    use HasFactory, Notifiable;
    protected $table = 'frequencytreatment';
    protected $fillable = ['name', 'Hospital_Id', 'CreatedBy_Id', 'Status'];

    public function hospital()
    {
        return $this->belongsTo('App\Models\TypeAppointment','Hospital_Id');
    }

    public function createdby()
    {
        return $this->belongsTo('App\Models\User','CreatedBy_Id');
    }

}
