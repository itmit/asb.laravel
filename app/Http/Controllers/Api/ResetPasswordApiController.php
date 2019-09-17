<?php

namespace App\Http\Controllers\Api;

include_once base_path() . "/app/smsc_api.php";

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ResetPasswordApiController extends ApiBaseController
{
    public function resetPassword()
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

        return 'code: ' . $code . ' hash: ' . password_hash($code);
    }
}

// 1379