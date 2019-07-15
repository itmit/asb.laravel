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

        console.log([$(this).data('latitude'), $(this).data('longitude')]);
        let placeMark = new ymaps.Placemark([$(this).data('longitude'), $(this).data('latitude')]);
        myMap.geoObjects.add(placeMark);
    });
}
