@extends('layouts.profileApp')

@section('content')
    <h1>Клиенты</h1>
    <div class="col-sm-12">

        <a href="{{ route('auth.client.create') }}" class="btn btn-primary">Добавить клиентов</a>

        <input type="button" value="Удалить" class="js-destroy-button btn btn-primary">

        <select name="selectClientsByType" id="selectClientsByType" class="form-control">
            <option value="Individual" selected>Физические лица</option>
            <option value="Entity">Юридические лица</option>
        </select>

        <table class="table table-bordered" style="width: 100%">
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
                <td><a href="client/{{ $client->id }}"> {{ $client->name ?? "Имя неизвестно" }} </a></td>
                    <td>{{ $client->email ?? "Почта неизвестна"}}</td>
                    <td>{{ $client->phone_number }}</td>
                    <td>
                        @if($client->is_active)
                        Активен
                        @else
                        Не активен
                        @endif
                    </td>
                    <td>{{ date('H:i d.m.Y', strtotime($client->created_at->timezone('Europe/Moscow'))) }}</td>
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
                            console.log(response.length);
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
                                if(response[i]['organization'] != null)
                                {
                                    result += '<td><a href="client/' + response[i]['id'] + '">' + response[i]['organization'] + '</a></td>';
                                }
                                else
                                {
                                    result += '<td><a href="client/' + response[i]['id'] + '">Наименование неизвестно</a></td>';
                                }
                                if(response[i]['email'] != null)
                                {
                                    result += '<td>' + response[i]['email'] + '</td>';
                                }
                                else
                                {
                                    result += '<td>Почта неизвестна</td>';
                                }
                                result += '<td>' + response[i]['phone_number'] + '</td>';
                                if(response[i]['is_active'] == 1)
                                {
                                    result += '<td>Активен</td>';
                                }
                                else
                                {
                                    result += '<td>Не активен</td>';
                                }
                                
                                result += '<td>' + response[i]['created_at'] + '</td>';
                                result += '</tr>'; 
                            }
                            $('tbody').html(result);
                            // console.log(response);
                        }
                        if(selectClientsByType == 'Individual')
                        {
                            console.log(response.length);
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
                                result += '<tr>';
                                result += '<td><input type="checkbox" name="destroy-all-clients" class="js-destroy-all"/></td>';
                                if(response[i]['name'] != null)
                                {
                                    result += '<td><a href="client/' + response[i]['id'] + '">' + response[i]['name'] + '</a></td>';
                                }
                                else
                                {
                                    result += '<td><a href="client/' + response[i]['id'] + '">Имя неизвестно</a></td>';
                                }
                                if(response[i]['email'] != null)
                                {
                                    result += '<td>' + response[i]['email'] + '</td>';
                                }
                                else
                                {
                                    result += '<td>Почта неизвестна</td>';
                                }
                                result += '<td>' + response[i]['phone_number'] + '</td>';
                                if(response[i]['is_active'] == 1)
                                {
                                    result += '<td>Активен</td>';
                                }
                                else
                                {
                                    result += '<td>Не активен</td>';
                                }
                                
                                result += '<td>' + response[i]['created_at'] + '</td>';
                                result += '</tr>'; 
                            }
                            $('tbody').html(result);
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