@extends('layouts.profileApp')

@section('content')
    @if($client->type == 'Физическое лицо')
        <h1 id="client" data-clientid="{{ $client->id }}">Клиент {{ $client->name }}</h1>
        <div class="col-sm-12">
            <a href="{{ url()->previous() }}" class="btn btn-primary client-back">Назад</a>
        </div>
        <div class="col-sm-12">
            <div>
                <img src="{{URL::asset($client->user_picture)}}" alt="profile Pic" height="200" width="200">
            </div>
            <div>
                Примечание о клиенте: {{ $client->note }}
            </div>
            <div>
                Паспорт: {{ $client->passport }}
            </div>
            <div>
                Электронная почта: {{ $client->email }}
            </div>
            <div>
                {{ $client->type }}
            </div>
            <div>
                Номер телефона: {{ $client->phone_number }}
            </div>
            <div>
                <span class="clientActiveStatus">
                    @if($client->is_active)
                        Активен с {{ $client->active_from }}
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
            @ability('super-admin', 'show-last-location')
            <div>
                <button class="btn btn-primary display-location">Показать последнее местоположение</button>
            </div>
            @endability
            <div id="updated_at"></div>
            <div id="location" style="width: 600px; height: 400px"></div>
        </div>

    @elseif($client->type == 'Юридическое лицо')
        <h1 id="client" data-clientid="{{ $client->id }}">Клиент {{ $client->organization }}</h1>
        <div class="col-sm-12">
            <a href="{{ url()->previous() }}" class="btn btn-primary client-back">Назад</a>
        </div>
        <div class="col-sm-12">
            <div>
                <img src="{{URL::asset($client->user_picture)}}" alt="profile Pic" height="200" width="200">
            </div>
            <div>
                Генеральный директор: {{ $client->director }}
            </div>
            <div>
                Электронная почта: {{ $client->email }}
            </div>
            <div>
                {{ $client->type }}
            </div>
            <div>
                Номер телефона: {{ $client->phone_number }}
            </div>
            <div>
                ИНН: {{ $client->INN }}
            </div>
            <div>
                ОГРН: {{ $client->OGRN }}
            </div>
            <div>
                <span class="clientActiveStatus">
                    @if($client->is_active)
                        Активен до {{ $client->active_from }}
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
    @endif
    
    <script>
    $(document).ready(function()
    {
        let clientID = $('h1').data('clientid');
        let is_map_open = 0;
        $(document).on('click', '.display-location', function() {
            $.ajax({
                headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                dataType: "json",
                data: {clientID: clientID},
                url     : '../clients/lastLocation',
                method    : 'post',
                success: function (response) {

                    if(!is_map_open)
                    {
                        ymaps.ready(init);

                        function init() {
                            myMap = new ymaps.Map("location", {
                                center: [response['longitude'], response['latitude']],
                                zoom: 15
                            });

                                let placeMark = new ymaps.Placemark([response['longitude'], response['latitude']]);
                                myMap.geoObjects.add(placeMark);
                        }
                        is_map_open = 1;
                    }
                    else
                    {
                        myMap.geoObjects.removeAll()

                        let placeMark = new ymaps.Placemark([response['longitude'], response['latitude']]);
                        myMap.geoObjects.add(placeMark);
                        myMap.setCenter([response['longitude'], response['latitude']], 15);
                    }
                    $('#updated_at').html('');
                    $('#updated_at').html('Последнее обновление: ' + response['updated_at']);

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