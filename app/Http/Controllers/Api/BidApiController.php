<?php

namespace App\Http\Controllers\Api;

use App\Models\Bid;
use App\Models\PointOnMap;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BidApiController extends ApiBaseController
{
    /**
     * Возвращает все завки, по дате обновления, по убыванию.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request) : JsonResponse
    {
        $bids = DB::table('bid')
            ->join('point_on_map', 'bid.location', '=', 'point_on_map.id')
            ->join('clients', 'point_on_map.client', '=', 'clients.id')
            ->join('users', 'clients.representative', '=', 'users.id')
            ->where('clients.representative', '=', $this->getRepresentativeId())
            ->where('bid.status', '=', 'PendingAcceptance')
            ->orderBy('bid.updated_at', 'desc')
            ->get()->toArray();

        echo $this->getRepresentativeId();

        return $this->sendResponse(
            $bids,
            'Bids retrieved successfully.'
        );
    }

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

    public function PendingAcceptanceBids()
    {
        
    }
}
