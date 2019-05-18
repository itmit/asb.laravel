<?php

namespace App\Http\Controllers;

use App\Models\Dispatcher;
use App\Models\Role;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DispatcherListController extends Controller
{
    /**
     * @return Factory|RedirectResponse|Redirector|View
     */
    public function index()
    {
        if (Auth::user()->ability(['super-admin'], ['create-dispatcher'])) {

            return view('representative.dispatcherList', [
                    "dispatchers" => Role::getUsersByRoleName('dispatcher')
                ]
            );
        }

        return redirect('/login');
    }
}
