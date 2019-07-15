<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bid;

class BidWebController extends Controller
{
    public function index()
    {
        return view('dispatcher.listOfBid', [
            'bids' => Bid::all()->sortByDesc('created_at')
        ]);
    }
}
