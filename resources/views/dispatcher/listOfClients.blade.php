@extends('layouts.profileApp')

@section('content')
    <h1>Клиенты</h1>
    <div class="col-sm-12">

        <a href="{{ route('auth.client.create') }}">Добавить клиентов</a>

        <input type="button" value="Удалить" class="js-destroy-button">

        <table class="table table-bordered">
            <thead>
            <tr>
                <th><input type="checkbox" name="destroy-all-places" class="js-destroy-all"/></th>
                <th>Имя</th>
                <th>Почта</th>
                <th>Номер телефона</th>
                <th>Дата создания</th>
            </tr>
            </thead>
            <tbody>
            @foreach($clients as $client)
                <tr>
                    <td><input type="checkbox" data-place-id="{{ $client->id }}" name="destoy-place-{{ $client->id }}" class="js-destroy"/></td>
                <td><a href="{{ route('clientDetail') }}">{{ $client->name }}</a></td>
                    <td>{{ $client->email }}</td>
                    <td>{{ $client->phone_number }}</td>
                    <td>{{ $client->created_at->timezone('Europe/Moscow') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection