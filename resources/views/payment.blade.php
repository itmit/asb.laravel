<!--Подключение библиотеки-->
<script src="https://kassa.yandex.ru/checkout-ui/v2.js"></script>

<!--HTML-элемент, в котором будет отображаться платежная форма-->
<div id="payment-form"></div>

<script>
//Инициализация виджета. Все параметры обязательные.
const checkout = new window.YandexCheckout({
    confirmation_token: '{{ $paymentInfo->confirmation->confirmation_token }}', //Токен, который перед проведением оплаты нужно получить от Яндекс.Кассы
    return_url: 'http://asb.itmit-studio.ru/api/paymentSuccess?user={{ $user }}', //Ссылка на страницу завершения оплаты
    error_callback(error) {
        //Обработка ошибок инициализации
    }
});

//Отображение платежной форме в заданном элементе
checkout.render('payment-form');
</script>