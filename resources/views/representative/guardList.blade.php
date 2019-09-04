@extends('layouts.profileApp')

@section('content')
    <h1>Охранники</h1>
    <div class="col-sm-12">

        @ability('super-admin,representative', 'create-guard')
        <a href="{{ route('auth.guard.create') }}" class="btn btn-primary">Создать охранника</a>
        <input type="button" value="Удалить" class="js-destroy-button-guard btn btn-primary">
        @endability

        <table class="table table-bordered">
            <thead>
            <tr>
                @ability('super-admin,representative', 'destroy')
                <th><input type="checkbox" name="destroy-all-places" class="js-destroy-all"/></th>
                @endability
                <th>Имя</th>
                <th>Почта</th>
                <th>Телефон</th>
                <th>Дата создания</th>
                <th>Дата обновления</th>
            </tr>
            </thead>
            <tbody>
                @foreach($guards as $guard)
                <tr>
                    @ability('super-admin,representative', 'destroy')
                    <td><input type="checkbox" data-place-id="{{ $guard->id }}" name="destoy-place-{{ $guard->id }}" class="js-destroy"/></td>
                    @endability
                    <td>{{ $guard->name }}</td>
                    <td>{{ $guard->email }}</td>
                    <td>{{ $guard->phone_number }}</td>
                    <td>{{ $guard->created_at->timezone('Europe/Moscow') }}</td>
                    <td>{{ $guard->updated_at->timezone('Europe/Moscow') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <script>
        $('.left-menu > .nav > *:nth-child(4)').addClass('active');
        $('.left-menu > .nav > *:nth-child(1)').removeClass('active');
    </script>
@endsection