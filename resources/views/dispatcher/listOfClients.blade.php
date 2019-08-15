@extends('layouts.profileApp')

@section('content')
    <h1>Клиенты</h1>
    <div class="col-sm-12">

        <a href="{{ route('auth.client.create') }}" class="btn btn-primary">Добавить клиентов</a>

        <input type="button" value="Удалить" class="js-destroy-button btn btn-primary">

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
                    <td>{{ $client->is_active }}</td>
                    <td>{{ $client->created_at->timezone('Europe/Moscow') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <script>
        $('.left-menu > .nav > *:nth-child(6)').addClass('active');
        $('.left-menu > .nav > *:nth-child(1)').removeClass('active');
    </script>
@endsection