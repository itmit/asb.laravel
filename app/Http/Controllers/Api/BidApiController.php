<?php

namespace App\Http\Controllers\Api;

use App\Models\Bid;
use App\Models\PointOnMap;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Events\ChangeStatus;
use Illuminate\Support\Facades\Auth;

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
        $bids = Bid::select('id', 'uid', 'client','status', 'type', 'updated_at', 'created_at', 'latitude', 'longitude')
            ->when(request('status'), function ($query) {
            return $query->where('status', 'PendingAcceptance');
        })
        ->orderBy('created_at', 'desc')
        ->get();
        
        $response = [];
        $currentClient = null;
        
        foreach ($bids as $bid) {
            $currentClient = $bid->client();
            if($currentClient->location() == NULL) continue;
            
            $response[] = [
                'uid' => $bid->uid,
                'status' => $bid->status,
                'type' => $bid->type,
                'updated_at' => date('Y-m-d H:i:s', strtotime($bid->updated_at)),
                'created_at' => date('Y-m-d H:i:s', strtotime($bid->created_at)),
                'location' => [
                'latitude' => $bid->latitude,
                'longitude' => $bid->longitude
            ],
                'client' => [
                'type' => $currentClient['type'],
                'name' => $currentClient['name'],
                'email' => $currentClient['email'],
                'phone_number' => $currentClient['phone_number'],
                'organization' => $currentClient['organization'],
                'note' => $currentClient['note'],
                'user_picture' => $currentClient['user_picture'],
                'passport' => $currentClient['passport'],
                'INN' => $currentClient['INN'],
                'OGRN' => $currentClient['OGRN'],
                'director' => $currentClient['director']
            ]
            ];
        }
        
        return $this->sendResponse(
            $response,
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
            'uid' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), "Validation error", 401);
        }

        $userId = auth('api')->user()->id;

        $is_active = Client::where('id', '=', $userId)->first(['is_active']);

        if($is_active->is_active != 1)
        {
            return $this->sendError($is_active->is_active, "Client is not active", 401);
        }

        $isBid = Bid::where('client', '=', $userId)->where('status', '=', 'PendingAcceptance')->first();
        if($isBid != NULL)
        {
            return $this->sendError('Bid create error', "Уже есть активная тревога", 401);
        }

        $bid = Bid::create([
            'client' => $userId,
            'status' => 'PendingAcceptance',
            'uid' => $request->input('uid'),
            'type' => $request->input('type'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
        ]);

        PointOnMap::create([
            'client' => $userId,
            'bid' => $bid->id
        ]);
    }

    public function updateCoordinates(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' => 'required|uuid',

        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), "Validation error", 401);
        }

        $bid = Bid::where('uid', '=', $request->uid)->first();

        // return $bid;

        $response = [];
        // self::translateStatus($bid);
        // self::translateType($bid);

        $response = [
            'updated_at' => date('Y-m-d H:i:s', strtotime($bid->updated_at)),
            'location' => [
                'latitude' => $bid->latitude,
                'longitude' => $bid->longitude
            ],
        ];
        
        return $this->sendResponse($response, 'Updated');
    }
}
