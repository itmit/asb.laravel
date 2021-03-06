@extends('layouts.profileApp')

@section('content')
    <div class="row">
        <h1>Заявки</h1>
        <div class="col-sm-12">

            {{-- <div id="map" style="width: 600px; height: 400px"></div> --}}

            {{-- <select name="selectBidsByStatus" id="selectBidsByStatus">
                <option value="PendingAcceptance" selected>Ожидает принятия</option>
                <option value="Accepted">Принята</option>
                <option value="Processed">Выполнена</option>
            </select> --}}

            <ul class="nav nav-tabs" id="myTab">
                <li data-type="active" class="active"><a href="#">Активные</a></li>
                {{-- <li data-type="Accepted"><a href="#">Принята</a></li> --}}
                <li data-type="closed"><a href="#">Выполненные</a></li>
            </ul>

            <table class="table table-bordered" style="width: 100%">
                <thead>
                <tr>
                    <th>Статус</th>
                    <th>Клиент</th>
                    <th>Место</th>
                    <th>Тип</th>
                    <th>Телефон</th>
                    <th>Дата создания</th>
                    <th>Дата обновления</th>
                </tr>
                </thead>
                <tbody>

                @foreach($bids as $bid)
                @if($bid->client()->location() == NULL)
                    @continue
                @endif
                    <tr class="bid" style="transition-duration:1s">
                        <td><a href="bid/{{ $bid->id }}"> {{ $bid->status }} </a></td>
                        <td>
                            <div class="js-location" data-longitude="{{ $bid->latitude }}" data-latitude="{{ $bid->longitude }}">
                                <a href="client/{{ $bid->client()->id }}">
                                    @if($bid->client()->name != NULL) {{ $bid->client()->name }}
                                    @else {{ $bid->client()->organization }}
                                    @endif
                                </a>
                            </div>
                        </td>
                        <td>
                            {{ $bid->latitude }} | {{ $bid->longitude }}
                        </td>
                        <td>{{ $bid->type }}</td>
                        <td>{{ $bid->client()->phone_number }}</td>
                        <td>{{ date('H:i d.m.Y', strtotime($bid->created_at->timezone('Europe/Moscow'))) }}</td>
                        <td>{{ date('H:i d.m.Y', strtotime($bid->updated_at->timezone('Europe/Moscow'))) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <input type="hidden" id="bmfkd" value="{{ $role }}">
    </div>

    <script>
        $(document).ready(function()
        {
            let bidColor = true;
            let role = $('#bmfkd').val();
            if(role == 0)
            {
                setInterval(function(){ 
                let selectBidsByStatus = $('#myTab li.active').data('type');
                $.ajax({
                    headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    dataType: "json",
                    data: {selectBidsByStatus: selectBidsByStatus},
                    url     : 'bid/updateList',
                    method    : 'post',
                    success: function (response) {
                        let result = '';
                        for(var i = 0; i < response.length; i++) {
                            result += '<tr class="bid">';
                            result += '<td><a href="bid/' + response[i]['id'] + '">' + response[i]['status'] + '</a></td>';
                            if(response[i]['client']['name'] == null)
                            {     
                                result += '<td><a href="client/' + response[i]['client']['id'] + '">' + response[i]['client']['organization'] + '</a></td>';
                            }
                            else
                            {
                                result += '<td><a href="client/' + response[i]['client']['id'] + '">' + response[i]['client']['name'] + '</a></td>';
                            }
                            
                            result += '<td>' + response[i]['location']['latitude'] + ' | ' + response[i]['location']['longitude'] + '</td>';
                            result += '<td>' + response[i]['type'] + '</td>';
                            result += '<td>' + response[i]['client']['phone_number'] + '</td>';
                            result += '<td>' + response[i]['created_at'] + '</td>';
                            result += '<td>' + response[i]['updated_at'] + '</td>';
                            result += '</tr>';
                        }
                        $('tbody').html(result);

                        let bidsCount = $('tbody').html();
                        if (bidsCount != '')
                        {
                            if(selectBidsByStatus == "active")
                            {
                                if(bidColor)
                                {
                                    $(".bid").css("background-color", "white");
                                    // $(".bid").css("color", "white");
                                    bidColor = false;
                                }
                                else
                                {
                                    $(".bid").css("background-color", "#ff000061");
                                    $(".bid").css("color", "white");
                                    bidColor = true;
                                }
                                // let audio = new Audio('alert.mp3');
                                // audio.play();
                            }
                        }
                        else{
                            // console.log("NULL");
                        }
                    },
                    error: function (xhr, err) { 
                        console.log("Error: " + xhr + " " + err);
                    }
                });

            }, 10000);
            }            

        });
        $('#myTab li').click(function (e) {
            e.preventDefault()
            $(this).tab('show')
            // console.log($(this).data('type'))
            let selectBidsByStatus = $(this).data('type');
            $.ajax({
                headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                dataType: "json",
                data: {selectBidsByStatus: selectBidsByStatus},
                url     : 'bid/updateList',
                method    : 'post',
                success: function (response) {
                    let result = '';
                        for(var i = 0; i < response.length; i++) {
                            result += '<tr>';
                            result += '<td><a href="bid/' + response[i]['id'] + '">' + response[i]['status'] + '</a></td>';
                            if(response[i]['client']['name'] == null)
                            {     
                                result += '<td><a href="client/' + response[i]['client']['id'] + '">' + response[i]['client']['organization'] + '</a></td>';
                            }
                            else
                            {
                                result += '<td><a href="client/' + response[i]['client']['id'] + '">' + response[i]['client']['name'] + '</a></td>';
                            }
                            result += '<td>' + response[i]['location']['latitude'] + ' | ' + response[i]['location']['longitude'] + '</td>';
                            result += '<td>' + response[i]['type'] + '</td>';
                            result += '<td>' + response[i]['client']['phone_number'] + '</td>';
                            result += '<td>' + response[i]['created_at'] + '</td>';
                            result += '<td>' + response[i]['updated_at'] + '</td>';
                            result += '</tr>';
                        }
                        $('tbody').html(result);
                },
                error: function (xhr, err) { 
                    console.log(err + " " + xhr);
                }
            });
        })
    </script>
@endsection