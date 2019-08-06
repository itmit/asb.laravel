@extends('layouts.profileApp')

@section('content')
    <h1>Представители</h1>
    <div class="col-sm-12">

        @ability('super-admin', 'create-representative')
        <a href="{{ route('auth.representative.create') }}">Создать представителя</a>
        <input type="button" value="Удалить" class="js-destroy-button-representative">
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
            @foreach($representatives as $representative)
                <tr>
                    <td><input type="checkbox" data-place-id="{{ $representative->id }}" name="destoy-place-{{ $representative->id }}" class="js-destroy"/></td>
                    <td>{{ $representative->name }}</td>
                    <td>{{ $representative->email }}</td>
                    <td>{{ $representative->created_at }}</td>
                    <td>{{ $representative->updated_at }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection