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
        if(request('status'))
        {
            $bids = DB::table('bid')
            ->join('point_on_map', 'bid.location', '=', 'point_on_map.id')
            ->join('clients', 'point_on_map.client', '=', 'clients.id')
            ->join('users', 'clients.representative', '=', 'users.id')
            // ->where('clients.representative', '=', $this->getRepresentativeId())
            ->where('bid.status', '=', request('status'))
            ->select('bid.status', 'point_on_map.latitude', 'point_on_map.longitude', 'clients.name', 'clients.email',
                'clients.phone_number', 'clients.organization', 'bid.created_at', 'bid.updated_at', 'bid.uid', 'clients.note', 'clients.user_picture', 'bid.type')
            ->orderBy('bid.updated_at', 'desc')
            ->get();
        }
        else 
        {
            $bids = DB::table('bid')
            ->join('point_on_map', 'bid.location', '=', 'point_on_map.id')
            ->join('clients', 'point_on_map.client', '=', 'clients.id')
            ->join('users', 'clients.representative', '=', 'users.id')
            // ->where('clients.representative', '=', $this->getRepresentativeId())
            ->select('bid.status', 'point_on_map.latitude', 'point_on_map.longitude', 'clients.name', 'clients.email',
                'clients.phone_number', 'clients.organization', 'bid.updated_at', 'clients.note', 'clients.user_picture', 'bid.created_at', 'bid.uid', 'bid.type')
            ->orderBy('bid.updated_at', 'desc')
            ->get();
        }

        $response = [];
        foreach ($bids as $bid) {
            $response[] = [
                'uid'   => $bid->uid,
                'status' => $bid->status,
                'type' => $bid->type,
                'updated_at' => $bid->updated_at,
                'created_at' => $bid->created_at,
                'location' => [
                    'latitude' => $bid->latitude,
                    'longitude' => $bid->longitude
                ],
                'client' => [
                    'name' => $bid->name,
                    'email' => $bid->email,
                    'phone_number' => $bid->phone_number,
                    'organization' => $bid->organization,
                    'note' => $bid->note, 
                    'user_picture' => $bid->user_picture
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
            return $this->sendError($validator->errors(), "Validation error", 401);
        }

        $is_active = Client::where('id', '=', auth('api')->user()->id)->first(['is_active']);

        if($is_active->is_active != 1)
        {
            return $this->sendError($is_active->is_active, "Client is not active", 401);
        }

        Bid::create([
            'location' =>
                PointOnMap::create([
                    'client' => auth('api')->user()->id,
                    'latitude' => $request->input('latitude'),
                    'longitude' => $request->input('longitude')
                ])->id,
            'status' => 'PendingAcceptance',
            'uid' => $request->input('uid'),
            'type' => $request->input('type')
        ]);
    }

    public function changeStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' => 'required',
            'new_status' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), "Validation error", 401);
        }

        $userId = Auth::id();

        if($request['new_status'] == 'Accepted')
        {
            $guard_enable = Bid::where('status', '=', 'Accepted')->where('guard', '=', $userId)->first();

            if($guard_enable != NULL)
            {
                return $this->SendError('Guard error', 'Данный экипаж уже принял другую заявку', 401);
            }
        }
        

        $bid = Bid::where('uid', '=', $request->uid)
            ->update(['status' => $request->new_status, 'guard' => $userId]);

        if($bid > 0)
        {
            // event(new ChangeStatus($bid));
            return $this->sendResponse([
                $bid
            ],
                'Updated');
        }
        return $this->SendError('Update error', 'Something gone wrong', 401);
    }

    public function updateCoordinates(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' => 'required|uuid',

        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), "Validation error", 401);
        }

        $bid = Bid::where('uid', '=', $request->uid)->first();

        // return $bid;

        $response = [];
        // self::translateStatus($bid);
        // self::translateType($bid);

        $response = [
            'updated_at' => substr($bid->updated_at->timezone('Europe/Moscow'), 0),
            'location' => [
                'latitude' => $bid->location()->latitude,
                'longitude' => $bid->location()->longitude
            ],
        ];
        
        return $this->sendResponse($response, 'Updated');
    }

    // public function testFunc(Request $bidID)
    // {
    //     $bid = Bid::findOrFail($bidID)->first();

    //     return event(new ChangeStatus($bid));
    // }
}
