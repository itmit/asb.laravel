@extends('layouts.profileApp')

@section('content')
    <h1>Представители</h1>
    <div class="col-sm-12">

        @ability('super-admin', 'create-representative')
        <a href="{{ route('auth.representative.create') }}" class="btn btn-primary">Создать представителя</a>
        <input type="button" value="Удалить" class="js-destroy-button-representative btn btn-primary">
        @endability

        <table class="table table-bordered">
            <thead>
            <tr>
                <th><input type="checkbox" name="destroy-all-places" class="js-destroy-all"/></th>
                <th>Имя</th>
                <th>Почта</th>
                <th>Город</th>
                <th>Дата создания</th>
                <th>Дата обновления</th>
            </tr>
            </thead>
            <tbody>
            @foreach($representatives as $representative)
                <tr>
                    <td><input type="checkbox" data-place-id="{{ $representative->id }}" name="destoy-place-{{ $representative->id }}" class="js-destroy"/></td>
                    <td>{{ $representative->name }}</td>
                    <td>{{ $representative->email }}</td>
                    <td>{{ $representative->city }}</td>
                    <td>{{ $representative->created_at->timezone('Europe/Moscow') }}</td>
                    <td>{{ $representative->updated_at->timezone('Europe/Moscow') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <script>
        $('.left-menu > .nav > *:nth-child(2)').addClass('active');
        $('.left-menu > .nav > *:nth-child(1)').removeClass('active');
    </script>
    <script>
    $(document).ready(function() {
        $(document).on('click', '.js-destroy-button-representative', function() {
            let ids = [];
    
            $(".js-destroy:checked").each(function(){
                ids.push($(this).data('placeId'));
            });
            
            console.log(ids);
    
            let uSure = confirm('Вы действительно хотите удалить?');
            if(uSure)
            {
                $.ajax({
                headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                dataType: "json",
                data    : { ids: ids },
                url     : 'representative/delete',
                method    : 'delete',
                success: function (response) {
                    console.log(response);
                    $(".js-destroy:checked").closest('tr').remove();
                    $(".js-destroy").prop("checked", "");
                },
                error: function (xhr, err) { 
                    console.log("Error: " + xhr + " " + err);
                }
            });
            // console.log('sss');
            }
        });
    });
    </script>
@endsection