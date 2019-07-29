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
                <th>Имя</th>
                <th>Почта</th>
                <th>Дата создания</th>
                <th>Дата обновления</th>
            </tr>
            </thead>
            <tbody>
                @foreach($guards as $guard)
                <tr>
                    <td>{{ $guard->name }}</td>
                    <td>{{ $guard->email }}</td>
                    <td>{{ $guard->phone_number }}</td>
                    <td>{{ $guard->created_at }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection