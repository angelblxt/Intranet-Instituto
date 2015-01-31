<div class="sectionTitle">Renombrar Archivo</div>

<?php \helpers\messages::comprobarErrores(); ?>

<div class="folders_showName"><i class="fa fa-file-o"></i> <?php echo $file['decrypted'] ?></div>

<form method="post" action="<?php echo DIR; ?>post/rename/file" autocomplete="off">

	<center>

		<div class="wrapper">
			<div class="inputIcon"><i class="fa fa-file"></i></div>
			<input type="text" name="nombre" value="<?php echo $file['actualName'] ?>" placeholder="Nombre del Archivo"/>
		</div>

		<input type="hidden" name="token" value="<?php echo $token; ?>">
		<input type="hidden" name="file" value="<?php echo $file['encrypted'] ?>">

		<button type="submit" name="rename" class="button button-rounded button-flat-action" style="margin-top: 10px"><i class="fa fa-check-circle" style="margin-left: -4px"></i> Renombrar</button>

	</center>

</form>

<center>

	<button onClick="location.href='<?php echo DIR ?>folders/<?php echo $folderOfFile ?>'" class="button button-rounded button-flat-primary" style="margin-top: 10px"><i class="fa fa-chevron-left" style="margin-left: -4px"></i> Volver atrás</button>

</center>