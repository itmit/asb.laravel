<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
    {{-- <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDWrxs1776GRtqwZddV5ImvjIfDQfLv_qk&callback=initMap"
    type="text/javascript"></script> --}}
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/script.js') }}"></script>
    <script src="{{ asset('js/jquery.mask.js') }}"></script>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div id="app">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse" aria-expanded="false">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    Охранное предприятие АСБ
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    &nbsp;
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @guest
                        <li><a href="{{ route('login') }}">Войти</a></li>
                        <li><a href="{{ route('register') }}">Регистрация</a></li>
                    @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>

                            </a>

                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        Выйти?
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <div class="container" style="width: 90% !important">
        <div class="row">
            <div class="col-sm-3 left-menu">
                <ul class="nav">

                    <li name="home" class="active"><a href="{{ route('auth.home') }}">Главная</a></li>

                    @ability('super-admin', 'show-representatives')
                    <li name="representative"><a href="{{ route('auth.representative.index') }}">Представители</a></li>
                    @endability

                    @ability('super-admin,representative', 'show-dispatchers')
                    <li name="dispatcher"><a href="{{ route('auth.dispatcher.index') }}">Диспетчеры</a></li>                    
                    @endability

                    <li name="bid"><a href="{{ route('auth.bid.index') }}">Заявки</a></li>

                    <li name="client"><a href="{{ route('auth.client.index') }}">Клиенты</a></li>
                </ul>
            </div>
            <div class="col-sm-9 tabs-content">
                @yield('content')
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="myModal">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="modal-title">Имеются необработанные тревоги!</h2>
        </div>
        <div class="modal-body">
          <p class="modal-text"></p>
        </div>
      </div>
    </div>
</div>

<!-- Scripts -->

