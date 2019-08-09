<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bid;

class BidWebController extends BaseWebController
{
    public function index()
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

    public function updateList()
    {
        $bids = Bid::all()
        ->join('point_on_map', 'bid.location', '=', 'point_on_map.id')
        ->sortByDesc('created_at')
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
