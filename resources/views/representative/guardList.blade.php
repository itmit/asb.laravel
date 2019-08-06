@extends('layouts.profileApp')

@section('content')
    <h1>Охранники</h1>
    <div class="col-sm-12">

        @ability('super-admin,representative', 'create-guard')
        <a href="{{ route('auth.guard.create') }}">Создать охранника</a>
        @endability

        <table class="table table-bordered">
            <thead>
            <tr>
                <th><input type="checkbox" name="destroy-all-places" class="js-destroy-all"/></th>
                <th>Имя</th>
                <th>Почта</th>
                <th>Дата создания</th>
                <th>Дата обновления</th>
            </tr>
            </thead>
            <tbody>
                @foreach($guards as $guard)
                <tr>
                    <td><input type="checkbox" data-place-id="{{ $dispatcher->id }}" name="destoy-place-{{ $dispatcher->id }}" class="js-destroy"/></td>
                    <td>{{ $guard->name }}</td>
                    <td>{{ $guard->email }}</td>
                    <td>{{ $guard->phone_number }}</td>
                    <td>{{ $guard->created_at->timezone('Europe/Moscow') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection