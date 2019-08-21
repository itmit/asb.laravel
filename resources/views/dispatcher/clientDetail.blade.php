@extends('layouts.profileApp')

@section('content')
    <h1 id="client" data-clientid="{{ $client->id }}">Клиент {{ $client->name }}</h1>
    <div class="col-sm-12">
        <a href="{{ url()->previous() }}" class="btn btn-primary client-back">Назад</a>
    </div>
    <div class="col-sm-12">
        <div>
            <img src="{{URL::asset($client->user_picture)}}" alt="profile Pic" height="200" width="200">
        </div>
        <div>
            {{ $client->note }}
        </div>
        <div>
            <span class="clientActiveStatus">
                @if($client->is_active)
                    Активен
                @else
                    Не активен
                @endif 
            </span>
            @ability('super-admin', 'change-activity')
            @if($client->is_active)
                <input type="checkbox" name="activeClient" id="activeClient" checked>
            @else
                <input type="checkbox" name="activeClient" id="activeClient">
            @endif                  
            @endability
            
        </div>
        <div>
            <button class="btn btn-primary display-location">Показать последнее местоположение</button>
        </div>
        <div id="updated_at"></div>
        <div id="location" style="width: 600px; height: 400px"></div>
    </div>

    <script>
    $(document).ready(function()
    {
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
                myMap.geoObjects.add(placeMark);
            });
        }

        let clientID = $('h1').data('clientid');
        $(document).on('click', '.display-location', function() {
            $('#location').html('');
            $('#updated_at').html('');
            $.ajax({
                headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                dataType: "json",
                data: {clientID: clientID},
                url     : '../clients/lastLocation',
                method    : 'post',
                success: function (response) {
                    
                    myMap.destroy();

                    $('#location').html('');
                    $('#updated_at').html('');
                    $('#updated_at').html('Последнее обновление: ' + response['updated_at']);
                    
                    // Функция ymaps.ready() будет вызвана, когда
                    // загрузятся все компоненты API, а также когда будет готово DOM-дерево.
                    ymaps.ready(init);

                    function init() {
                        myMap.geoObjects.removeAll()

                        let placeMark = new ymaps.Placemark([response['latitude'], response['longitude']]);
                        myMap.geoObjects.add(placeMark);
                    }

                },
                error: function (xhr, err) { 
                    console.log("Error: " + xhr + " " + err);
                }
            })
        })

        $(document).on('change', '#activeClient', function() {
            if($("#activeClient").prop("checked")){
                console.log('no check');
                $.ajax({
                headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                dataType: "json",
                data: {clientID: clientID, direction: 1},
                url     : '../clients/changeActivity',
                method    : 'post',
                success: function (response) {
                    $(".clientActiveStatus").html("Активен");
                },
                error: function (xhr, err) { 
                    console.log("Error: " + xhr + " " + err);
                }
            })
            }
            else{
                console.log('check');
                $.ajax({
                headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                dataType: "json",
                data: {clientID: clientID, direction: 0},
                url     : '../clients/changeActivity',
                method    : 'post',
                success: function (response) {
                    $(".clientActiveStatus").html("Не активен");
                },
                error: function (xhr, err) { 
                    console.log("Error: " + xhr + " " + err);
                }
            })
            }
        })
    })
    </script>
@endsection