@extends('layouts.profileApp')

@section('content')
    <h1>Диспетчеры</h1>
    <div class="col-sm-12">

        @ability('super-admin,representative', 'create-dispatcher')
        <a href="{{ route('auth.dispatcher.create') }}">Создать диспетчера</a>
        @endability

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Имя</th>
                <th>Почта</th>
                <th>Дата создания</th>
                <th>Дата обновления</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dispatchers as $dispatcher)
                <tr>
                    <td>{{ $dispatcher->user()->name }}</td>
                    <td>{{ $dispatcher->user()->email }}</td>
                    <td>{{ $dispatcher->created_at->timezone('Europe/Moscow') }}</td>
                    <td>{{ $dispatcher->updated_at->timezone('Europe/Moscow') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection