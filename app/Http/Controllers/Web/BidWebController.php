<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bid;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;

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
                    'bids' => $bids
                ]);
            }
            else
            {
                $bids = Bid::all()->where('status', '=', 'PendingAcceptance')->sortByDesc('created_at');
                $bs = [];
                self::translateStatus($bids);
                self::translateType($bids);
                foreach ($bids as $bid) {
                    if ($bid->location()->client()->representative != $this->getRepresentativeId()) {
                        continue;
                    }
                    $bs[] = $bid;
                }
                return view('dispatcher.listOfBid', [
                    'bids' => $bs
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
                    if ($bid->location()->client()->representative != $this->getRepresentativeId()) {
                        continue;
                    }

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

        self::translateStatus($bid);

        return view("dispatcher.bidDetail", [
            'bid' => $bid
        ]);
    }

    public function translateStatus($bids)
    {
        if ($bids instanceof Illuminate\Database\Eloquent\Collection) {
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
                    $bisd->status = 'Принята';
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
        return $bids;
    }

    public function alarm()
    {
        return 'alarm';
    }
}
