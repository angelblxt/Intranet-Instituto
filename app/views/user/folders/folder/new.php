<div class="sectionTitle">Nueva Carpeta</div>

<?php \helpers\messages::comprobarErrores(); ?>

<div class="folders_showName"><i class="fa fa-folder"></i> <?php echo $folder['decrypted'] ?></div>

<form method="post" action="<?php echo DIR; ?>post/new/folder" autocomplete="off">

	<center>

		<div class="wrapper">
			<div class="inputIcon"><i class="fa fa-folder"></i></div>
			<input type="text" name="nombre" placeholder="Nombre de la Carpeta"/>
		</div>

		<input type="hidden" name="token" value="<?php echo $token; ?>">
		<input type="hidden" name="folder" value="<?php echo $folder['encrypted'] ?>">

		<button type="submit" name="create" class="button button-rounded button-flat-action" style="margin-top: 10px"><i class="fa fa-check-circle" style="margin-left: -4px"></i> Crear</button>

	</center>

</form>

<center>

	<button onClick="location.href='<?php echo DIR ?>folders/<?php echo $folder['encrypted'] ?>'" class="button button-rounded button-flat-primary" style="margin-top: 10px"><i class="fa fa-chevron-left" style="margin-left: -4px"></i> Volver atrás</button>

</center>