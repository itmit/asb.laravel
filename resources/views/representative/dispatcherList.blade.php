@extends('layouts.profileApp')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-3">

            </div>
            <div class="col-sm-9">
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
                                    <td>{{ $dispatcher->name }}</td>
                                    <td>{{ $dispatcher->email }}</td>
                                    <td>{{ $dispatcher->created_at }}</td>
                                    <td>{{ $dispatcher->updated_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection