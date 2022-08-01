<?php

namespace App\Models;

use Laratrust\Models\LaratrustRole;

class Role extends LaratrustRole
{
    public $guarded = [];
    public function User()
    {
        return $this->hasMany('App\Models\User');
    }
}
