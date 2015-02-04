<div class="sectionTitle">Importar Usuarios</div>

<?php \helpers\messages::comprobarErrores(); ?>

<p> El formato del archivo CSV debe ser: <b>Usuario;Nombre;Apellidos;Curso</b>. Las contraseñas por defecto para todos los usuarios será <b>IESBENJAMINJARNES</b>. </p>

<form method="post" action="<?php echo DIR; ?>post/importUsers" enctype="multipart/form-data">

	<input type="file" name="csv" accept="text/csv"><br/>

	<center>

		<button type="submit" name="import" class="button button-rounded button-flat-action" style="margin-top: 10px"><i class="fa fa-check-circle" style="margin-left: -4px"></i> Importar</button>

	</center>

</form>

<center>

	<button onClick="location.href='<?php echo DIR ?>admin/users'" class="button button-rounded button-flat-primary" style="margin-top: 10px"><i class="fa fa-chevron-left" style="margin-left: -4px"></i> Volver atrás</button>

</center>