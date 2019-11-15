<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use YandexCheckout\Client as YandexClient;
use Models\Client;

class PaymentController extends Controller
{
    public function index() 
    {
        $client = new YandexClient();
        $client->setAuth(config('app.YANDEX_KASSA_SHOP_ID'), config('app.YANDEX_KASSA_SECRET_KEY'));

        $paymentInfo = $client->createPayment(
        array(
            'amount' => array(
                'value' => 1,
                'currency' => 'RUB',
            ),
            'confirmation' => array(
                'type' => 'embedded'
            ),
            'capture' => true,
            'description' => 'Заказ №72',
            ),
            uniqid('', true)
        );

        return view('payment', [
            "paymentInfo" => $paymentInfo,
            "user" => auth('api')->user()->id
        ]);
    }

    public function showSuccess(Request $request) 
    {
        if ($request->input('status') == "Success") {
            
            $date = date_create();
            $current_date = date_format($date, 'Y-m-d H:i:s');

            Client::where('id', '=', $request->input('user'))->update([
                'is_active' => 1,
                'active_from' => $current_date,
                'sms_alert' => 0
                ]);

            return "Success";
        }
    }
}