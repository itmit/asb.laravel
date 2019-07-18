<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bid;
use App\Models\PointOnMap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BidApiController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     */
    public function store(Request $request)
    {
        $a = Bid::create([
            'location' =>
                PointOnMap::create([
                    'client' => null,
                    'latitude' => $request->input('latitude'),
                    'longitude' => $request->input('longitude')
                ])->id,
            'status' => 'PendingAcceptance'
        ]);
        
    }
}
