<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Role;
use App\Models\Dispatcher;

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

        $cities = User::select('*')
            ->whereNotNull ('city')
            ->distinct()
            ->count('city');

        $reprs = Role::getUsersByRoleName('representative')->count();

        $dispathers = Dispatcher::all()->count();
 
        return view('home', [
            'cities' => $cities,
            'reprs' => $reprs,
            'dispathers' => $dispathers,
        ]);
    }
}
