<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class GuardWebController extends Controller
{

    /**
     * @return Factory|RedirectResponse|Redirector|View
     */
    public function index()
    {
        $repId = false;
        $user = Auth::user();
        if ($user instanceof User) {
            if ($user->hasRole('dispatcher')) {
                $repId = $user->dispatcher->representative;
                return view('representative.guardList', [
                    'guards' => Client::where('representative', '=', $repId)
                        ->where('is_guard', '=', 1)
                        ->orderBy('created_at', 'desc')->get()
                ]
            );
        }

            if ($user->hasRole('representative')) {
                $userId = Auth::id();
                return view('representative.guardList', [
                    'guards' => Client::where('representative', '=', $userId)
                        ->where('is_guard', '=', 1)
                        ->orderBy('created_at', 'desc')->get()
                ]
            );

            } elseif ($user->hasRole('super-admin')) {
                return view('representative.guardList', [
                    'guards' => Client::select('*')
                        ->where('is_guard', '=', 1)
                        ->orderBy('created_at', 'desc')->get()
                ]
            );
            }
        }

        
    }

    /**
     * Показывает страницу создания диспетчера.
     *
     * @return Response
     */
    public function create()
    {
        return view("representative.guardCreate");
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'required|string|min:11'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('auth.guard.create')
                ->withErrors($validator)
                ->withInput();
        }

        if ($user instanceof User && $user->hasRole('dispatcher'))
        {
            $repId = $user->dispatcher->representative;
        }
        else
        {
            $repId = $user->id;
        }

        $number = request('phone');
        $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        $phoneNumberObject = $phoneNumberUtil->parse($number, 'RU');
        $number = $phoneNumberUtil->format($phoneNumberObject, \libphonenumber\PhoneNumberFormat::E164);

        Client::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'phone_number' => $number,
            'representative' => $repId,
            'is_guard' => 1,
            'type' => 'Guard',
        ]);

        return redirect()->route('auth.guard.index');
    }

    public function destroy(Request $request)
    {
        Client::destroy($request->input('ids'));

        return response()->json(['Clients destroyed']);
    }

    public function show($id)
    {
        $guard = Client::where('id', '=', $id)->first();

        return view("representative.guardDetail", [
            'guard' => $guard
        ]);
    }
}