<script>
    $(document).ready(function() {

        // let pathname = window.location.pathname;

        // switch(pathname) {
        // case '/':
        //     $( "li[name='home']" ).addClass( "active" );
        //     break;

        // case '/representative':
        //     $( "li[name='representative']" ).addClass( "active" );
        //     break;

        // case '/dispatcher':
        //     $( "li[name='dispatcher']" ).addClass( "active" );
        //     break;

        // case '/bid':
        //     $( "li[name='bid']" ).addClass( "active" );
        //     break;

        // case '/client':
        //     $( "li[name='client']" ).addClass( "active" );
        //     break;
        // }

        $(document).on('click', '.js-destroy-button', function() {
            let ids = [];

            $(".js-destroy:checked").each(function(){
                ids.push($(this).data('placeId'));
            });
            
            // console.log(ids);

            let uSure = confirm('Вы действительно хотите удалить?');
            if(uSure)
            {
                $.ajax({
                headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                dataType: "json",
                data    : { ids: ids },
                url     : 'clients/delete',
                method    : 'delete',
                success: function (response) {
                    $(".js-destroy:checked").closest('tr').remove();
                    $(".js-destroy").prop("checked", "");
                },
                error: function (xhr, err) { 
                    console.log("Error: " + xhr + " " + err);
                    alert('Клиент не был удален, т.к. у него есть тревоги')
                }
            });
            }
        });

        $(document).on('click', '.js-destroy-button-dispatcher', function() {
            let ids = [];

            $(".js-destroy:checked").each(function(){
                ids.push($(this).data('placeId'));
            });

            let uSure = confirm('Вы действительно хотите удалить?');
            if(uSure)
            {
                $.ajax({
                headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                dataType: "json",
                data    : { ids: ids },
                url     : 'dispatcher/delete',
                method    : 'delete',
                success: function (response) {
                    // console.log(response);
                    $(".js-destroy:checked").closest('tr').remove();
                    $(".js-destroy").prop("checked", "");
                },
                error: function (xhr, err) { 
                    console.log("Error: " + xhr + " " + err);
                }
            });
            }
        });

        $(document).on('click', '.js-destroy-button-guard', function() {
            let ids = [];

            $(".js-destroy:checked").each(function(){
                ids.push($(this).data('placeId'));
            });
            
            // console.log(ids);

            let uSure = confirm('Вы действительно хотите удалить?');
            if(uSure)
            {
                $.ajax({
                headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                dataType: "json",
                data    : { ids: ids },
                url     : 'guard/delete',
                method    : 'delete',
                success: function (response) {
                    // console.log(response);
                    $(".js-destroy:checked").closest('tr').remove();
                    $(".js-destroy").prop("checked", "");
                },
                error: function (xhr, err) { 
                    alert('ГБР не был удален!')
                    console.log("Error: " + xhr + " " + err);
                }
            });
            // console.log('sss');
            }
        });
        
        $(function(){
            $(".js-destroy-all").on("click", function() {

                if($(".js-destroy-all").prop("checked")){
                    $(".js-destroy").prop("checked", "checked");
                    // console.log('check');
                }
                else{
                    $(".js-destroy").prop("checked", "");
                }

            });
        });

        let openModal = 0;
        var pathname = window.location.pathname;
        let urlcheck = /\/bid\/\d+$/.test(pathname); 
        if(pathname != '/bid')
        {
            if(urlcheck != true)
            {
                $mapInit = 0;
                setInterval(function(){ 
                        $.ajax({
                            headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            dataType: "json",
                            url     : '../bid/alarmSound',
                            method    : 'post',
                            success: function (response) {
                                if(response.length != 0)
                                {
                                    if(openModal == 0)
                                    {
                                        $('#myModal').modal('toggle');
                                        $('#myModal').modal({
                                        backdrop: 'static',
                                        keyboard: false
                                        })
                                        if(response.length == 1)
                                        {
                                            $.each(response, function(k, v) {
                                                if(v.client.name == null)
                                                {
                                                    $('.modal-text').html('<div><a href="../bid/' + v.id + '">Новая активная тревога!</a> Клиент: <a href="../client/' + v.client.id + '">' + v.client.organization + '</a> Дата создания: ' + v.created_at + ' телефон: '+ v.client.phone_number +'</div>');
                                                }
                                                if(v.client.name != null)
                                                {
                                                    $('.modal-text').html('<div><a href="../bid/' + v.id + '">Новая активная тревога!</a> Клиент: <a href="../client/' + v.client.id + '">' + v.client.name + '</a> Дата создания: ' + v.created_at + ' телефон: '+ v.client.phone_number +'</div>');
                                                }
                                            });
                                            $('.modal-text').append('<div id="map" style="width: 600px; height: 400px"></div>');
                                        }
                                        else
                                        {
                                            $.each(response, function(k, v) {
                                                if(v.client.name == null)
                                                {
                                                    $('.modal-text').append('<div><a href="../bid/' + v.id + '">Активная тревога!</a> Клиент: <a href="../client/' + v.client.id + '">' + v.client.organization + '</a> Дата создания: ' + v.created_at + ' телефон: '+ v.client.phone_number +'</div><hr>');
                                                }
                                                if(v.client.name != null)
                                                {
                                                    $('.modal-text').append('<div><a href="../bid/' + v.id + '">Активная тревога!</a> Клиент: <a href="../client/' + v.client.id + '">' + v.client.name + '</a> Дата создания: ' + v.created_at + ' телефон: '+ v.client.phone_number +'</div><hr>');
                                                }
                                            });
                                        }
                                        openModal = 1;
                                    }
                                    if(openModal == 1)
                                    {
                                        if(response.length == 1)
                                        {

                                            $.each(response, function(k, v) {
                                                if(v.client.name == null)
                                                {
                                                    $('.modal-text').html('<div><a href="../bid/' + v.id + '">Новая активная тревога!</a> Клиент: <a href="../client/' + v.client.id + '">' + v.client.organization + '</a> Дата создания: ' + v.created_at + ' телефон: '+ v.client.phone_number +'</div>');
                                                }
                                                if(v.client.name != null)
                                                {
                                                    $('.modal-text').html('<div><a href="../bid/' + v.id + '">Новая активная тревога!</a> Клиент: <a href="../client/' + v.client.id + '">' + v.client.name + '</a> Дата создания: ' + v.created_at + ' телефон: '+ v.client.phone_number +'</div>');
                                                }

                                                $('.modal-text').append('<div id="map" style="width: 600px; height: 400px"></div>'); 

                                                ymaps.ready(init);

                                                function init() {
                                                    
                                                    // Создание карты.
                                                    myMap = new ymaps.Map("map", {
                                                        // Координаты центра карты.
                                                        // Порядок по умолчанию: «широта, долгота».
                                                        // Чтобы не определять координаты центра карты вручную,
                                                        // воспользуйтесь инструментом Определение координат.
                                                        center: [v.location.latitude, v.location.longitude],
                                                        // Уровень масштабирования. Допустимые значения:
                                                        // от 0 (весь мир) до 19.
                                                        zoom: 15
                                                    });

                                                
                                                    let placeMark = new ymaps.Placemark([v.location.latitude, v.location.longitude]);
                                                        myMap.geoObjects
                                                        .add(placeMark);
                                                }
                                            });
                                            
                                        }
                                        else
                                        {
                                            $('.modal-text').html('');
                                            $.each(response, function(k, v) {
                                                if(v.client.name == null)
                                                {
                                                    $('.modal-text').append('<div><a href="../bid/' + v.id + '">Активная тревога!</a> Клиент: <a href="../client/' + v.client.id + '">' + v.client.organization + '</a> Дата создания: ' + v.created_at + ' телефон: '+ v.client.phone_number +'</div><hr>');
                                                }
                                                if(v.client.name != null)
                                                {
                                                    $('.modal-text').append('<div><a href="../bid/' + v.id + '">Активная тревога!</a> Клиент: <a href="../client/' + v.client.id + '">' + v.client.name + '</a> Дата создания: ' + v.created_at + ' телефон: '+ v.client.phone_number +'</div><hr>');
                                                }
                                                
                                            });
                                        }
                                    }

                                    let audio = new Audio(location.origin + '/alert.mp3'); 
                                    audio.pause();
                                    audio.play();
                                    
                                }
                                if(response.length == 0)
                                {
                                    openModal = 0;
                                    $('#myModal').modal('hide');
                                }
                                
                            },
                            error: function (xhr, err) { 
                                console.log("Error: " + xhr + " " + err);
                            }
                        })
                }, 12000);
            };
        };
    });


    $(document).ready(function () {
        $('input[type=tel]').mask("+7 (999) 999-99-99");
    });

</script>
</body>
</html>
