@extends('layouts.profileApp')

@section('content')
    <h1>Клиенты</h1>
    <div class="col-sm-12">

        <input type="button" value="Удалить" class="js-destroy-button btn btn-primary">

        <select name="selectClientsByType" id="selectClientsByType" class="form-control">
            <option value="Individual" selected>Физические лица</option>
            <option value="Entity">Юридические лица</option>
        </select>

        <table class="table table-bordered">
            <thead>
            <tr>
                <th><input type="checkbox" name="destroy-all-places" class="js-destroy-all"/></th>
                <th>Имя</th>
                <th>Почта</th>
                <th>Номер телефона</th>
                <th>Статус активности</th>
                <th>Дата создания</th>
            </tr>
            </thead>
            <tbody>
            @foreach($clients as $client)
                <tr>
                    <td><input type="checkbox" data-place-id="{{ $client->id }}" name="destoy-place-{{ $client->id }}" class="js-destroy"/></td>
                <td><a href="client/{{ $client->id }}"> {{ $client->name }} </a></td>
                    <td>{{ $client->email }}</td>
                    <td>{{ $client->phone_number }}</td>
                    <td>
                        @if($client->is_active)
                        Активен
                        @else
                        Не активен
                        @endif
                    </td>
                    <td>{{ $client->created_at->timezone('Europe/Moscow') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <script>
        $('.left-menu > .nav > *:nth-child(6)').addClass('active');
        $('.left-menu > .nav > *:nth-child(1)').removeClass('active');

        $(document).ready(function()
        {
            $(document).on('change', '#selectClientsByType', function() {
                let selectClientsByType = $('#selectClientsByType').val();
                $.ajax({
                    headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    dataType: "json",
                    data: {selectClientsByType: selectClientsByType},
                    url     : 'clients/selectClientsByType',
                    method    : 'post',
                    success: function (response) {
                        if(selectClientsByType == 'Entity')
                        {
                            let result = '';
                            result += '<tr>';
                            result += '<th><input type="checkbox" name="destroy-all-clients" class="js-destroy-all"/></th>';
                            result += '<th>Название</th>';
                            result += '<th>Почта</th>';
                            result += '<th>Номер телефона</th>';
                            result += '<th>Статус активности</th>';
                            result += '<th>Дата создания</th>';
                            result += '</tr>';
                            $('thead').html(result);
                            result = '';
                            for(var i = 0; i < response.length; i++) {
                                console.log(response[i]);
                                result += '<tr>';
                                result += '<td><input type="checkbox" name="destroy-all-clients" class="js-destroy-all"/></td>';
                            //     result += '<td><a href="client/' + response[i]['client']['id'] + '">' + response[i]['client']['name'] + '</a></td>';
                            //     result += '<td>' + response[i]['location']['latitude'] + ' | ' + response[i]['location']['longitude'] + '</td>';
                            //     result += '<td>' + response[i]['type'] + '</td>';
                            //     result += '<td>' + response[i]['created_at'] + '</td>';
                            //     result += '<td>' + response[i]['updated_at'] + '</td>';
                                result += '</tr>'; 
                            }
                            $('tbody').html(result);
                            // console.log(response);
                        }
                        if(selectClientsByType == 'Individual')
                        {
                            let result = '';
                            result += '<tr>';
                            result += '<th><input type="checkbox" name="destroy-all-places" class="js-destroy-all"/></th>';
                            result += '<th>Имя</th>';
                            result += '<th>Почта</th>';
                            result += '<th>Номер телефона</th>';
                            result += '<th>Статус активности</th>';
                            result += '<th>Дата создания</th>';
                            result += '</tr>';
                            $('thead').html(result);
                            result = '';
                            for(var i = 0; i < response.length; i++) {
                                console.log(response[i]);
                                result += '<tr>';
                                result += '<td><input type="checkbox" name="destroy-all-clients" class="js-destroy-all"/></td>';
                            //     result += '<td><a href="client/' + response[i]['client']['id'] + '">' + response[i]['client']['name'] + '</a></td>';
                            //     result += '<td>' + response[i]['location']['latitude'] + ' | ' + response[i]['location']['longitude'] + '</td>';
                            //     result += '<td>' + response[i]['type'] + '</td>';
                            //     result += '<td>' + response[i]['created_at'] + '</td>';
                            //     result += '<td>' + response[i]['updated_at'] + '</td>';
                                result += '</tr>'; 
                            }
                            $('tbody').html(result);
                            // console.log(response);
                        }
                        
                    },
                    error: function (xhr, err) { 
                        console.log(err + " " + xhr);
                    }
                });
            });
        });
    </script>
@endsection