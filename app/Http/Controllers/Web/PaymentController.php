<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use YandexCheckout\Client as YandexClient;

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
            "paymentInfo" => $paymentInfo
        ]);
    }
}