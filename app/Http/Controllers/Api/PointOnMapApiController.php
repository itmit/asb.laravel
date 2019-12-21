<?php

namespace App\Http\Controllers\Api;

use App\Models\PointOnMap;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Bid;
use Illuminate\Support\Facades\DB;

class PointOnMapApiController extends ApiBaseController
{

    /**
     * @param Request $request
     * @return JsonResponse|void
     */
    public function store(Request $request)
    {
        $userId = auth('api')->user()->id;

        $validator = Validator::make($request->all(), [
            'latitude' => 'required',
            'longitude' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), "Validation error", 401);
        }

        $is_active = Client::where('id', '=', $userId)->first(['is_active']);

        if($is_active->is_active != 1)
        {
            return $this->sendError($is_active->is_active, "Client is not active", 401);
        }

        $bid = Bid::where('uid', '=', $request->input('uid'))->first();

        PointOnMap::create([
            'client' => $userId,
            'bid' => $bid->id
        ]);

        Bid::where('client', '=', $userId)->where('status', '<>', 'Accepted')->update([
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
        ]);
    }
}
