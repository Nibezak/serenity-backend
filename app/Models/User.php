<?php
namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laratrust\Traits\LaratrustUserTrait;

class User extends Authenticatable implements JWTSubject
{
    use LaratrustUserTrait;
    use HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Role_id',
        'FirstName',
        'LastName',
        'Email',
        'Telephone',
        'gender',
        'ProfileImageUrl',
        'Address',
        'LicenseNumber',
        'Title',
        'Hospital_Id',
        'password',
        'LastLoginDate',
        'JoinDate',
        'IsActive',
        'IsNotLocked',
        'IsAccountNonExpired',
        'IsAccountNonLocked',
        'IsCredentialsNonExpired',
        'session',
        'Speciality',
        'Department_Id',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }


    public function Role()
    {
        return $this->belongsTo('App\Models\Role','Role_id');
    }

    public function hospital()
    {
        return $this->belongsTo('App\Models\Hospital','Hospital_Id');
    }

}
