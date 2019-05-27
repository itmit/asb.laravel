@extends('layouts.profileApp')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-3">

            </div>
            <div class="col-sm-9">
                <h1>Представители</h1>
                <div class="col-sm-12">

                    @ability('super-admin', 'create-representative')
                    <a href="{{ route('auth.representative.create') }}">Создать представителя</a>
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
                        @foreach($representatives as $representative)
                            <tr>
                                <td>{{ $representative->name }}</td>
                                <td>{{ $representative->email }}</td>
                                <td>{{ $representative->created_at }}</td>
                                <td>{{ $representative->updated_at }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection