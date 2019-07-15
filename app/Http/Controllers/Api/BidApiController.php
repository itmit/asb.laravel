<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PointOnMap;
use Illuminate\Http\Request;

class BidApiController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     */
    public function store(Request $request)
    {
        Bid::create([
            'location' =>
                PointOnMap::create([
                    'client' => null,
                    'latitude' => $request->input('latitude'),
                    'longitude' => $request->input('longitude')
                ]),
            'status' => 'PendingAcceptance'
        ]);
    }
}
