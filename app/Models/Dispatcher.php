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
      'representative'
    ];

    /**
     * @var string
     */
    protected $table = 'dispatcher';
}
