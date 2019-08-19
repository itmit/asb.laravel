@extends('layouts.profileApp')

@section('content')
    <h1 data-bidID="{{ $bid->id }}">Заявка {{ $bid->id }}</h1>
    <div class="col-sm-12">
        <a href="{{ url()->previous() }}">Назад</a>
    </div>
    <div class="col-sm-12">
        <div>
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
        <div>
            Обновлена: {{ $bid->updated_at->timezone('Europe/Moscow') }}
        </div>
        <div>
            Тип: {{ $bid->type }}
        </div>
        <div>
            Клиент: <a href="../client/{{ $bid->location()->client()->id }}">{{ $bid->location()->client()->name }}</a>
        </div>
        <div>
            Заявку принял: <a href="../guard/{{ $bid->guard }}">{{ $bid->guard }}</a>
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

    $(document).ready(function()
        {
        $(document).on('change', '#changeBidStatus', function() {
            let selectBidsByStatus = $('#selectBidsByStatus').val();
            console.log(changeBidStatus);
        // $.ajax({
        //     headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        //     dataType: "json",
        //     data: {selectBidsByStatus: selectBidsByStatus},
        //     url     : 'bid/updateList',
        //     method    : 'post',
        //     success: function (response) {
        //         let result = '';
        //             for(var i = 0; i < response.length; i++) {
        //                 result += '<tr>';
        //                 result += '<td><a href="bid/' + response[i]['id'] + '">' + response[i]['status'] + '</a></td>';
        //                 result += '<td>' + response[i]['client']['email'] + '</td>';
        //                 result += '<td>' + response[i]['location']['latitude'] + ' | ' + response[i]['location']['longitude'] + '</td>';
        //                 result += '<td>' + response[i]['type'] + '</td>';
        //                 result += '<td>' + response[i]['created_at'] + '</td>';
        //                 result += '<td>' + response[i]['updated_at'] + '</td>';
        //                 result += '</tr>';
        //             }
        //             $('tbody').html(result);
        //     },
        //     error: function (xhr, err) { 
        //         console.log(err + " " + xhr);
        //     }
        // });
        })
    });
    </script>
@endsection