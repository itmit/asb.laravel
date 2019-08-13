<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bid;
use Illuminate\Support\Facades\Auth;

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
            }
        }

        
    }

    public function updateList()
    {
        $bids = Bid::select('*')
        ->join('point_on_map', 'bid.location', '=', 'point_on_map.id')
        ->join('clients', 'point_on_map.client', '=', 'clients.id')
        ->get();
        $bs = [];
        foreach ($bids as $bid) {
            if ($bid->location()->client()->representative != $this->getRepresentativeId()) {
                continue;
            }

            $bs[] = $bid;
        }

        return response()->json($bs);
    }
}
