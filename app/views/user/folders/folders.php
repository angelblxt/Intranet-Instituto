<div class="sectionTitle">Carpeta Personal</div>

<?php if(!empty($previous)): ?>

	<a href="<?php echo DIR ?>folders/<?php echo $previous ?>">Atrás</a>

<?php endif; ?>

<?php foreach($files as $file): ?>

	<div class="folders_file">

		<div class="icono"> <?php echo $file['icon'] ?> </div>

		<?php if($file['type'] == 'dir'): ?>

			<a href="<?php echo DIR ?>folders/<?php echo $file['next'] ?>"><div class="nombre <?php echo $file['type'] ?>"> <?php echo $file['name']['decrypted'] ?> </div></a>

		<?php else: ?>

			<div class="nombre <?php echo $file['type'] ?>"> <?php echo $file['name']['decrypted'] ?> </div>

		<?php endif; ?>

		<div class="opciones">

			<div class="size"> <?php echo $file['size'] ?> </div>
			
			<span class="hint--rounded hint--bounce hint--bottom" data-hint="Descargar">
				<button onClick="location.href=''" class="button button-rounded button-flat-primary button-tiny"><i class="fa fa-download" style="margin: 0 -10px"></i></button>
			</span>
			<span class="hint--rounded hint--bounce hint--bottom" data-hint="Renombrar">
				<button onClick="location.href=''" class="button button-rounded button-flat-action button-tiny"><i class="fa fa-pencil" style="margin: 0 -10px"></i></button>
			</span>
			<span class="hint--rounded hint--bounce hint--bottom" data-hint="Eliminar">
				<button onClick="location.href=''" class="button button-rounded button-flat-caution button-tiny"><i class="fa fa-times-circle" style="margin: 0 -10px"></i></button>
			</span>
		
		</div>

	</div>

<?php endforeach; ?>