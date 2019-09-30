@extends('layouts.profileApp')

@section('content')

<form action="{{ route('send') }}" method="GET">
    {{ csrf_field() }}
    <input type="submit" value="createnewbid">
</form>

<div id="example">
    <example-component></example-component>
</div>

<div class="content">
    <div class="m-b-md">
        New notification will be alerted realtime!
    </div>
</div>
<!-- receive notifications -->
{{-- <script src="{{ asset('/js/bootstrap.js') }}"></script>
<script src=" {{ mix('js/app.js') }} "></script> --}}
{{-- <script src="https://js.pusher.com/4.1/pusher.min.js"></script> --}}
     
    <script>
    //   Pusher.logToConsole = true;
     
    //   Echo.private('bid.{{ $bidUID }}')
    //   .listen('NewBidNotification', (e) => {
    //       alert(e.message.message);
    //   });

    //   Echo.private(`bid.${bidUID}`)
    //     .listen('NewBidNotification', (e) => {
    //         console.log(e.update);
    //     });
    </script>
<!-- receive notifications -->
@endsection