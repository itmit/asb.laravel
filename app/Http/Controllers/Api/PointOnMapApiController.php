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
        $validator = Validator::make($request->all(), [
            'latitude' => 'required',
            'longitude' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), "Validation error", 401);
        }

        $is_active = Client::where('id', '=', auth('api')->user()->id)->first(['is_active']);

        if($is_active->is_active != 1)
        {
            return $this->sendError($is_active->is_active, "Client is not active", 401);
        }

        $bid = Bid::where('uid', '=', $request->input('uid'))->first();

        PointOnMap::create([
            'client' => auth('api')->user()->id,
            'bid' => $bid->id
        ]);

        Bid::where('client', '=', auth('api')->user()->id)->where('status', '<>', 'Accepted')->update([
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
        ]);
    }
}
