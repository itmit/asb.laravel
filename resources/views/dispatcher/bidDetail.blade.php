@extends('layouts.profileApp')

@section('content')
    <h1>Заявка {{ $bid->id }}</h1>
    <div class="col-sm-12">
        <a href="{{ url()->previous() }}">Назад</a>
    </div>
    <div class="col-sm-12">
        <div>
            Статус: {{ $bid->created_at->timezone('Europe/Moscow') }}
            <select name="" id="">
                <option value=""></option>
                <option value=""></option>
                <option value=""></option>
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
            Клиент: <a href="client/{{ $bid->location()->client()->id }}">{{ $bid->location()->client()->name }}</a>
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
    </script>
@endsection