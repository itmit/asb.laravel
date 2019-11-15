<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use YandexCheckout\Client as YandexClient;

class GuardWebController extends Controller
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