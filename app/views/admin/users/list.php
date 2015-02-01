<div class="sectionTitle">Listado de Usuarios</div>

<?php \helpers\messages::comprobarErrores(); ?>

<?php if(count($users) == 0): ?>

	<center>

		<i class="fa fa-times-circle-o messages_no"></i><br/>

		<div class="messages_no_text">No hay Usuarios Registrados</div>

		<p>

			<button onClick="location.href='<?php echo DIR ?>admin'" class="button button-rounded button-flat-primary" style="margin-top: 10px"><i class="fa fa-chevron-left" style="margin-left: -4px"></i> Volver atrás</button>

		</p>

	</center>

<?php else: ?>

	<center>

		<button onClick="location.href='<?php echo DIR ?>admin'" class="button button-rounded button-flat-primary" style="margin: 10px 0"><i class="fa fa-chevron-left" style="margin-left: -4px"></i> Volver atrás</button><br/>

		<button onClick="location.href='<?php echo DIR ?>admin/users/new'" class="button button-rounded button-flat-action" style="margin: 10px 0"><i class="fa fa-plus-circle" style="margin-left: -4px"></i> Nuevo Usuario</button>
		<button onClick="location.href='<?php echo DIR ?>admin/users/import'" class="button button-rounded button-flat-action" style="margin: 10px 0"><i class="fa fa-upload" style="margin-left: -4px"></i> Importar Usuarios CSV</button><br/>

		<?php echo $page_links; ?><br/>

	</center>

	<table width="100%">

		<tr class="titulo">

			<td> <i class="fa fa-user"></i> Alumno </td>
			<td> <i class="fa fa-user"></i> Usuario </td>
			<td> <i class="fa fa-graduation-cap"></i> Curso </td>
			<td> <i class="fa fa-calendar"></i> Fecha de Registro </td>
			<td> <i class="fa fa-cogs"></i> Acciones </td>

		</tr>

		<?php foreach($users as $user): ?>

			<tr>

				<td> <?php echo $user['name']['nombre'] ?> <?php echo $user['name']['apellidos'] ?> </td>
				<td> <?php echo $user['user'] ?> </td>
				<td> <?php echo $user['curso'] ?> </td>
				<td> <?php echo $user['tiempo_registro'] ?> </td>
				<td>

					<a href="<?php echo DIR ?>admin/users/<?php echo $user['hash'] ?>/edit"><i class="fa fa-pencil"></i> Editar</a> 
					<a href="<?php echo DIR ?>admin/users/<?php echo $user['hash'] ?>/delete/0" style="margin-left: 10px"><i class="fa fa-times-circle"></i> Eliminar</a>

				</td>

			</tr>

		<?php endforeach; ?>

	</table><br/>

	<center>

		<?php echo $page_links; ?><br/>

		<?php if($isAdmin === true): ?>

			<button onClick="location.href='<?php echo DIR ?>admin/users/download'" class="button button-rounded button-flat-highlight" style="margin-top: 10px"><i class="fa fa-download" style="margin-left: -4px"></i> Descargar Archivo CSV</button> 
			
		<?php endif; ?>

		<button onClick="location.href='<?php echo DIR ?>admin'" class="button button-rounded button-flat-primary" style="margin-top: 10px"><i class="fa fa-chevron-left" style="margin-left: -4px"></i> Volver atrás</button>

	</center>

<?php endif; ?>

