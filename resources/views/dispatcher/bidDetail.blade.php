@extends('layouts.profileApp')

@section('content')
    <h1 data-bidID="{{ $bid->id }}">Заявка {{ $bid->id }}</h1>
    <div class="col-sm-12">
        <a href="{{ url()->previous() }}">Назад</a>
    </div>
    <div class="col-sm-12">
        <div>
            Статус: {{ $bid->status }}
            <select id="changeBidStatus">
                <option value="Accepted">Accepted</option>
                <option value="PendingAcceptance">PendingAcceptance</option>
                <option value="Processed">Processed</option>
            </select>
        </div>
        <div>
            Создана: {{ $bid->created_at->timezone('Europe/Moscow') }}
        </div>
        <div>
            Обновлена: {{ $bid->updated_at->timezone('Europe/Moscow') }}
        </div>
        <div>
            Тип: {{ $bid->type }}
        </div>
        <div>
            Клиент: <a href="../client/{{ $bid->location()->client()->id }}">{{ $bid->location()->client()->name }}</a>
        </div>
        <div class="js-location" data-longitude="{{ $bid->location()->latitude }}" data-latitude="{{ $bid->location()->longitude }}">
            Координаты: {{ $bid->location()->latitude }} | {{ $bid->location()->longitude }}
        </div>
        <div id="map" style="width: 600px; height: 400px"></div>
    </div>
    <script>
    let $doc = $(document);

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
            zoom: 7
        });

        $locations.each(function () {

            // console.log([$(this).data('latitude'), $(this).data('longitude')]);
            let placeMark = new ymaps.Placemark([$(this).data('longitude'), $(this).data('latitude')]);
            myMap.geoObjects.add(placeMark);
        });
    }


    $(document).on('change', '#changeBidStatus', function() {
        // let bidNewStatus = $('#changeBidStatus').val();
        // let bidID = data('bidID');
        // console.log(bidNewStatus);
        console.log('sss');
        // $.ajax({
        //     headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        //     dataType: "json",
        //     data    : { bid: bid },
        //     url     : 'places/getPlacesByBlock',
        //     method    : 'post',
        //     success: function (data) {
        //     let result = '';
        //     if(data[0].length === 0)
        //     {
        //         result += '<tr><td colspan="7">В выбранном разделе ничего нет</td></tr>'
        //     }
        //     else
        //     {
        //         for(var i = 0; i < data[0].length; i++) {
        //             result += '<tr>';
        //             result += '<td><input type="checkbox" data-place-id="' + data[0][i]['id'] + '" name="destoy-place-' + data[0][i]['id'] + '" class="js-destroy"/></td>';
        //             result += '<td>' + data[0][i]['block'] + '</td>';
        //             result += '<td>' + data[0][i]['floor'] + '</td>';
        //             result += '<td>' + data[0][i]['row'] + '</td>';
        //             result += '<td>' + data[0][i]['place_number'] + '</td>';
        //             result += '<td>' + data[0][i]['status'] + '</td>';
        //             result += '<td>' + data[0][i]['price'] + '</td>';
        //             result += '</tr>';
        //         }
        //     }   
        //     $('tbody').html(result);
        //     },
        //     error: function (xhr, err) { 
        //         console.log(err + " " + xhr);
        //     }
        // });
    });
    </script>
@endsection