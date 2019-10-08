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
        'uid',
        'type',
    ];

    /**
     * @var string
     */
    protected $table = 'bid';

    public function client()
    {
        dd($this->belongsTo(Client::class, 'client')->get()->first());
        return $this->belongsTo(Client::class, 'client')->get()->first();
    }
}
