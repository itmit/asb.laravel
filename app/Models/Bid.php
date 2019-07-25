<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'location',
        'status',
    ];

    /**
     * @var string
     */
    protected $table = 'bid';

    public function location()
    {
        return $this->belongsTo(PointOnMap::class, 'location')->get()->first();
    }
}
