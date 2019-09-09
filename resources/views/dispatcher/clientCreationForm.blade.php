@extends('layouts.profileApp')

@section('content')
    <h1>Создание клиента</h1>
    <div class="col-sm-12">
        <form class="form-horizontal" method="POST" action="{{ route('auth.client.store') }}">
            {{ csrf_field() }}

            @php
                use App\Models\User;$user = Illuminate\Support\Facades\Auth::user();
                if ($user instanceof User && $user->hasRole('dispatcher'))
                {
                     $repId = $user->dispatcher->representative;
                }
                else
                {
                    $repId = $user->id;
                }
            @endphp

            <input type="hidden" name="representative" value="{{ $repId }}">

            <div class="form-group">
                <div class="col-md-6">
                    <select name="clientType" id="clientType" class="form-control">
                        <option value="Individual">Физическое лицо</option>
                        <option value="Entity">Юридическое лицо</option>
                    </select>
                </div>
            </div>

            <div id="clientCreateForm">
    
                <div class="form-group">
                    <label for="indv_name" class="col-md-4 control-label">ФИО</label>
                
                    <div class="col-md-6">
                        <input id="indv_name" type="text" class="form-control" name="indv_name" required autofocus>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="indv_email" class="col-md-4 control-label">E-Mail адрес</label>
                
                    <div class="col-md-6">
                        <input id="indv_email" type="email" class="form-control" name="indv_email" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="indv_phone_number" class="col-md-4 control-label">Номер телефона</label>
                
                    <div class="col-md-6">
                        <input id="indv_phone_number" type="tel" class="form-control" name="indv_phone_number" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="indv_passport" class="col-md-4 control-label">Серия и номер паспорта</label>
                
                    <div class="col-md-6">
                        <input id="indv_passport" type="text" class="form-control" name="indv_passport" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password" class="col-md-4 control-label">Пароль</label>
                
                    <div class="col-md-6">
                        <input id="password" type="password" class="form-control" name="password" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password-confirm" class="col-md-4 control-label">Повторите пароль</label>
                
                    <div class="col-md-6">
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                               required>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <div class="col-md-6 col-md-offset-4">
                    <button type="submit" class="btn btn-primary">
                        Создать клиента
                    </button>
                </div>
            </div>
        </form>
    </div>
    <script>
        $('.left-menu > .nav > *:nth-child(6)').addClass('active');
        $('.left-menu > .nav > *:nth-child(1)').removeClass('active');

        $(document).ready(function()
        {
            $(document).on('change', '#clientType', function() {
                let clientType = $('#clientType').val();
                if(clientType == 'Entity')
                {
                    $.ajax({
                    headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    dataType: "html",
                    data: {clientType: clientType},
                    url     : '../clients/clientType',
                    method    : 'post',
                    success: function (response) {
                        $('#clientCreateForm').html(response);
                        // console.log(response);
                    },
                    error: function (xhr, err) { 
                        console.log(err + " " + xhr);
                    }
                    });
                }
                if(clientType == 'Individual')
                {
                    $.ajax({
                    headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    dataType: "html",
                    data: {clientType: clientType},
                    url     : '../clients/clientType',
                    method    : 'post',
                    success: function (response) {
                        $('#clientCreateForm').html(response);
                        // console.log(response);
                    },
                    error: function (xhr, err) { 
                        console.log(err + " " + xhr);
                    }
                    });
                }

            });
        });

    $('input[id=indv_passport]').mask("99 99 999999");

    </script>
@endsection