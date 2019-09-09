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
    
                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <label for="name" class="col-md-4 control-label">Имя</label>
    
                    <div class="col-md-6">
                        <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required
                               autofocus>
    
                        @if ($errors->has('name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
    
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="email" class="col-md-4 control-label">E-Mail адрес</label>
    
                    <div class="col-md-6">
                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}"
                               required>
    
                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
    
                <div class="form-group{{ $errors->has('phone_number') ? ' has-error' : '' }}">
                    <label for="phone_number" class="col-md-4 control-label">Номер телефона</label>
    
                    <div class="col-md-6">
                        <input id="phone_number" type="tel" class="form-control" name="phone_number"
                               value="{{ old('phone_number') }}" required>
    
                        @if ($errors->has('phone_number'))
                            <span class="help-block">
                                <strong>{{ $errors->first('phone_number') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
    
                <div class="form-group{{ $errors->has('organization') ? ' has-error' : '' }}">
                    <label for="organization" class="col-md-4 control-label">Организация</label>
    
                    <div class="col-md-6">
                        <input id="organization" type="text" class="form-control" name="organization"
                               value="{{ old('organization') }}" required>
    
                        @if ($errors->has('organization'))
                            <span class="help-block">
                                <strong>{{ $errors->first('organization') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
    
                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label for="password" class="col-md-4 control-label">Пароль</label>
    
                    <div class="col-md-6">
                        <input id="password" type="password" class="form-control" name="password" required>
    
                        @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
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
                    $('#clientCreateForm').html('1');
                }
                if(clientType == 'Individual')
                {
                    $('#clientCreateForm').html('2');
                }

            });
        });
    </script>
@endsection