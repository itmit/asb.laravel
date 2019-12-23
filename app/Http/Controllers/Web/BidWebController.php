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
                if($request->input('selectBidsByStatus') == 'active')
                {
                    $bids = Bid::all()->where('status', '<>', 'Processed')->sortByDesc('created_at');
                }
                else
                {
                    $bids = Bid::all()->where('status', '=', 'Processed')->sortByDesc('created_at');
                }

                $response = [];
                self::translateStatus($bids);
                self::translateType($bids);
                foreach ($bids as $bid) {
                    $guard = Client::where('id', '=', $bid->guard)->first();
                    if($guard == NULL)
                    {
                        $guard_name = '';
                    }
                    else
                    {
                        $guard_name = $guard->name;
                    }
                    $response[] = [
                        'id'   => $bid->id,
                        'status' => $bid->status,
                        'type' => $bid->type,
                        'updated_at' => date('H:i d.m.Y', strtotime($bid->updated_at->timezone('Europe/Moscow'))),
                        'created_at' => date('H:i d.m.Y', strtotime($bid->created_at->timezone('Europe/Moscow'))),
                        'location' => [
                            'latitude' => $bid->latitude,
                            'longitude' => $bid->longitude
                        ],
                        'client' => [
                            'id' => $bid->client()->id,
                            'name' => $bid->client()->name,
                            'organization' => $bid->client()->organization,
                            'email' => $bid->client()->email,
                            'phone_number' => $bid->client()->phone_number
                        ]
                    ];
                }

                return response()->json($response);
            }
            else
            {
                if($request->input('selectBidsByStatus') == 'active')
                {
                    $bids = Bid::all()->where('status', '<>', 'Processed')->sortByDesc('created_at');
                }
                else
                {
                    $bids = Bid::all()->where('status', '=', 'Processed')->sortByDesc('created_at');
                }

                $response = [];
                self::translateStatus($bids);
                self::translateType($bids);
                foreach ($bids as $bid) {
                    $response[] = [
                        'id'   => $bid->id,
                        'status' => $bid->status,
                        'type' => $bid->type,
                        'updated_at' => date('H:i d.m.Y', strtotime($bid->updated_at->timezone('Europe/Moscow'))),
                        'created_at' => date('H:i d.m.Y', strtotime($bid->created_at->timezone('Europe/Moscow'))),
                        'location' => [
                            'latitude' => $bid->latitude,
                            'longitude' => $bid->longitude
                        ],
                        'client' => [
                            'id' => $bid->client()->id,
                            'name' => $bid->client()->name,
                            'organization' => $bid->client()->organization,
                            'email' => $bid->client()->email,
                            'phone_number' => $bid->client()->phone_number
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

        self::translateStatus($bid);
        self::translateType($bid);

        return view("dispatcher.bidDetail", [
            'bid' => $bid,
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
                        $bid->status = 'В работе';
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
                    $bids->status = 'В работе';
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
                'updated_at' => date('H:i:s d.m.Y', strtotime($bid->updated_at->timezone('Europe/Moscow'))),
                'location' => [
                    'latitude' => $bid->latitude,
                    'longitude' => $bid->longitude,
                    'last_checkpoint' => date('H:i:s d.m.Y', strtotime($bid->updated_at->timezone('Europe/Moscow')))
                ]
            ];
        }
        elseif($bidid->bidStatus == 'Ожидает принятия')
        {
            $response = [];
            self::translateStatus($bid);
            self::translateType($bid);

            $response = [
                'updated_at' => date('H:i:s d.m.Y', strtotime($bid->updated_at->timezone('Europe/Moscow'))),
                'location' => [
                    'latitude' => $bid->latitude,
                    'longitude' => $bid->longitude,
                    'last_checkpoint' => date('H:i:s d.m.Y', strtotime($bid->updated_at->timezone('Europe/Moscow')))
                ]
            ];
        }
        else
        {
            $response = [];
            $response = ['false'];
        }

        return response()->json($response);
    }

    public function alarmSound()
    {
        $user = Auth::user();
        $i=0;

        if ($user->hasRole('dispatcher'))
        {
            $bids = Bid::where('status', '=', 'PendingAcceptance')->orderBy('created_at', 'desc')->limit(10)->get();

            $response = [];
            
            if(count($bids) == 1)
            {
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
                        'location' =>
                        [
                            'latitude' => $bid->latitude,
                            'longitude' => $bid->longitude
                        ],
                        'client' =>
                        [
                            'id' => $currentClient['id'],
                            'name' => $currentClient['name'],
                            'email' => $currentClient['email'],
                            'phone_number' => $currentClient['phone_number'],
                            'organization' => $currentClient['organization'],
                        ]
                    ];
                }

                return response()->json($response);
            }
            if(count($bids) > 1)
            {
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
                        'location' =>
                        [
                            'latitude' => $bid->latitude,
                            'longitude' => $bid->longitude
                        ],
                        'client' =>
                        [
                            'id' => $currentClient['id'],
                            'name' => $currentClient['name'],
                            'email' => $currentClient['email'],
                            'phone_number' => $currentClient['phone_number'],
                            'organization' => $currentClient['organization'],
                        ]
                    ];
                }
                return response()->json($response);
            }
            
            if(count($bids) == 0)
            {
                return response()->json('');
            }
        }
        else return response()->json('');
    }

    public function closeByUser(Request $request)
    {
        $bid = Bid::where('id', '=', $request->bidid)->update(['status' => 'Processed']);

        return 'close';
    }
}
