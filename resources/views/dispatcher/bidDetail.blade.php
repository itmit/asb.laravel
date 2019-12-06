@extends('layouts.bidDetailApp')

@section('content')
    <h1 data-bidid="{{ $bid->id }}">Заявка {{ $bid->id }}</h1>
    <div class="col-sm-12">
        <a href="/bid">Назад</a>
    </div>
    <div class="col-sm-12">
        <div data-bidstatus = "{{ $bid->status }}" class="bidstatus">
            Статус: {{ $bid->status }} 
            @ability('super-admin,dispatcher', 'close-bid')
                @if($bid->status != 'Выполнена')
                    <input type="submit" value="Закрыть заявку" data-bidid="{{ $bid->id }}" class="close-bid btn btn-primary">
                @endif
            @endability
        </div>
        <div>
            Создана: {{ date('H:i:s d.m.Y', strtotime($bid->created_at->timezone('Europe/Moscow'))) }}
        </div>
        <div class="updated">
            Обновлена: {{ date('H:i:s d.m.Y', strtotime($bid->client()->location()->created_at->timezone('Europe/Moscow'))) }}
        </div>
        <div>
            Тип: {{ $bid->type }}
        </div>
        <div>
            Клиент: <a href="../client/{{ $bid->client()->id }}">{{ $bid->client()->name ??  $bid->client()->organization}}</a>
        </div>
        <div>
            Телефон: {{ $bid->client()->phone_number }}
        </div>
        @if($bid->status != 'Ожидает принятия')
            @if($guard != NULL)
        <div id="guard" data-guardlongitude="{{ $guard->longitude }}" data-guardlatitude="{{ $guard->latitude }}">
            Заявку принял: <a href="../guard/{{ $bid->guard }}">ГБР {{ $guard->name }}</a>
        </div>
            @else
            <div id="guard">
                Заявка закрыта диспетчером
            </div>
            @endif
        @endif
        <div class="js-location" data-longitude="{{ $bid->latitude }}" data-latitude="{{ $bid->longitude }}">
            Координаты: {{ $bid->latitude }} | {{ $bid->longitude }}
        </div>
        <div id="map" style="width: 600px; height: 400px"></div>
    </div>
    <script>

    $(document).ready(function()
        {
            document.title='ASB';

            let $bidStatus = $('.bidstatus');

            // Функция ymaps.ready() будет вызвана, когда
            // загрузятся все компоненты API, а также когда будет готово DOM-дерево.
            ymaps.ready(init);

            function init() {
                let $locations = $('.js-location');
                // Создание карты.
                myMap = new ymaps.Map("map", {
                    // Координаты центра карты.
                    // Порядок по умолчанию: «широта, долгота».
                    // Чтобы не определять координаты центра карты вручную,
                    // воспользуйтесь инструментом Определение координат.
                    center: [$locations.first().data('longitude'), $locations.first().data('latitude')],
                    // Уровень масштабирования. Допустимые значения:
                    // от 0 (весь мир) до 19.
                    zoom: 15
                });

                $locations.each(function () {
                    let placeMark = new ymaps.Placemark([$(this).data('longitude'), $(this).data('latitude')]);

                    if($bidStatus.data('bidstatus') == 'Принята')
                    {

                        let placeMarkGuard = new ymaps.Placemark([$('#guard').data('guardlongitude'), $('#guard').data('guardlatitude')], {}, {
                            // preset: "islands#circleDotIcon",
                            // iconColor: '#ff0000',
                            // Необходимо указать данный тип макета.
                            iconLayout: 'default#image',
                            // Своё изображение иконки метки.
                            iconImageHref: '../caricon.png',
                            // Размеры метки.
                            iconImageSize: [40, 35],
                            // Смещение левого верхнего угла иконки относительно
                            // её "ножки" (точки привязки).
                            iconImageOffset: [0, 0]
                        });

                        myMap.geoObjects
                            .add(placeMark)
                            .add(placeMarkGuard);
                    }
                    else
                    {
                        myMap.geoObjects
                        .add(placeMark);
                    }
                });
            }

            function timer()
            {
                let bidid = $('h1').data('bidid');
                timer = setInterval(function()
                { 
                    $.ajax({
                        headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        dataType: "json",
                        data: {bidid: bidid, bidStatus: $bidStatus.data('bidstatus')},
                        url     : '../bid/updateCoordinates',
                        method    : 'post',
                        success: function (response) {
                            $('.updated').html('Обновлена: ' + response['location']['last_checkpoint']);
                            $('.js-location').html('Координаты: ' + response['location']['latitude'] + ' | ' +  response['location']['longitude']);
                            $('.js-location').data('longitude', response['location']['longitude']);
                            $('.js-location').data('latitude', response['location']['latitude']);
                            myMap.geoObjects.removeAll()
                            MyIconContentLayout = ymaps.templateLayoutFactory.createClass(
                            '<div style="color: #FFFFFF; font-weight: bold;">$[properties.iconContent]</div>'
                            );
                            let placeMark = new ymaps.Placemark([response['location']['latitude'], response['location']['longitude']]);
                            if($bidStatus.data('bidstatus') == 'Принята')
                            {
                                let placeMarkGuard = new ymaps.Placemark([response['guard']['guard_latitude'], response['guard']['guard_longitude']], {}, {
                                    iconLayout: 'default#image',
                                    iconImageHref: '../caricon.png',
                                    iconImageSize: [40, 35],
                                    iconImageOffset: [-20, -17.5]
                                });
                                myMap.geoObjects
                                    .add(placeMark)
                                    .add(placeMarkGuard);
                            }
                            else
                            {
                                myMap.geoObjects
                                .add(placeMark);
                            }
                        },
                        error: function (xhr, err) { 
                            console.log("Error: " + xhr + " " + err);
                        }
                    });
                }, 10000);
            }

            timer();

            $(window).focus(function() {
                
                document.title='ASB';

                if($bidStatus.data('bidstatus') == 'Ожидает принятия' || $bidStatus.data('bidstatus') == 'Принята')
                {
                    let bidid = $('h1').data('bidid');
                    timer = setInterval(function()
                    { 
                        $.ajax({
                            headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            dataType: "json",
                            data: {bidid: bidid, bidStatus: $bidStatus.data('bidstatus')},
                            url     : '../bid/updateCoordinates',
                            method    : 'post',
                            success: function (response) {
                                $('.updated').html('Обновлена: ' + response['location']['last_checkpoint']);
                                $('.js-location').html('Координаты: ' + response['location']['latitude'] + ' | ' +  response['location']['longitude']);
                                $('.js-location').data('longitude', response['location']['longitude']);
                                $('.js-location').data('latitude', response['location']['latitude']);
                                myMap.geoObjects.removeAll()
                                MyIconContentLayout = ymaps.templateLayoutFactory.createClass(
                                '<div style="color: #FFFFFF; font-weight: bold;">$[properties.iconContent]</div>'
                                );
                                let placeMark = new ymaps.Placemark([response['location']['latitude'], response['location']['longitude']]);
                                if($bidStatus.data('bidstatus') == 'Принята')
                                {
                                    let placeMarkGuard = new ymaps.Placemark([response['guard']['guard_latitude'], response['guard']['guard_longitude']], {}, {
                                        iconLayout: 'default#image',
                                        iconImageHref: '../caricon.png',
                                        iconImageSize: [40, 35],
                                        iconImageOffset: [-20, -17.5]
                                    });
                                    myMap.geoObjects
                                        .add(placeMark)
                                        .add(placeMarkGuard);
                                }
                                else
                                {
                                    myMap.geoObjects
                                    .add(placeMark);
                                }
                            },
                            error: function (xhr, err) { 
                                console.log("Error: " + xhr + " " + err);
                            }
                        });
                    }, 10000);
                }

            }); //Во вкладке
            $(window).blur(function() {
                document.title='ASB (не обновляется)';
                clearInterval(timer);
            }); //Покинули вкладку
            
            $('.close-bid').click(function (e) {
                let bidid = $(this).data('bidid');
                console.log(bidid);
                $.ajax({
                    headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    dataType: "html",
                    data: {bidid: bidid},
                    url     : 'closeByUser',
                    method    : 'post',
                    success: function (response) {
                        location.reload();
                    },
                    error: function (xhr, err) { 
                        console.log(err + " " + xhr);
                    }
                })

            })

        })

    </script>
@endsection