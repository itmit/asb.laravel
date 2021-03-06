<?php

namespace App\Http\Controllers\Api;

include_once base_path() . "/app/smsc_api.php";

use App\Models\Client;
use App\Models\User;
use App\Models\Bid;
use App\Models\Payment;
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
use YandexCheckout\Client as YandexClient;

class ClientController extends ApiBaseController
{
    public $successStatus = 200;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'clientType' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), "Validation error", 401);
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
            return $this->sendError($validator->errors()->first(), "Validation error", 401);
        }

        $number = $request['phone_number'];
        $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        $phoneNumberObject = $phoneNumberUtil->parse($number, 'RU');
        $number = $phoneNumberUtil->format($phoneNumberObject, \libphonenumber\PhoneNumberFormat::E164);
        $request['phone_number'] = $number;

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6',
            'phone_number' => 'required|string|min:11|unique:clients,phone_number',
            'name' => 'required|string|min:2',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), "Validation error", 401);
        }

        $asb = User::where('name', '=', 'dispASB')->first(['id']);

        if($asb == NULL)
        {
            return $this->sendError("DB Error", "Validation error", 401);
        }

        $client = Client::create([
            'password' => bcrypt($request['password']),
            'phone_number' => $number,
            'name' => $request['name'],
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
            'organization' => 'required|string|min:2',
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
            'organization' => $request['organization'],
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
            if($user->is_guard == 1)
            {
                return response()->json(['error' => ['ошибка авторизации']], 401);
            }
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
        $userId = Auth::id();

        $guard_enable = Bid::where('status', '=', 'Accepted')->where('guard', '=', $userId)->first();

        $client = auth('api')->user()->toArray();
        $client['is_guard'] = (int) $client['is_guard'];
        $client['is_active'] = (int) $client['is_active'];
        $client['sms_alert'] = (int) $client['sms_alert'];

        if($guard_enable != NULL)
        {
            $client['on_duty'] = 1;
            $client['bid_uuid'] = $guard_enable->uid;
        }

        else
        {
            $client['on_duty'] = 0;
            $client['bid_uuid'] = '';
        }

        // return $this->SendResponse(auth('api')->user()->toArray(), "");
        return $this->SendResponse($client, "");
    }

    public function changePhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'contents' => 'image|mimes:jpeg,jpg,png,gif|required|max:5000',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $path = Storage::putFileAs(
            'public/avatars', $request->file('contents'), auth('api')->user()->id . '.jpg'
        );

        // return $path;

        $user = Client::where('id', '=', auth('api')->user()->id)
            ->update(['user_picture' => 'storage/avatars/' . auth('api')->user()->id . '.jpg']);

        if($user > 0)
        {
            return $this->sendResponse([
                $user
            ],
                'Updated');
        }
        return $this->SendError('Update error', 'Фотография не была загружена', 401);
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
            // usleep(1);
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

    public function setActivityFrom(Request $data)
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

            $client = new YandexClient();
            $client->setAuth(config('app.YANDEX_KASSA_SHOP_ID'), config('app.YANDEX_KASSA_SECRET_KEY'));

            $paymentInfo = $client->createPayment(
                array(
                    'payment_token' => $data->payment_token,
                    'amount' => array(
                        'value' => '1.00',
                        'currency' => 'RUB',
                    ),
                    'capture' => false,
                    'description' => 'Оплата АСБ подписки',

                ),
                uniqid('', true)
            );

            $newPayment = Payment::create([
                'yandex_kassa_id' => $paymentInfo->id,
                'client' => auth('api')->user()->id,
                'status' => $paymentInfo->status,
                'payment_token' => $data->payment_token,
            ]);
        }
        else return $this->SendError('Payment error', 'Данный аккаунт уже оплачен', 401);

        return $paymentInfo->confirmation->confirmation_url;
    }

    public function capturePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'payment_token' => 'required|string'
        ]);

        if ($validator->fails()) { 
            return $this->sendError($validator->errors()->first(), "Validation error", 401); 
        }

        $paymentId = Payment::where('payment_token', '=', $request->payment_token)->latest()->first();

        $paymentId = $paymentId->yandex_kassa_id;

        $client = new YandexClient();
        $client->setAuth(config('app.YANDEX_KASSA_SHOP_ID'), config('app.YANDEX_KASSA_SECRET_KEY'));

        $idempotenceKey = uniqid('', true);
        $response = $client->capturePayment(
            array(
                'amount' => array(
                    'value' => '1.00',
                    'currency' => 'RUB',
                ),
            ),
            $paymentId,
            $idempotenceKey
        );

        if($response->status != 'succeeded'){
            return $this->SendError('Payment error', 'Оплата не удалась', 401);
        }

        $payment_confirm = Payment::where('yandex_kassa_id', '=', $paymentId)->update(['status' => 'succeeded']);
        if ($payment_confirm > 0)
        {
            return $this->activateClient();
        }
        return $this->SendError('Update error', 'Something gone wrong', 401);
    }

    private function activateClient()
    {
        $date = date_create();
        $current_date = date_format($date, 'Y-m-d H:i:s');

        $client_update = Client::where('id', '=', auth('api')->user()->id)
        ->update([
            'is_active' => 1,
            'active_from' => $current_date,
            'sms_alert' => 0
            ]);
        

        if($client_update > 0)
        {
            return $this->sendResponse([
                'active_from' => $current_date,
                'is_active' => 1,
            ],
                'Updated');
        }
        return $this->SendError('Update error', 'Something gone wrong', 401);
    }

    public function activate(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'payment_token' => 'required|string'
        ]);

        if ($validator->fails()) { 
            return $this->sendError($validator->errors()->first(), "Validation error", 401);         
        }

        $payment_confirm = Payment::where('payment_token', '=', $request->payment_token)->update(['status' => 'succeeded']);
        if ($payment_confirm > 0)
        {
            return $this->activateClient();
        }
        return $this->SendError('Update error', 'Something gone wrong', 401);
    }

    public function getPaymentStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'payment_token' => 'required|string'
        ]);

        if ($validator->fails()) { 
            return $this->sendError($validator->errors()->first(), "Validation error", 401);         
        }

        $paymentId = Payment::where('payment_token', '=', $request->payment_token)->latest()->first();

        $paymentId = $paymentId->yandex_kassa_id;

        $client = new YandexClient();
        $client->setAuth(config('app.YANDEX_KASSA_SHOP_ID'), config('app.YANDEX_KASSA_SECRET_KEY'));

        $payment = $client->getPaymentInfo($paymentId);
        $paymentStatus = $payment->status;
        return $this->sendResponse([
            $paymentStatus
        ],
            'payment');
    }

    public function checkDates()
    {   
        $active_clients = Client::whereNotNull('active_from')->get();

        $date = date_create();
        $current_date = date_format($date, 'Y-m-d');

        $current_date_unix = strtotime(date_format($date, 'Y-m-d H:i:s'));

        foreach($active_clients as $active_client)
        {
            $active_client->active_from = strtotime($active_client->active_from);

            // return 'cur: ' . $current_date . ' til: ' . gmdate("Y-m-d", strtotime("+27 day", $active_client->active_from));

            if($current_date == gmdate("Y-m-d", strtotime("+27 day", $active_client->active_from)))
            {
                if($active_client->sms_alert == 0)
                {
                    self::sendNoticeSMS($active_client->phone_number, "notice");
                    $client = Client::where('id', '=', $active_client->id)->update(['sms_alert' => 1]);
                }
            }
            
            if($current_date_unix > strtotime("+30 day", $active_client->active_from))
            {
                self::sendNoticeSMS($active_client->phone_number, "disable");
                self::deactivateClient($active_client->id);
            }
        }

    }

    public function sendNoticeSMS($phone, string $type)
    {
        if($type == "notice")
        {
            send_sms_mail($phone, "Ваша подписка закончится через 3 дня");
            return true;
        }

        if($type == "disable")
        {
            send_sms_mail($phone, "Ваша подписка закончилась");
            return true;
        }
    }

    public function deactivateClient(int $id)
    {
        Client::where('id', '=', $id)->update(['sms_alert' => 0, 'is_active' => 0, 'active_from' => NULL]);
    }

    public function chechClientActiveStatus(int $id)
    {
        $client = auth('api')->user()->toArray();

        $response = [
            'is_active' => $client['is_active'],
            'active_from' => $client['is_active'],
        ];

        // return $this->SendResponse(auth('api')->user()->toArray(), "");
        return $this->SendResponse($response, "");
    }
}
