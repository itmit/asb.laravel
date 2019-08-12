@extends('layouts.profileApp')

@section('content')
    <h1>Клиент {{ $client->name }}</h1>
    <div class="col-sm-12">
        <div>
            {!! Html::image('img/logo.png') !!}
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