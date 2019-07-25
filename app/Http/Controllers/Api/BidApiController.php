<?php

namespace App\Http\Controllers\Api;

use App\Models\Bid;
use App\Models\PointOnMap;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BidApiController extends ApiBaseController
{

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse|void
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), "Validation error", 401);
        }

        Bid::create([
            'location' =>
                PointOnMap::create([
                    'client' => auth('api')->user()->id,
                    'latitude' => $request->input('latitude'),
                    'longitude' => $request->input('longitude')
                ])->id,
            'status' => 'PendingAcceptance'
        ]);
    }
}
