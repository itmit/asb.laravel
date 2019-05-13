<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class HomeRepresentativeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index()
    {
        if (!is_null(Auth::user())) {
            if (Auth::user()->hasRole(['super-admin', 'representative'])) {

                return view('representative.home', [
                        "dispatchers" => Role::findOrFail(5)->users()->get()
                    ]
                );
            }
        }

        return view('auth.login');
    }
}
