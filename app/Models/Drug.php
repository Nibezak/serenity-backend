<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Drug extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'drugs';
    protected $fillable =    [
    'name',
    'hospital',
    'dose',
    'weight',
    'recordedby',
    'Pharmacy',
    ];


}
