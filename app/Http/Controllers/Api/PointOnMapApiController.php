<?php

namespace App\Http\Controllers\Api;

use App\Models\PointOnMap;
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
            'longitude' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), "Validation error", 401);
        }

        if($request->uid)
        {
            DB::beginTransaction();
                $record = new PointOnMap;
                $record->client = auth('api')->user()->id;
                $record->latitude = $request->input('latitude');
                $record->longitude = $request->input('longitude');
                $record->save();

                // $record = Bid::where('uid', '=', $request->uid)->lockForUpdate()->first();
                // sleep(1);
                // $record->location = $record->id;
                // $record->save();
            DB::commit();
            // $pom = PointOnMap::create([
            //     'client' => auth('api')->user()->id,
            //     'latitude' => $request->input('latitude'),
            //     'longitude' => $request->input('longitude')
            // ]);

            // $bid = Bid::where('uid', '=', $request->uid)
            // ->update(['location' => $pom->id]);
        }
        else
        {
            PointOnMap::create([
                'client' => auth('api')->user()->id,
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude')
            ]);
        }
    }
}
