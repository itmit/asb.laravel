@extends('layouts.profileApp')

@section('content')
    <h1>Главная</h1>

    <div><span>{{ $cities }} город(а)</span></div>
    <div><span>{{ $reprs }} представитель(я)</span></div>
    <div><span>{{ $dispathers }} диспетчера(ов)</span></div>
@endsection
