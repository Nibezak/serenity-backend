<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class TypeOrg extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'typeorg';
    protected $fillable = [
    'name',
    ];
}
