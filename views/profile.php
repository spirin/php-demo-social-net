<h3>Профиль</h3>

<hr /><form style="max-width: 400px" method="post">
	<div class="form-group row">
		<label for="firstname" class="col-sm-4 col-form-label">Имя</label>
		<div class="col-sm-8">
			<input type="firstname" class="form-control form-control-sm" name="firstname" id="firstname" value="<?php echo htmlspecialchars($sessionUser['firstname']); ?>">
		</div>
	</div>
	<div class="form-group row">
		<label for="lastname" class="col-sm-4 col-form-label">Фамилия</label>
		<div class="col-sm-8">
			<input type="lastname" class="form-control form-control-sm" name="lastname" id="lastname" value="<?php echo htmlspecialchars($sessionUser['lastname']); ?>">
		</div>
	</div>
	<div class="form-group row">
		<label for="borndate" class="col-sm-4 col-form-label">Дата рождения</label>
		<div class="col-sm-8">
			<input type="borndate" class="form-control form-control-sm" name="borndate" id="borndate" value="<?php echo date('d.m.Y', $sessionUser['borndate']); ?>">
		</div>
	</div>
	<div class="form-group row">
		<label for="sex" class="col-sm-4 col-form-label">Пол</label>
		<div class="col-sm-8">
			<div class="form-check form-check-inline">
				<label class="form-check-label">
					<input class="form-check-input" type="radio" name="sex" id="sex" value="1"<?php echo empty($sessionUser['sex']) || $sessionUser['sex'] === '1' ? ' checked=""' : '' ?>> Муж
				</label>
			</div>
			<div class="form-check form-check-inline">
				<label class="form-check-label">
					<input class="form-check-input" type="radio" name="sex" id="sex" value="0"<?php echo $sessionUser['sex'] === '0' ? ' checked=""' : '' ?>> Жен
				</label>
			</div>
		</div>
	</div>
	<div class="form-group row">
		<label for="about" class="col-sm-4 col-form-label">О себе</label>
		<div class="col-sm-8">
			<textarea class="form-control form-control-sm" name="about" id="about" rows="3"><?php echo htmlspecialchars($sessionUser['about']); ?></textarea>
		</div>
	</div>
	<div class="form-group row">
		<label for="password" class="col-sm-4 col-form-label">Пароль</label>
		<div class="col-sm-8">
			<input type="password" class="form-control form-control-sm" name="password" id="password" value="">
		</div>
	</div>
	<button type="submit" class="btn btn-primary" name="submit" value="submit">Обновить</button>
</form>


<?php $this->scriptStart(); ?>

<script type="text/javascript">
	$('#borndate').datepicker({
		format: "dd.mm.yyyy",
		language: "ru",
		autoclose: true,
		todayHighlight: true
	});
</script>
<?php
$this->scriptEnd();
