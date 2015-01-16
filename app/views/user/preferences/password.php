<div class="sectionTitle">Cambio de Contraseña</div>

<?php \helpers\messages::comprobarErrores(); ?>

<form method="post" action="<?php echo DIR; ?>post/changePassword" autocomplete="off">

	<center>

		<div class="wrapper">
			<div class="inputIcon"><i class="fa fa-lock"></i></div>
			<input type="password" name="pass_actual" placeholder="Contraseña Actual"/>
		</div>
		<div class="wrapper">
			<div class="inputIcon"><i class="fa fa-lock"></i></div>
			<input type="password" name="pass_nueva_1" placeholder="Nueva Contraseña"/>
		</div>
		<div class="wrapper">
			<div class="inputIcon"><i class="fa fa-lock"></i></div>
			<input type="password" name="pass_nueva_2" placeholder="Repite tu Nueva Contraseña"/>
		</div>

		<input type="hidden" name="token" value="<?php echo $token; ?>">

		<button type="submit" name="change" class="button button-rounded button-flat-action" style="margin-top: 10px"><i class="fa fa-check-circle" style="margin-left: -4px"></i> Guardar</button>

	</center>

</form>

<center>

	<button onClick="location.href='<?php echo DIR ?>preferences'" class="button button-rounded button-flat-primary" style="margin-top: 10px"><i class="fa fa-chevron-left" style="margin-left: -4px"></i> Volver atrás</button>

</center>