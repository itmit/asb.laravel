<?php

namespace App\Http\Controllers;

use App\Models\Dispatcher;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CreateDispatcherController extends Controller
{

    /**
     * Показывает страницу создания диспетчера.
     *
     * @return Response
     */
    public function index()
    {
        return view("representative.createDispatcher");
    }

    /**
     * Создает нового диспетчера и редиректит на главную страницу представителя.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function createDispatcher(Request $request)
    {

        if (Auth::user()->ability(['super-admin', 'representative'], ['create-dispatcher'])) {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return redirect()
                    ->route('auth.representative.createDispatcher')
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

            return redirect()->route('auth.dispatcherList');
        }
        return redirect('login');
    }
}
