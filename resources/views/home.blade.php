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
                <th>Наименование</th>
                <th class="number">Количество</th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Город</td>
                    <td class="number">{{ $cities }}</td>
                </tr>
                <tr>
                    <td>Представитель</td>
                    <td class="number">{{ $reprs }}</td>
                </tr>
                <tr>
                    <td>Диспетчер</td>
                    <td class="number">{{ $dispathers }}</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
