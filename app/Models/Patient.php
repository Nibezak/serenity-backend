<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Patient extends Model
{
    use HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    // protected $table = '';
    protected $fillable = [
        'FirstName',
        'LastName',
        'MobilePhone',
        'HomePhone',
        'WorkPhone',
        'Dob',
        'Gender',
        'AccountNumber',
        'Address',
        'BloodType',
        'Height',
        'Weight',
        'MartialStatus',
        'GenderIdentity',
        'SexualOrientation',
        'Employment',
        'Language',
    ];

}
