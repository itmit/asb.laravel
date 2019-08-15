<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use App\Models\PointOnMap;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ClientWebController extends Controller
{

    /**
     * @return Factory|RedirectResponse|Redirector|View
     */
    public function index()
    {
        $repId = false;
        $user = Auth::user();
        if ($user instanceof User) {
            if ($user->hasRole('super-admin'))
            {
                return view('dispatcher.listOfClients', [
                    'clients' => Client::select('*')
                        ->where('is_guard', '<>', 1)
                        ->orderBy('created_at', 'desc')->get()
                ]
            );
            }
            if ($user->hasRole('dispatcher')) {
                $repId = $user->dispatcher->representative;
                return view('dispatcher.listOfClients', [
                    'clients' => Client::where('representative', '=', $repId)
                        ->where('is_guard', '<>', 1)
                        ->orderBy('created_at', 'desc')->get()
                ]
            );
            }
            if ($user->hasRole('representative')) {
                $userId = Auth::id();
                return view('dispatcher.listOfClients', [
                    'clients' => Client::where('representative', '=', $userId)
                        ->where('is_guard', '<>', 1)
                        ->orderBy('created_at', 'desc')->get()
                ]
            );
            }
        }

        // return view('dispatcher.listOfClients', [
        //         'clients' => Client::where('representative', '=', $repId)
        //             ->where('is_guard', '<>', 1)
        //             ->orderBy('created_at', 'desc')->get()
        //     ]
        // );
    }

    /**
     * Показывает страницу создания диспетчера.
     *
     * @return Response
     */
    public function create()
    {
        return view("dispatcher.clientCreationForm");
    }

    /**
     * Создает нового диспетчера и редиректит на главную страницу представителя.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'password' => 'required|string|min:6|confirmed|same:password',
            'phone_number' => 'required|string|min:11',
            'representative' => 'required',
            'organization' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('auth.clint.create')
                ->withErrors($validator)
                ->withInput();
        }

        $number = $request->input('phone_number');
        $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        $phoneNumberObject = $phoneNumberUtil->parse($number, 'RU');
        $number = $phoneNumberUtil->format($phoneNumberObject, \libphonenumber\PhoneNumberFormat::E164);

        Client::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'phone_number' => $number,
            'representative' => $request->input('representative')
        ]);

        return redirect()->route('auth.client.index');
    }

    public function destroy(Request $request)
    {
        Client::destroy($request->input('ids'));

        return response()->json(['Clients destroyed']);
    }

    public function show($id)
    {
        $client = Client::where('id', '=', $id)->first();

        return view("dispatcher.clientDetail", [
            'client' => $client
        ]);
    }

    public function lastLocation(Request $clientID)
    {
        $lastClientLocation = PointOnMap::all()->where('client', '=', $clientID);
        return $lastClientLocation;
        return response()->json($lastClientLocation);
    }

    public function changeActivity(Request $request)
    {
        $user = Auth::user();
        if ($user instanceof User) {
            if ($user->hasRole('super-admin'))
            {
                $client = Client::where('id', '=', $request->clientID)
                    ->update(['is_active' => $request->direction]);
                return response()->json(['Активность пользователя изменена']);
            }
            else return response()->json(['Недостаточно прав']);
        }
        else return 'Что-то пошло не так :(';
    }
}
