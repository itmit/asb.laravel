<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bid;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class BidWebController extends BaseWebController
{
    public function index()
    {
        $user = Auth::user();
        if ($user instanceof User) {
            if ($user->hasRole('super-admin'))
            {
                $bids = Bid::all()->where('status', '=', 'PendingAcceptance')->sortByDesc('created_at');
                return view('dispatcher.listOfBid', [
                    'bids' => $bids
                ]);
            }
            else
            {
                $bids = Bid::all()->where('status', '=', 'PendingAcceptance')->sortByDesc('created_at');
                $bs = [];
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

    public function updateList()
    {

        $user = Auth::user();
        if ($user instanceof User) {
            if ($user->hasRole('super-admin'))
            {
                $bids = Bid::all()->where('status', '=', 'PendingAcceptance')->sortByDesc('created_at');

                $response = [];
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
                            'name' => $bid->location()->client()->name,
                            'email' => $bid->location()->client()->email
                        ]
                    ];
                }

                return response()->json($response);
            }
            else
            {
                $bids = Bid::all()->where('status', '=', 'PendingAcceptance')->sortByDesc('created_at');

                $response = [];
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

        return view("dispatcher.bidDetail", [
            'bid' => $bid
        ]);
    }
}
