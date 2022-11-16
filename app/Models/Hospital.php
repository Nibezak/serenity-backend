<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Hospital extends Model
{
    use HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'hospital';
    protected $fillable = [

    'TypeOrganization',
    'PracticeName',
    'BusinessPhone',
    'BusinessEmail',
    'Province',
    'District',
    'Sector',
    'Cell',
    'Village',
    'TinNumber',
    'logo',
    'Doneby',
    'IsClinician',
    'IsReceptionist',
    'IsFinance',


    ];



}
