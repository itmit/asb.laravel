@extends('layouts.profileApp')

@section('content')
    <h1 id="guard" data-clientid="{{ $guard->id }}">Охранник {{ $guard->name }}</h1>
@endsection