<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bid;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class BidWebController extends BaseWebController
{
    public function index()
    {
        $user = Auth::user();
        if ($user instanceof User) {
            if ($user->hasRole('super-admin'))
            {
                $bids = Bid::all()->where('status', '=', 'PendingAcceptance')->sortByDesc('created_at');
                self::translateStatus($bids);
                self::translateType($bids);
                return view('dispatcher.listOfBid', [
                    'bids' => $bids,
                    'role' => 1
                ]);
            }
            else
            {
                $bids = Bid::all()->where('status', '=', 'PendingAcceptance')->sortByDesc('created_at');
                $bs = [];
                self::translateStatus($bids);
                self::translateType($bids);
                // foreach ($bids as $bid) {
                //     if ($bid->location()->client()->representative != $this->getRepresentativeId()) {
                //         continue;
                //     }
                //     $bs[] = $bid;
                // }
                return view('dispatcher.listOfBid', [
                    'bids' => $bids,
                    'role' => 0
                ]);
            };
        };
        return 'Что-то пошло не так :(';
    }

    public function updateList(Request $request)
    {

        $user = Auth::user();
        if ($user instanceof User) {
            if ($user->hasRole('super-admin'))
            {
                $bids = Bid::all()->where('status', '=', $request->input('selectBidsByStatus'))->sortByDesc('created_at');

                $response = [];
                self::translateStatus($bids);
                self::translateType($bids);
                foreach ($bids as $bid) {
                    $response[] = [
                        'id'   => $bid->id,
                        'status' => $bid->status,
                        'type' => $bid->type,
                        'updated_at' => substr($bid->updated_at->timezone('Europe/Moscow'), 0),
                        'created_at' => substr($bid->created_at->timezone('Europe/Moscow'), 0),
                        'location' => [
                            'latitude' => $bid->location()->latitude,
                            'longitude' => $bid->location()->longitude
                        ],
                        'client' => [
                            'id' => $bid->location()->client()->id,
                            'name' => $bid->location()->client()->name,
                            'email' => $bid->location()->client()->email
                        ]
                    ];
                }

                return response()->json($response);
            }
            else
            {
                $bids = Bid::all()->where('status', '=', $request->input('selectBidsByStatus'))->sortByDesc('created_at');

                $response = [];
                self::translateStatus($bids);
                self::translateType($bids);
                foreach ($bids as $bid) {
                    // if ($bid->location()->client()->representative != $this->getRepresentativeId()) {
                    //     continue;
                    // }

                    $response[] = [
                        'id'   => $bid->id,
                        'status' => $bid->status,
                        'type' => $bid->type,
                        'updated_at' => substr($bid->updated_at->timezone('Europe/Moscow'), 0),
                        'created_at' => substr($bid->created_at->timezone('Europe/Moscow'), 0),
                        'location' => [
                            'latitude' => $bid->location()->latitude,
                            'longitude' => $bid->location()->longitude
                        ],
                        'client' => [
                            'id' => $bid->location()->client()->id,
                            'name' => $bid->location()->client()->name,
                            'email' => $bid->location()->client()->email
                        ]
                    ];
                }

                return response()->json($response);
            };
        };
        return 'Что-то пошло не так :(';
    }

    public function show($id)
    {
        $bid = Bid::where('id', '=', $id)->first();
        $guard = Client::where('id', '=', $bid->guard)->first();

        self::translateStatus($bid);
        self::translateType($bid);

        return view("dispatcher.bidDetail", [
            'bid' => $bid,
            'guard' => $guard
        ]);
    }

    public function translateStatus($bids)
    {
        if ($bids instanceof Collection) {
            foreach ($bids as $bid) {
                switch ($bid->status) {
                    case 'PendingAcceptance':
                        $bid->status = 'Ожидает принятия';
                        break;
                    case 'Accepted':
                        $bid->status = 'Принята';
                        break;
                    case 'Processed':
                        $bid->status = 'Выполнена';
                        break;
                    default:
                        $bid->type = 'Неопределено';
                };
            }
        }
        else
        {
            switch ($bids->status) {
                case 'PendingAcceptance':
                    $bids->status = 'Ожидает принятия';
                    break;
                case 'Accepted':
                    $bids->status = 'Принята';
                    break;
                case 'Processed':
                    $bids->status = 'Выполнена';
                    break;
                default:
                    $bids->type = 'Неопределено';
            };
        }
        
        return $bids;
    }

    public function translateType($bids)
    {
        if ($bids instanceof Collection) {
            foreach ($bids as $bid) {
                switch ($bid->type) {
                    case 'Alert':
                        $bid->type = 'Тревога';
                        break;
                    case 'Call':
                        $bid->type = 'Звонок';
                        break;
                    default:
                        $bid->type = 'Неопределено';
                };
            }
        }
        else
        {
            switch ($bids->type) {
                case 'Alert':
                    $bids->type = 'Тревога';
                    break;
                case 'Call':
                    $bids->type = 'Звонок';
                    break;
                default:
                    $bids->type = 'Неопределено';
            };
        }
        return $bids;
    }

    public function updateCoordinates(Request $bidid)
    {
        $bid = Bid::where('id', '=', $bidid->bidid)->first();

        if($bidid->bidStatus == 'Принята')
        {
            $guard = Client::where('id', '=', $bid->guard)->first();

            $response = [];
            self::translateStatus($bid);
            self::translateType($bid);

            $response = [
                'updated_at' => substr($bid->updated_at->timezone('Europe/Moscow'), 0),
                'location' => [
                    'latitude' => $bid->location()->latitude,
                    'longitude' => $bid->location()->longitude
                ],
                'guard' => [
                    'guard_latitude' => $guard->latitude,
                    'guard_longitude' => $guard->longitude
                ]
            ];
        }
        else
        {
            $response = [];
            self::translateStatus($bid);
            self::translateType($bid);

            $response = [
                'updated_at' => substr($bid->updated_at->timezone('Europe/Moscow'), 0),
                'location' => [
                    'latitude' => $bid->location()->latitude,
                    'longitude' => $bid->location()->longitude
                ]
            ];
        }

        return response()->json($response);
    }

    public function alarmSound()
    {
        $user = Auth::user();

        if ($user->hasRole('dispatcher'))
        {
            $bids = Bid::all()->where('status', '=', 'PendingAcceptance')->sortByDesc('created_at');

            $response = [];
            if(count($bids) == 1)
            {
                $response = [
                    'id'   => $bid->id,
                    'status' => $bid->status,
                    'type' => $bid->type,
                    'updated_at' => substr($bid->updated_at->timezone('Europe/Moscow'), 0),
                    'created_at' => substr($bid->created_at->timezone('Europe/Moscow'), 0),
                    'location' => [
                        'latitude' => $bid->location()->latitude,
                        'longitude' => $bid->location()->longitude
                    ],
                    'client' => [
                        'id' => $bid->location()->client()->id,
                        'name' => $bid->location()->client()->name,
                        'email' => $bid->location()->client()->email
                    ]
                ];
                return response()->json($response);
            }
            if(count($bids) > 1)
            {
                foreach ($bids as $bid) {
                    $response[] = [
                        'id'   => $bid->id,
                        'status' => $bid->status,
                        'type' => $bid->type,
                        'updated_at' => substr($bid->updated_at->timezone('Europe/Moscow'), 0),
                        'created_at' => substr($bid->created_at->timezone('Europe/Moscow'), 0),
                        'location' => [
                            'latitude' => $bid->location()->latitude,
                            'longitude' => $bid->location()->longitude
                        ],
                        'client' => [
                            'id' => $bid->location()->client()->id,
                            'name' => $bid->location()->client()->name,
                            'email' => $bid->location()->client()->email
                        ]
                    ];
                }
                return response()->json($response);
            }
        }
    }
    else return response()->json('err');
}
