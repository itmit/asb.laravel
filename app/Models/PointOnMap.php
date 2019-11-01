<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointOnMap extends Model
{
    /**
     * @var string
     */
    protected $table = 'point_on_map';

    protected $guarded = ['id'];

    // public function client() 
    // {
    //     return $this->belongsTo(Client::class, 'client')->get()->first();        
    // }
}
