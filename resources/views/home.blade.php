@extends('layouts.profileApp')

@section('content')
    <h1>Главная</h1>

    <!--<div><span>{{ $cities }} город(а)</span></div>
    <div><span>{{ $reprs }} представитель(я)</span></div>
    <div><span>{{ $dispathers }} диспетчера(ов)</span></div> -->
    <div class="col-sm-12">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th class="table-checkbox"><input type="checkbox" name="destroy-all-places" class="js-destroy-all"/></th>
                <th></th>
                <th>Количество</th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="checkbox" class="js-destroy"/></td>
                    <td>Город</td>
                    <td>{{ $cities }}</td>
                </tr>
                <tr>
                    <td><input type="checkbox" class="js-destroy"/></td>
                    <td>Представитель</td>
                    <td>{{ $reprs }}</td>
                </tr>
                <tr>
                    <td><input type="checkbox" class="js-destroy"/></td>
                    <td>Диспетчер</td>
                    <td>{{ $dispathers }}</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
