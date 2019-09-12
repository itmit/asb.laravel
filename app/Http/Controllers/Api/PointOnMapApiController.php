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
            'longitude' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), "Validation error", 401);
        }

        $is_active = Client::where('id', '=', auth('api')->user()->id)->first('is_active');

        if($is_active->is_active != 1)
        {
            return $this->sendError($is_active->is_active, "Client is not active", 401);
        }

        if($request->uid)
        {
            $id = NULL;
            DB::beginTransaction();
            try {
            
                $record = new PointOnMap;
                // usleep(1);
                $record->client = auth('api')->user()->id;
                $record->latitude = $request->input('latitude');
                $record->longitude = $request->input('longitude');
                $record->save();
                $id = $record->id;

                $record = Bid::where('uid', '=', $request->uid)->lockForUpdate()->first();
                // usleep(1);
                $record->location = $id;
                $record->save();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return $this->sendError(0, 'Ошибка');
            }
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
