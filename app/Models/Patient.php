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

     protected $table = 'patients';
    protected $fillable = [
        'FirstName',
        'LastName',
        'email',
        'MobilePhone',
        'HomePhone',
        'WorkPhone',
        'Dob',
        'GenderIdentity',
        'AccountNumber',
        'Address',
        'BloodType',
        'Height',
        'Weight',
        'MartialStatus',
        'AdministrativeSex',
        'SexualOrientation',
        'Employment',
        'Languages',
        'Createdby_Id',
        'Hospital_Id',
        'Nationality',
        'SSN',
        'Province',
        'District',
        'Sector',
        'Cell',
        'Village',
        'StreetCode',
        'Status',
        'AssignedDoctor_Id',
        'profileimageUrl',
        'PatientCode',
        'gender',



    ];

    public function hospital()
    {
        return  $this->belongsTo('App\Models\Hospital','Hospital_Id');
    }
    public function doctor()
    {
        return $this->belongsTo('App\Models\User','AssignedDoctor_Id');
    }
    public function doneby()
    {
        return $this->belongsTo('App\Models\User','Createdby_Id');
    }


}
