<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @var string
     */
    protected $table = 'bid';

    public function client()
    {
        return $this->belongsTo(Client::class, 'client')->get()->first();
    }
}
