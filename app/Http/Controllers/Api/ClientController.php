<?php

namespace App\Http\Controllers\Api;

use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ClientController extends ApiBaseController
{
    public $successStatus = 200;

    public function register(Request $request)
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
        return 'ind';
        $number = $request['phone_number'];
        $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        $phoneNumberObject = $phoneNumberUtil->parse($number, 'RU');
        $number = $phoneNumberUtil->format($phoneNumberObject, \libphonenumber\PhoneNumberFormat::E164);
        $request['phone_number'] = $number;

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6',
            'phone_number' => 'required|string|min:11|unique:clients,phone_number'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('auth.client.create')
                ->withErrors($validator)
                ->withInput();
        }

        Client::create([
            'password' => bcrypt($request['password']),
            'phone_number' => $number,
            // 'representative' => $request['representative'],
            'type' => 'Individual',
            'is_active' => 0
        ]);

        return redirect()->route('auth.client.index');
    }

    public function storeEntity($request)
    {
        return 'ent';
        $number = $request['ent_phone_number'];
        $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        $phoneNumberObject = $phoneNumberUtil->parse($number, 'RU');
        $number = $phoneNumberUtil->format($phoneNumberObject, \libphonenumber\PhoneNumberFormat::E164);
        $request['ent_phone_number'] = $number;

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6',
            'phone_number' => 'required|string|min:11|unique:clients,phone_number',
            'representative' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('auth.client.create')
                ->withErrors($validator)
                ->withInput();
        }

        $client = Client::create([
            'password' => bcrypt($request['password']),
            'phone_number' => $number,
            // 'representative' => $request['representative'],
            'type' => 'Entity',
            'is_active' => 0
        ]);

        return redirect()->route('auth.client.index');
    }

    /**
     * login api
     *
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phoneNumber' => 'required',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $number = request('phoneNumber');
        $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        $phoneNumberObject = $phoneNumberUtil->parse($number, 'RU');
        $number = $phoneNumberUtil->format($phoneNumberObject, \libphonenumber\PhoneNumberFormat::E164);

        $user = Client::where('phone_number', '=', $number)
            ->get()->first();
        
        if ($user != null) {
            if (Hash::check(request('password'), $user->password))
            {
                Auth::login($user);
            }

            if (Auth::check()) {
                $tokenResult = $user->createToken(config('app.name'));
                $token = $tokenResult->token;
                $token->expires_at = Carbon::now()->addWeeks(1);
                $token->save();

                return $this->sendResponse([
                    'access_token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'expires_at' => Carbon::parse(
                        $tokenResult->token->expires_at
                    )->toDateTimeString()
                ],
                    "Authorization is successful");
            }
        }

        return $this->SendError('Authorization error', "Uncorrect login or password (login is with number: '$number')", 401);
    }

    /**
     * details api
     *
     * @return JsonResponse
     */
    public function details()
    {
        return $this->SendResponse(auth('api')->user()->toArray(), "");
    }

    public function changePhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'contents' => 'image|mimes:jpeg,jpg,png,gif|required|max:10000',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $path = Storage::putFileAs(
            'public/avatars', $request->file('contents'), auth('api')->user()->id . '.jpg'
        );

        $user = Client::where('id', '=', auth('api')->user()->id)
            ->update(['user_picture' => 'storage/avatars/' . auth('api')->user()->id . '.jpg']);

        if($user > 0)
        {
            return $this->sendResponse([
                $user
            ],
                'Updated');
        }
        return $this->SendError('Update error', 'Something gone wrong', 401);
    }

    public function note(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'note' => 'required|string|max:10000',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $user = Client::where('id', '=', auth('api')->user()->id)
            ->update(['note' => $request->note]);

        if($user > 0)
        {
            return $this->sendResponse([
                $user
            ],
                'Updated');
        }
        return $this->SendError('Update error', 'Something gone wrong', 401);
    }

    public function updateCurrentLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        DB::enableQueryLog();

        // $client = Client::where('id', '=', auth('api')->user()->id)
        //     ->update(['latitude' => $request->latitude, 'longitude' => $request->longitude]);

        $latitude = $request->latitude;
        $longitude = $request->longitude;

        DB::beginTransaction();
            $record = Client::where('id', '=', auth('api')->user()->id)->lockForUpdate()->first();
            usleep(1);
            $record->latitude = $latitude;
            $record->longitude = $longitude;
            $record->save();
        DB::commit();
    
        // return ['success' => true];

        // $client = DB::table("clients")
        //     ->where('id', auth('api')->user()->id)
        //     ->update(['latitude' => $request->latitude, 'longitude' => $request->longitude]);

        if($record->count() > 0)
        {
            return $this->sendResponse([
                $record, DB::getQueryLog()
            ], 'Updated');
        }
        return $this->SendError('Update error', [auth('api')->user()->id, $request->all(), DB::getQueryLog()], 401);
    }
    
}
