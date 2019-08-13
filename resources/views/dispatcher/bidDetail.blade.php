@extends('layouts.profileApp')

@section('content')
    <h1>Заявка {{ $client->name }}</h1>
    <div class="col-sm-12">
        <a href="{{ url()->previous() }}">Назад</a>
    </div>
    <div class="col-sm-12">
        <div>
            <img src="{{URL::asset($client->user_picture)}}" alt="profile Pic" height="200" width="200">
        </div>
        <div>
            PASSWORD?????
        </div>
        <div>
            {{ $client->note }}
        </div>
        <div>
            {{ $client->is_active }}
        </div>
    </div>
@endsection