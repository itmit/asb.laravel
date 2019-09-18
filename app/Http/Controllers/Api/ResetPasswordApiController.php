<?php

namespace App\Http\Controllers\Api;

include_once base_path() . "/app/smsc_api.php";

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordApiController extends ApiBaseController
{
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'phone_number' => 'required|string',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $number = $request['phone_number'];
        $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        $phoneNumberObject = $phoneNumberUtil->parse($number, 'RU');
        $number = $phoneNumberUtil->format($phoneNumberObject, \libphonenumber\PhoneNumberFormat::E164);
        $request['phone_number'] = $number;

        $client = Client::where('phone_number', '=', $request['phone_number'])->first();

        if($client == NULL)
        {
            return $this->SendError('Client error', 'Client doesnot exist', 401);
        }

        $code = random_int(1000, 9999);

        $client = Client::where('phone_number', '=', $request['phone_number'])
            ->update(['hash' => Hash::make($code)]);

        if($client != 0)
        {
            send_sms_mail($request['phone_number'], "Код для восстановления пароля: " . $code);
            return $this->sendResponse([],
                'Code was generated');
        }
        else return $this->SendError('DB error', 'Something gone wrong', 401);
        // return 'code: ' . $code . ' hash: ' . password_hash($code, PASSWORD_BCRYPT);
    }

    public function checkCode(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'secret_code' => 'required|string',
            'phone_number' => 'required|string',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $number = $request['phone_number'];
        $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        $phoneNumberObject = $phoneNumberUtil->parse($number, 'RU');
        $number = $phoneNumberUtil->format($phoneNumberObject, \libphonenumber\PhoneNumberFormat::E164);
        $request['phone_number'] = $number;

        $client = Client::where('phone_number', '=', $request['phone_number'])->first();

        if($client == NULL)
        {
            return $this->SendError('Client error', 'Client doesnot exist', 401);
        }

        if (Hash::check($request['secret_code'], $client->hash))
        {
            return $this->sendResponse([],
                'Code confirmed');
        }
        else return $this->SendError('Code error', 'Invalid code', 401);

        // $checkCode = Client::where('id', '=', $client->id)->where('hash', '=')->first();
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'secret_code' => 'required|string',
            'phone_number' => 'required|string',
            'new_password' => 'required|string',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $number = $request['phone_number'];
        $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        $phoneNumberObject = $phoneNumberUtil->parse($number, 'RU');
        $number = $phoneNumberUtil->format($phoneNumberObject, \libphonenumber\PhoneNumberFormat::E164);
        $request['phone_number'] = $number;

        $client = Client::where('phone_number', '=', $request['phone_number'])->first();

        if($client == NULL)
        {
            return $this->SendError('Client error', 'Client doesnot exist', 401);
        }

        if (Hash::check($request['secret_code'], $client->hash))
        {
            $client = Client::where('phone_number', '=', $request['phone_number'])
                ->update(['password' => Hash::make($request['new_password'])]);

            if($client > 0)
            {
                return $this->sendResponse([
                    $client
                ],
                    'Updated');
            }
        }
        else return $this->SendError('Code error', 'Invalid code', 401);
    }
}

// 1379