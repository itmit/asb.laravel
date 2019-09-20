@extends('layouts.profileApp')

@section('content')
    <h1 id="guard" data-clientid="{{ $guard->id }}">ГБР</h1>
    <div class="col-sm-12">
        <div>
            Наименование: {{ $guard->name }}
        </div>
        <div>
            Почта: {{ $guard->email }}
        </div>
        <div>
            Телефон: {{ $guard->phone_number }}
        </div>
    </div>
@endsection