<div class="sectionTitle">Nuevo Usuario</div>

<?php \helpers\messages::comprobarErrores(); ?>

<form method="post" action="<?php echo DIR; ?>post/addUser" autocomplete="off">

	<center>

		<div class="wrapper">
			<div class="inputIcon"><i class="fa fa-user"></i></div>
			<input type="text" name="nombre" placeholder="Nombre"/>
		</div>

		<div class="wrapper">
			<div class="inputIcon"><i class="fa fa-user"></i></div>
			<input type="text" name="apellidos" placeholder="Apellidos"/>
		</div>

		<div class="wrapper">
			<div class="inputIcon"><i class="fa fa-user"></i></div>
			<input type="text" name="user" placeholder="Usuario de Entrada"/>
		</div>

		<div class="wrapper">
			<div class="inputIcon"><i class="fa fa-lock"></i></div>
			<input type="password" name="password1" placeholder="Contraseña"/>
		</div>

		<div class="wrapper">
			<div class="inputIcon"><i class="fa fa-lock"></i></div>
			<input type="password" name="password2" placeholder="Repetir la Contraseña"/>
		</div>

		<div class="wrapper">
			<div class="inputIcon"><i class="fa fa-graduation-cap"></i></div>
			<select name="curso">

				<?php

					foreach($cursos as $item => $value){

						echo '<option value="'. $item .'">'. $value .'</option>';

					}

				?>

			</select>
		</div>

		<div class="wrapper">
			<div class="inputIcon"><i class="fa fa-shield"></i></div>
			<select name="rango">

				<?php

					foreach($rangos as $item => $value){

						echo '<option value="'. $item .'">'. $value .'</option>';

					}

				?>

			</select>
		</div>

		<input type="hidden" name="token" value="<?php echo $token; ?>">

		<button type="submit" name="add" class="button button-rounded button-flat-action" style="margin-top: 10px"><i class="fa fa-check-circle" style="margin-left: -4px"></i> Guardar</button>

	</center>

</form>

<center>

	<button onClick="location.href='<?php echo DIR ?>admin/users'" class="button button-rounded button-flat-primary" style="margin-top: 10px"><i class="fa fa-chevron-left" style="margin-left: -4px"></i> Volver atrás</button>

</center>