@extends('layouts.profileApp')

@section('content')
    <div class="row">
        <h1>Заявки</h1>
        <div class="col-sm-12">

            <div id="map" style="width: 600px; height: 400px"></div>

            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Статус</th>
                    <th>Клиент</th>
                    <th>Место</th>
                    <th>Дата создания</th>
                    <th>Дата обновления</th>
                </tr>
                </thead>
                <tbody>
                @foreach($bids as $bid)
                    <tr>
                        <td>{{ $bid->status }}</td>
                        <td>
                            <div class="js-location" data-longitude="{{ $bid->location()->latitude }}" data-latitude="{{ $bid->location()->longitude }}">
                            {{ $bid->location()->client()->email }}
                            </div>
                        </td>
                        <td>

                        </td>
                        <td>{{ $bid->created_at->timezone('Europe/Moscow') }}</td>
                        <td>{{ $bid->updated_at->timezone('Europe/Moscow') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        global.$ = global.jQuery = require('jquery');
        $(document).ready(function() {
            setInterval(function(){ 
                // $.ajax({
                // headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                // dataType: "json",
                // url     : 'bid',
                // method    : 'get',
                // success: function (response) {
                //     console.log(response);
                // },
                // error: function (xhr, err) { 
                //     console.log("Error: " + xhr + " " + err);
                // }
                console.log('+');
            }, 5000);
            });
    </script>
@endsection