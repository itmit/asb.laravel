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
                $bids = Bid::all()->sortByDesc('created_at');
                return view('dispatcher.listOfBid', [
                    'bids' => $bids
                ]);
            }
            else
            {
                $bids = Bid::all()->sortByDesc('created_at');
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
                $bids = Bid::all()->sortByDesc('created_at');

                $response = [];
                foreach ($bids as $bid) {
                    $response[] = [
                        'id'   => $bid->id,
                        'status' => $bid->status,
                        'updated_at' => $bid->updated_at,
                        'created_at' => $bid->created_at,
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

                // $bids = Bid::select('bid.id', 'type', 'location', 'status', 'bid.created_at', 'bid.updated_at')
                // ->join('point_on_map', 'bid.location', '=', 'point_on_map.id')
                // ->join('clients', 'point_on_map.client', '=', 'clients.id')
                // ->orderBy('bid.created_at', 'desc')
                // ->get();

                return response()->json($response);
            }
            else
            {
                $bids = Bid::select('*')
                ->join('point_on_map', 'bid.location', '=', 'point_on_map.id')
                ->join('clients', 'point_on_map.client', '=', 'clients.id')
                ->orderBy('bid.created_at', 'desc')
                ->get();
                $bs = [];
                foreach ($bids as $bid) {
                    if ($bid->location()->client()->representative != $this->getRepresentativeId()) {
                        continue;
                    }

                    $bs[] = $bid;
                }

                return response()->json($bs);
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
