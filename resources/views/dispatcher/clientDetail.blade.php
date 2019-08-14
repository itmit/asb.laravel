@extends('layouts.profileApp')

@section('content')
    <h1 id="client" data-clientID="{{ $client->id }}">Клиент {{ $client->name }}</h1>
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
            {{ $client->is_active }}
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
            <div id="location"></div>
        </div>
    </div>

    <script>
    $(document).ready(function()
    {
        $(document).on('click', '.display-location', function() {
            let clientID = $('#client').data('clientID');
            console.log(clientID);
            $.ajax({
                headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                dataType: "json",
                data: {clientID: clientID},
                url     : '../clients/lastLocation',
                method    : 'post',
                success: function (response) {
                    $('#location').html(
                        's'
                    );
                },
                error: function (xhr, err) { 
                    console.log("Error: " + xhr + " " + err);
                }
            })
        })
    })
    </script>
@endsection