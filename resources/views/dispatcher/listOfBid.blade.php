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
        $(document).ready(function() {
            setInterval(function(){ 
                $.ajax({
                    headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    dataType: "json",
                    url     : 'bid/updateList',
                    method    : 'post',
                    success: function (response) {
                        let result = '';
                        let count = 0;
                        count = response.length;
                        console.log(count);
                        for(var i = 0; i < response[0].length; i++) {
                            result += '<tr>';
                            result += '<td>' + response[0][i]['status'] + '</td>';
                            result += '<td>' + response[0][i]['email'] + '</td>';
                            result += '<td>' + response[0][i]['location'] + '</td>';
                            result += '<td>' + response[0][i]['created_at'] + '</td>';
                            result += '<td>' + daresponseta[0][i]['updated_at'] + '</td>';
                            result += '</tr>';
                        }
                        console.log(result);
                        $('tbody').html(result);
                    },
                    error: function (xhr, err) { 
                        console.log("Error: " + xhr + " " + err);
                    }
                });
            }, 10000);
            });
    </script>
@endsection