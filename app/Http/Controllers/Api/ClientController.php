<?php

namespace App\Http\Controllers\Api;

include_once base_path() . "/app/smsc_api.php";

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\smsc_api;

class ClientController extends ApiBaseController
{
    public $successStatus = 200;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'clientType' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
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
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|min:11',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

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
            return response()->json(['error' => $validator->errors()], 401);
        }

        $asb = User::where('name', '=', 'dispASB')->first(['id']);

        if($asb == NULL)
        {
            return response()->json(['error'], 401);
        }

        $client = Client::create([
            'password' => bcrypt($request['password']),
            'phone_number' => $number,
            'representative' => $asb->id,
            'type' => 'Individual',
            'is_active' => 0
        ]);

        Auth::login($client);     

        if (Auth::check()) {
            $tokenResult = $client->createToken(config('app.name'));
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
                'Authorization is successful');
        }
        
        return $this->SendError('Authorization error', 'Unauthorised', 401);
    }

    public function storeEntity($request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|min:11',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $number = $request['phone_number'];
        $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        $phoneNumberObject = $phoneNumberUtil->parse($number, 'RU');
        $number = $phoneNumberUtil->format($phoneNumberObject, \libphonenumber\PhoneNumberFormat::E164);
        $request['phone_number'] = $number;

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6',
            'phone_number' => 'required|string|min:11|unique:clients,phone_number',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $asb = User::where('name', '=', 'dispASB')->first(['id']);

        if($asb == NULL)
        {
            return response()->json(['error'], 401);
        }

        $client = Client::create([
            'password' => bcrypt($request['password']),
            'phone_number' => $number,
            'representative' => $asb->id,
            'type' => 'Entity',
            'is_active' => 0
        ]);

        Auth::login($client);     

        if (Auth::check()) {
            $tokenResult = $client->createToken(config('app.name'));
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
                'Authorization is successful');
        }
        
        return $this->SendError('Authorization error', 'Unauthorised', 401);
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

        // DB::enableQueryLog();

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

    public function edit(Request $request)
    {
        $client = Client::where('id', '=', auth('api')->user()->id)
            ->update([
                'name' => $request->name,
                'passport' => $request->passport,
                'email' => $request->email,
                'organization' => $request->organization,
                'INN' => $request->INN,
                'OGRN' => $request->OGRN,
                'director' => $request->director,
                ]);

        if($client > 0)
        {
            return $this->sendResponse([
                $client
            ],
                'Updated');
        }
        return $this->SendError('Update error', 'Something gone wrong', 401);
    }

    public function setActivityFrom()
    {
        $active_from = Client::where('id', '=', auth('api')->user()->id)->first(['active_from']);
        $active_from_unix = strtotime($active_from->active_from);

        $date = date_create();
        $current_date = date_format($date, 'Y-m-d');

        // return 'cur: ' . $current_date . ' active from: ' . gmdate("Y-m-d", strtotime($active_from->active_from)) . ' active til: ' . gmdate("Y-m-d", strtotime("+30 day",$active_from_unix));

        if($active_from->active_from == NULL || gmdate("Y-m-d", strtotime("+30 day", $active_from_unix)) <= $current_date)
        {
            $date = date_create();
            $current_date = date_format($date, 'Y-m-d H:i:s');

            $client = Client::where('id', '=', auth('api')->user()->id)
            ->update([
                'is_active' => 1,
                'active_from' => $current_date
                ]);
            return 'payment access';
        }
        else return 'payment deniend. Cur: ' . $current_date . ' active til: ' . gmdate("Y-m-d", strtotime("+30 day", $active_from_unix));

        if($client > 0)
        {
            return $this->sendResponse([
                $client
            ],
                'Updated');
        }
        return $this->SendError('Update error', 'Something gone wrong', 401);
    }

    public function sendSMS()
    {
        // return send_sms("79997913230", "Ваш пароль: 123", 1, 0, 0, 0, "SMSC.RU");
        // return send_sms_mail("79997913230", "Ваш пароль: 123");
    }

    public function checkDates()
    {   
        $active_clients = Client::whereNotNull('active_from')->get();

        $date = date_create();
        $current_date = date_format($date, 'Y-m-d');

        $current_date_unix = date_format($date, 'Y-m-d H:i:s');

        foreach($active_clients as $active_client)
        {
            $active_client->active_from = strtotime($active_client->active_from);

            // return 'cur: ' . $current_date . ' til: ' . gmdate("Y-m-d", strtotime("+27 day", $active_client->active_from));

            if($current_date == gmdate("Y-m-d", strtotime("+27 day", $active_client->active_from)))
            {
                self::sendNoticeSMS($active_client->phone_number);
            }

            if($current_date_unix > strtotime("+30 day", $active_client->active_from))
            {
                return 'time to disable';
            }
        }

    }

    public function sendNoticeSMS($phone)
    {
        send_sms_mail($phone, "Ваша подписка закончится через 3 дня");
        return true;
    }
}
