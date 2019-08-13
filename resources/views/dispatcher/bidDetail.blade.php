@extends('layouts.profileApp')

@section('content')
    <h1>Заявка {{ $bid->id }}</h1>
    <div class="col-sm-12">
        <a href="{{ url()->previous() }}">Назад</a>
    </div>
    <div class="col-sm-12">
        <div>
            {{ $bid->created_at }}
        </div>
        <div>
            {{ $bid->updated_at }}
        </div>
        <div>
            {{ $bid->type }}
        </div>
        <div>
            
        </div>
    </div>
@endsection