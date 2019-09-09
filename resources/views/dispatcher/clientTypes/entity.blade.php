<div class="form-group">
    <label for="ent_organization" class="col-md-4 control-label">Наименование организации</label>

    <div class="col-md-6">
        <input id="ent_organization" type="text" class="form-control" name="ent_organization" required autofocus>
    </div>
</div>

<div class="form-group">
    <label for="ent_INN" class="col-md-4 control-label">ИНН</label>

    <div class="col-md-6">
        <input id="ent_INN" type="text" class="form-control" name="ent_INN" required>
    </div>
</div>

<div class="form-group">
    <label for="ent_OGRN" class="col-md-4 control-label">ОГРН</label>

    <div class="col-md-6">
        <input id="ent_OGRN" type="text" class="form-control" name="ent_OGRN" required>
    </div>
</div>

<div class="form-group">
    <label for="ent_email" class="col-md-4 control-label">E-Mail адрес</label>

    <div class="col-md-6">
        <input id="ent_email" type="email" class="form-control" name="ent_email" required>
    </div>
</div>

<div class="form-group">
    <label for="ent_phone_number" class="col-md-4 control-label">Номер телефона</label>

    <div class="col-md-6">
        <input id="ent_phone_number" type="tel" class="form-control" name="ent_phone_number" required>
    </div>
</div>

<div class="form-group">
    <label for="ent_gendir" class="col-md-4 control-label">Генеральный директор ФИО</label>

    <div class="col-md-6">
        <input id="ent_gendir" type="text" class="form-control" name="ent_gendir" required>
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

<script>
    $(document).ready(function () {
        $('input[type=tel]').mask("+7 (999) 999-99-99");
    });

    $(document).ready(function () {
        $('input[id=ent_INN]').mask("9999999999");
    });

    $(document).ready(function () {
        $('input[id=ent_OGRN]').mask("9999999999999");
    });
</script>