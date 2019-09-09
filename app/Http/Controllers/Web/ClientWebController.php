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
use Illuminate\Support\Collection;

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
            'clientType' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('auth.client.create')
                ->withErrors($validator)
                ->withInput();
        }

        if($request->clientType == 'Individual')
        {
            return self::storeIndividual($request);
        }

        if($request->clientType == 'Entity')
        {
            return self::storeEntity($request);
        }
        return 'Ошибка при регистрации';
    }

    public function storeIndividual($request)
    {
        return $request->indv_passport;
        // $number = $request->input('phone_number');
        // $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        // $phoneNumberObject = $phoneNumberUtil->parse($number, 'RU');
        // $number = $phoneNumberUtil->format($phoneNumberObject, \libphonenumber\PhoneNumberFormat::E164);
        // $request['phone_number'] = $number;

        // $validator = Validator::make($request->all(), [
        //     'name' => 'required|string|max:255',
        //     'email' => 'required|string|email|max:255|unique:clients',
        //     'password' => 'required|string|min:6|confirmed',
        //     'password' => 'required|string|min:6|confirmed|same:password',
        //     'phone_number' => 'required|string|min:11|unique:clients,phone_number',
        //     'representative' => 'required',
        //     'organization' => 'required'
        // ]);

        // if ($validator->fails()) {
        //     return redirect()
        //         ->route('auth.client.create')
        //         ->withErrors($validator)
        //         ->withInput();
        // }

        // Client::create([
        //     'name' => $request->input('name'),
        //     'email' => $request->input('email'),
        //     'password' => bcrypt($request->input('password')),
        //     'phone_number' => $number,
        //     'representative' => $request->input('representative')
        // ]);

        // return redirect()->route('auth.client.index');
    }

    public function storeEntity($request)
    {
        return 'stored ent';
        // $number = $request->input('phone_number');
        // $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        // $phoneNumberObject = $phoneNumberUtil->parse($number, 'RU');
        // $number = $phoneNumberUtil->format($phoneNumberObject, \libphonenumber\PhoneNumberFormat::E164);
        // $request['phone_number'] = $number;

        // $validator = Validator::make($request->all(), [
        //     'name' => 'required|string|max:255',
        //     'email' => 'required|string|email|max:255|unique:clients',
        //     'password' => 'required|string|min:6|confirmed',
        //     'password' => 'required|string|min:6|confirmed|same:password',
        //     'phone_number' => 'required|string|min:11|unique:clients,phone_number',
        //     'representative' => 'required',
        //     'organization' => 'required'
        // ]);

        // if ($validator->fails()) {
        //     return redirect()
        //         ->route('auth.client.create')
        //         ->withErrors($validator)
        //         ->withInput();
        // }

        // Client::create([
        //     'name' => $request->input('name'),
        //     'email' => $request->input('email'),
        //     'password' => bcrypt($request->input('password')),
        //     'phone_number' => $number,
        //     'representative' => $request->input('representative')
        // ]);

        // return redirect()->route('auth.client.index');
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

    public function lastLocation(Request $request)
    {
        $lastClientLocation = Client::all()->where('id', '=', $request->clientID)->first();

        $lastLocation = [
            'client' => $lastClientLocation->client,
            'latitude' => $lastClientLocation->latitude,
            'longitude' => $lastClientLocation->longitude,
            'updated_at' => substr($lastClientLocation->updated_at->timezone('Europe/Moscow'), 0),
        ];
        
        return response()->json($lastLocation);
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

    public function selectClientsByType(Request $request)
    {
        $clients = Client::all()->where('type', '=', $request->selectClientsByType)->sortByDesc('created_at');
        self::translateType($clients);
        return response()->json([$clients]); 
    }

    public function clientType(Request $request)
    {
        if($request->clientType == 'Entity')
        {
            return view("dispatcher.clientTypes.entity");
        }
        if($request->clientType == 'Individual')
        {
            return view("dispatcher.clientTypes.individual");
        }
    }
    
    public function translateType($clients)
    {
        if ($clients instanceof Collection) {
            foreach ($clients as $client) {
                switch ($client->type) {
                    case 'Individual':
                        $client->type = 'Физическое лицо';
                        break;
                    case 'Entity':
                        $client->type = 'Юридическое лицо';
                        break;
                    default:
                        $client->type = 'Неопределено';
                };
            }
        }
        else
        {
            switch ($clients->client) {
                case 'Alert':
                    $client->type = 'Физическое лицо';
                    break;
                case 'Call':
                    $client->type = 'Юридическое лицо';
                    break;
                default:
                    $client->type = 'Неопределено';
            };
        }
        return $clients;
    }
}
