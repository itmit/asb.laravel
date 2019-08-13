@extends('layouts.profileApp')

@section('content')
    <h1>Диспетчеры</h1>
    <div class="col-sm-12">

        @ability('super-admin,representative', 'create-dispatcher')
        <a href="{{ route('auth.dispatcher.create') }}" class="btn btn-primary">Создать диспетчера</a>
        <input type="button" value="Удалить" class="js-destroy-button-dispatcher btn btn-primary">
        @endability

        <table class="table table-bordered">
            <thead>
            <tr>
                <th><input type="checkbox" name="destroy-all-places" class="js-destroy-all"/></th>
                <th>Логин</th>
                <th>Имя</th>
                <th>Почта</th>
                <th>Дата создания</th>
                <th>Дата обновления</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dispatchers as $dispatcher)
                <tr>
                    <td><input type="checkbox" data-place-id="{{ $dispatcher->id }}" name="destoy-place-{{ $dispatcher->id }}" class="js-destroy"/></td>
                    <td>{{ $dispatcher->user()->name }}</td>
                    <td>{{ $dispatcher->user()->fio }}</td>
                    <td>{{ $dispatcher->user()->email }}</td>
                    <td>{{ $dispatcher->created_at->timezone('Europe/Moscow') }}</td>
                    <td>{{ $dispatcher->updated_at->timezone('Europe/Moscow') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <script>
        $('.left-menu > .nav > *:nth-child(3)').addClass('active');
        $('.left-menu > .nav > *:nth-child(1)').removeClass('active');
    </script>
@endsection