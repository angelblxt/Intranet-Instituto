<div class="sectionTitle">Editar a <?php echo $name['nombre'] ?> <?php echo $name['apellidos'] ?></div>

<?php \helpers\messages::comprobarErrores(); ?>

<form method="post" action="<?php echo DIR; ?>post/editUser" autocomplete="off">

	<center>

		<div class="wrapper">
			<div class="inputIcon"><i class="fa fa-user"></i></div>
			<input type="text" name="nombre" value="<?php echo $name['nombre'] ?>" placeholder="Nombre"/>
		</div>

		<div class="wrapper">
			<div class="inputIcon"><i class="fa fa-user"></i></div>
			<input type="text" name="apellidos" value="<?php echo $name['apellidos'] ?>" placeholder="Apellidos"/>
		</div>

		<div class="wrapper">
			<div class="inputIcon"><i class="fa fa-user"></i></div>
			<input type="text" name="user" value="<?php echo $user ?>" placeholder="Usuario de Entrada"/>
		</div>

		<div class="wrapper">
			<div class="inputIcon"><i class="fa fa-lock"></i></div>
			<input type="password" name="password" placeholder="Contraseña (Dejar vacía para no modificar)"/>
		</div>

		<div class="wrapper">
			<div class="inputIcon"><i class="fa fa-graduation-cap"></i></div>
			<select name="curso">

				<?php

					foreach($cursos as $item => $value){

						$selected = ($item == $curso)? 'selected' : '';

						echo '<option value="'. $item .'" '. $selected .'>'. $value .'</option>';

					}

				?>

			</select>
		</div>

		<div class="wrapper">
			<div class="inputIcon"><i class="fa fa-shield"></i></div>
			<select name="rango">

				<?php

					foreach($rangos as $item => $value){

						$selected = ($item == $rango)? 'selected' : '';

						echo '<option value="'. $item .'" '. $selected .'>'. $value .'</option>';

					}

				?>

			</select>
		</div>

		<input type="hidden" name="hash" value="<?php echo $hash; ?>">
		<input type="hidden" name="token" value="<?php echo $token; ?>">

		<button type="submit" name="edit" class="button button-rounded button-flat-action" style="margin-top: 10px"><i class="fa fa-check-circle" style="margin-left: -4px"></i> Guardar</button>

	</center>

</form>

<center>

	<button onClick="location.href='<?php echo DIR ?>admin/users'" class="button button-rounded button-flat-primary" style="margin-top: 10px"><i class="fa fa-chevron-left" style="margin-left: -4px"></i> Volver atrás</button>

</center>