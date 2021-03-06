<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class Client extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'clients';

    protected $guarded = ['id'];

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * @return User
     */
    public function representative()
    {
        return $this->belongsTo(User::class, 'representative')->get()->first();
    }

    /**
     * @return User
     */
    public function bid()
    {
        return $this->hasMany(Bid::class)->get()->first();
    }

    public function location()
    {
        return $this->hasMany(PointOnMap::class, 'client')->latest()->first();
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function loginUsername()
    {
        return 'name';
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
