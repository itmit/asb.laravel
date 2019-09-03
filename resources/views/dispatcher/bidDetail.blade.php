@extends('layouts.profileApp')

@section('content')
    <h1 data-bidid="{{ $bid->id }}">Заявка {{ $bid->id }}</h1>
    <div class="col-sm-12">
        <a href="{{ url()->previous() }}">Назад</a>
    </div>
    <div class="col-sm-12">
        <div data-bidstatus = "{{ $bid->status }}" class="bidstatus">
            Статус: {{ $bid->status }}
            {{-- <select id="changeBidStatus" name="changeBidStatus">
                <option value="Accepted">Accepted</option>
                <option value="PendingAcceptance">PendingAcceptance</option>
                <option value="Processed">Processed</option>
            </select> --}}
        </div>
        <div>
            Создана: {{ $bid->created_at->timezone('Europe/Moscow') }}
        </div>
        <div class="updated">
            Обновлена: {{ $bid->updated_at->timezone('Europe/Moscow') }}
        </div>
        <div>
            Тип: {{ $bid->type }}
        </div>
        <div>
            Клиент: <a href="../client/{{ $bid->location()->client()->id }}">{{ $bid->location()->client()->name }}</a>
        </div>
        @if($bid->status != 'Ожидает принятия')
        <div id="guard" data-guardlongitude="{{ $guard->longitude }}" data-guardlatitude="{{ $guard->latitude }}">
            Заявку принял: <a href="../guard/{{ $bid->guard }}">Экипаж {{ $guard->name }}</a>
        </div>
        @endif
        <div class="js-location" data-longitude="{{ $bid->location()->latitude }}" data-latitude="{{ $bid->location()->longitude }}">
            Координаты: {{ $bid->location()->latitude }} | {{ $bid->location()->longitude }}
        </div>
        <div id="map" style="width: 600px; height: 400px"></div>
    </div>
    <script>
    

    $(document).ready(function()
        {
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

                    console.log([$('.guard').data('guardlongitude'));

                    let placeMarkGuard = new ymaps.Placemark([$('.guard').data('guardlongitude'), $('.guard').data('guardlatitude')], {}, {
                        // preset: "islands#circleDotIcon",
                        // iconColor: '#ff0000',
                        // Необходимо указать данный тип макета.
                        iconLayout: 'default#image',
                        // Своё изображение иконки метки.
                        iconImageHref: '../storage/caricon.png',
                        // Размеры метки.
                        iconImageSize: [40, 35],
                        // Смещение левого верхнего угла иконки относительно
                        // её "ножки" (точки привязки).
                        iconImageOffset: [0, 0]
                    });

                    myMap.geoObjects
                        .add(placeMark)
                        .add(placeMarkGuard);
                });
            }

            let $bidStatus = $('.bidstatus');

            if($bidStatus.data('bidstatus') == 'Ожидает принятия' || $bidStatus.data('bidstatus') == 'Принята')
            {
                let bidid = $('h1').data('bidid');
                setInterval(function()
                { 
                    $.ajax({
                        headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        dataType: "json",
                        data: {bidid: bidid},
                        url     : '../bid/updateCoordinates',
                        method    : 'post',
                        success: function (response) {
                            $('.updated').html('Обновлена: ' + response['updated_at']);
                            $('.js-location').html('Координаты: ' + response['location']['latitude'] + ' | ' +  response['location']['longitude']);
                            $('.js-location').data('longitude', response['location']['longitude']);
                            $('.js-location').data('latitude', response['location']['latitude']);

                            myMap.geoObjects.removeAll()

                            MyIconContentLayout = ymaps.templateLayoutFactory.createClass(
                            '<div style="color: #FFFFFF; font-weight: bold;">$[properties.iconContent]</div>'
                            );

                            let placeMark = new ymaps.Placemark([response['location']['latitude'], response['location']['longitude']]);

                            let placeMarkGuard = new ymaps.Placemark([response['guard']['guard_latitude'], response['guard']['guard_longitude']], {}, {
                                // preset: "islands#circleDotIcon",
                                // iconColor: '#ff0000',
                                // Необходимо указать данный тип макета.
                                iconLayout: 'default#image',
                                // Своё изображение иконки метки.
                                iconImageHref: '../storage/caricon.png',
                                // Размеры метки.
                                iconImageSize: [40, 35],
                                // Смещение левого верхнего угла иконки относительно
                                // её "ножки" (точки привязки).
                                iconImageOffset: [0, 0]
                            });

                            myMap.geoObjects
                                .add(placeMark)
                                .add(placeMarkGuard);
                        },
                        error: function (xhr, err) { 
                            console.log("Error: " + xhr + " " + err);
                        }
                    });
                }, 5000);
            }
            
        })

    </script>
@endsection