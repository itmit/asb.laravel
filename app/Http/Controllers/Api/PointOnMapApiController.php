<?php

namespace App\Http\Controllers\Api;

use App\Models\PointOnMap;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Bid;

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
            'longitude' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), "Validation error", 401);
        }

        if($request->uid)
        {
            $pom = PointOnMap::create([
                'client' => null,
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude')
            ]);

            return $pom->id;

            $bid = Bid::where('uid', '=', $request->uid)
            ->update(['location' => $request->new_status]);
        }
        else
        {
            PointOnMap::create([
                'client' => null,
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude')
            ]);
        }
    }
}
