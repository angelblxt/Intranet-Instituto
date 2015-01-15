<div class="title"><span> Intranet </span></div>

<?php \helpers\messages::comprobarErrores(); ?>

<form method="post" action="post/login" autocomplete="off">

	<center>

		<div class="wrapper">
			<div class="inputIcon"><i class="fa fa-user"></i></div>
			<input type="text" name="user" placeholder="Usuario"/>
		</div>
		<div class="wrapper">
			<div class="inputIcon"><i class="fa fa-lock"></i></div>
			<input type="password" name="password" placeholder="Contraseña"/>
		</div>

		<input type="hidden" name="token" value="<?php echo $token; ?>">

		<button type="submit" name="login" class="button button-rounded button-flat-primary" style="margin-top: 10px"><i class="fa fa-sign-in" style="margin-left: -4px"></i> Entrar</button>

	</center>

</form>