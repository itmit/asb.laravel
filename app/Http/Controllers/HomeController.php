<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Class HomeController
 * @package App\Http\Controllers
 */
class HomeController extends Controller
{
    /**
     * @return Factory|View
     */
    public function index()
    {
        // dd(Hash::make('x5410041', [
        //     'rounds' => 12
        // ]));

        return view('home');
    }
}
