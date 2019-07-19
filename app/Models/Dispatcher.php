<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Dispatcher
 * @package App\Models
 */
class Dispatcher extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'representative', 'user'
    ];

    /**
     * @var string
     */
    protected $table = 'dispatcher';

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
    public function user()
    {
        return $this->belongsTo(User::class, 'user')->get()->first();
    }
}
