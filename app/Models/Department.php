<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Department extends Model
{
    use HasFactory, Notifiable;
    protected $table = 'departments';
    protected $fillable = [
    'CreatedBy_Id',
    'Hospital_Id',
    'Name',
    ];

    public function hospital()
    {
        return $this->belongsTo('App\Models\Hospital','Hospital_Id');
    }

    public function createdby()
    {
        return $this->belongsTo('App\Models\User','CreatedBy_Id');
    }



}
