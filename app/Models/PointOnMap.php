<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointOnMap extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'client_id',
        'latitude',
        'longitude',
    ];

    /**
     * @var string
     */
    protected $table = 'point_on_map';

    public function client() 
    {
        return $this->belongsTo(Client::class, 'client')->get()->first();        
    }
}
