<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PointOnMap;

class PointOnMapApiController extends Controller
{
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        PointOnMap::create([
            'client' => null,
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude')
        ]);
    }
}
