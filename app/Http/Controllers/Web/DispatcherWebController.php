<?php

namespace App\Http\Controllers\Web;

use App\Models\Dispatcher;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class DispatcherWebController extends Controller
{

    /**
     * @return Factory|RedirectResponse|Redirector|View
     */
    public function index()
    {
        return view('representative.dispatcherList', [
                "dispatchers" => Role::getUsersByRoleName('dispatcher')
            ]
        );
    }

    /**
     * Показывает страницу создания диспетчера.
     *
     * @return Response
     */
    public function create()
    {
        if (Auth::user()->ability(['super-admin', 'representative'], ['create-dispatcher'])) {
            return view("representative.createDispatcher");
        }

        return redirect()->route('auth.login');
    }

    /**
     * Создает нового диспетчера и редиректит на главную страницу представителя.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {

        if (Auth::user()->ability(['super-admin', 'representative'], ['create-dispatcher'])) {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return redirect()
                    ->route('auth.dispatcher.create')
                    ->withErrors($validator)
                    ->withInput();
            }

            DB::beginTransaction();

            $dispatcher = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => bcrypt($request->input('password')),
            ]);

            Dispatcher::create([
                'representative' => Auth::id()
            ]);

            $dispatcher->attachRole(Role::where('name', '=', 'dispatcher')->first());

            DB::commit();

            return redirect()->route('auth.dispatcher.index');
        }
        return redirect()->route('login');
    }
}
