<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class ClientController extends ApiBaseController
{
    public $successStatus = 200;

    /**
     * login api
     *
     * @return Response
     */
    public function login()
    {
        
        $user = Client::where('email', '=', request('email'))
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
                'Authorization is succesful');
            }
        }

        return $this->SendError('Authorization error', 'Unauthorised', 401);
    }

    /**
     * Register api
     *
     * @param Request $request
     * @return Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['representative'] = 1;
        $user = Client::create($input);
        $success['token'] = $user->createToken(config('app.name'))->accessToken;
        $success['name'] = $user->name;
        return response()->json(['success' => $success], $this->successStatus);
    }

    /**
     * details api
     *
     * @return Response
     */
    public function details(Request $request)
    {
        $user = Auth::user();
        return response()->json(['success' => $user], $this->successStatus);
    }
}
