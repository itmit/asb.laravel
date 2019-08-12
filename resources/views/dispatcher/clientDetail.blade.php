@extends('layouts.profileApp')

@section('content')
    <h1>Клиент {{ $client->name }}</h1>
    <div class="col-sm-12">
        <div>
            {{ HTML::image('{{ $client->user_picture }}', 'alt text', array('class' => 'css-class')) }}
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