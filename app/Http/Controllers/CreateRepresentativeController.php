<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CreateRepresentativeController extends Controller
{
    /**
     * Показывает страницу создания диспетчера.
     *
     * @return Response
     */
    public function index()
    {
        return view("admin.createRepresentative");
    }

    /**
     * Создает нового диспетчера и редиректит на главную страницу представителя.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function createRepresentative(Request $request)
    {

        $user = Auth::user();

        if ($user->ability(['super-admin'], ['create-representative'])) {

            $validator = $this->getValidator($request);

            if ($validator->fails()) {
                return redirect()
                    ->route('auth.admin.createRepresentative')
                    ->withErrors($validator)
                    ->withInput();
            }

            DB::beginTransaction();

            $representative = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => bcrypt($request->input('password')),
            ]);

            $representative->attachRole(Role::where('name', '=', 'representative')->first());

            DB::commit();

            return redirect()->route('auth.representativeList');
        }

        return redirect('login');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getValidator(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        return $validator;
    }
}
