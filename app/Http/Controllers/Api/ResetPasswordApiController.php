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
    public function resetPassword(Request $request)
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
            return 'error';
        }

        $code = random_int(1000, 9999);

        $client = Client::where('phone_number', '=', $request['phone_number'])
            ->update(['hash' => password_hash($code, PASSWORD_BCRYPT)]);

        if($client != 0)
        {
            return 'suc ' . $code;
        }
        else return 'err';
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
            return 'error';
        }

        $hashCode = password_hash($request['secret_code'], PASSWORD_BCRYPT);

        if (Hash::check($hashCode, $client->hash))
        {
            return 'equal';
        }
        else return 'not equal';

        // $checkCode = Client::where('id', '=', $client->id)->where('hash', '=')->first();
    }
}

// 1379