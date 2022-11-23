<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $table = 'documents';

    protected $fillable= [
        'file',
        'Patient_Id',
        'Hospital_Id',
        'AssignedDoctor_Id',
        'Createdby_Id',
    ];

    public function patient()
    {
        return $this->belongsTo('App\Models\Patient','Patient_Id');
    }

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
