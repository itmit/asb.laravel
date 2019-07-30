<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use EntrustUserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @return BelongsTo
     */
    public function dispatchers()
    {
        return $this->belongsTo(Dispatcher::class);
    }

    /**
     * @return BelongsTo
     */
    public function clients()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return HasOne
     */
    public function dispatcher()
    {
        return $this->hasOne(Dispatcher::class, 'user');
    }
}
