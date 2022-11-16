<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Slot extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'slots';
    protected $fillable = [
    'title',
     'start',
     'end',
     'day',
     'User_Id',
     'Hospital_Id',
     'status',
     'description',
     'Createdby_Id',
    ];


    public function hospital()
    {
        return  $this->belongsTo('App\Models\Hospital','Hospital_Id');
    }

    public function owner()
    {
        return $this->belongsTo('App\Models\User','User_Id');
    }

    public function doneby()
    {
        return $this->belongsTo('App\Models\User','Createdby_Id');
    }


}
