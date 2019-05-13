<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class CreateDispatcherController extends Controller
{

    /**
     * Показывает страницу создания диспетчера.
     *
     * @return Response
     */
    public function index() {
        return view("representative.createDispatcher");
    }


    /**
     * Создает нового диспетчера и редиректит на главную страницу представителя.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function createDispatcher(Request $request) {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('representative.createDispatcher')
                ->withErrors($validator)
                ->withInput();
        }

        $dispatcher = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);

        $dispatcher->attachRole(Role::where('name', '=', 'dispatcher')->first());

        return redirect()->route('representativeHome');
    }
}
