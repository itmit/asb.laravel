<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class RepresentativeWebController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super-admin');
    }

    /**
     * @return Factory|View
     */
    public function index()
    {
        return view('admin.representativeList', [
            'representatives' => Role::getUsersByRoleName('representative')
                ->sortByDesc('created_at')
        ]);
    }

    /**
     * Показывает страницу создания диспетчера.
     *
     * @return Response|RedirectResponse
     */
    public function create()
    {
        $user = Auth::user();

        if ($user->ability(['super-admin'], ['create-representative'])) {
            return view("admin.createRepresentative");
        }

        return redirect()->route('auth.representative.index');

    }

    /**
     * Создает нового диспетчера и редиректит на главную страницу представителя.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->ability(['super-admin'], ['create-representative'])) {

            $validator = $this->getValidator($request);

            if ($validator->fails()) {
                return redirect()
                    ->route('auth.representative.create')
                    ->withErrors($validator)
                    ->withInput();
            }

            DB::beginTransaction();

            $representative = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'city' => $request->input('city'),
                'fio' => $request->input('fio'),
                'password' => Hash::make($request['password']),
            ]);

            $representative->attachRole(Role::where('name', '=', 'representative')->first());

            DB::commit();

            return redirect()->route('auth.representative.index');
        }

        return redirect('login');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getValidator(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:150',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'fio' => 'required|string|min:2',
        ]);
    }

    public function destroy(Request $request)
    {
        User::destroy($request->input('ids'));

        return response()->json(['Representative destroyed']);
    }
}
