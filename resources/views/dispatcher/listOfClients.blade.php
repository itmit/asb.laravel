@extends('layouts.profileApp')

@section('content')
    <h1>Клиенты</h1>
    <div class="col-sm-12">

        <a href="{{ route('auth.client.create') }}">Добавить клиентов</a>

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Имя</th>
                <th>Почта</th>
                <th>Номер телефона</th>
                <th>Дата создания</th>
            </tr>
            </thead>
            <tbody>
            @foreach($clients as $client)
                <tr>
                    <td>{{ $client->name }}</td>
                    <td>{{ $client->email }}</td>
                    <td>{{ $client->phone_number }}</td>
                    <td>{{ $client->created_at->timezone('Europe/Moscow') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection