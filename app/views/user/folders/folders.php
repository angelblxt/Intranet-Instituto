<div class="sectionTitle"><?php echo $titleSection; ?></div>

<?php \helpers\messages::comprobarErrores(); ?>

<?php if(!empty($previous)): ?>

	<button onClick="location.href='<?php echo DIR ?>folders/<?php echo $previous ?>'" class="button button-rounded button-flat-primary button-tiny" style="padding: 0 10px; margin-bottom: 15px"><i class="fa fa-chevron-left"></i> Atrás</button>

<?php endif; ?>

	<button onClick="location.href='<?php echo DIR ?>folders/<?php echo $actual ?>/new/folder'" class="button button-rounded button-flat-action button-tiny" style="padding: 0 10px; margin-bottom: 15px"><i class="fa fa-folder"></i> Nueva Carpeta</button>

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
				<button onClick="location.href=''" class="button button-rounded button-flat-primary button-tiny" style="padding: 0 15px"><i class="fa fa-download" style="margin: 0 -10px"></i></button>
			</span>
			<span class="hint--rounded hint--bounce hint--bottom" data-hint="Renombrar">
				<button onClick="location.href='<?php echo $file['buttons']['rename'] ?>'" class="button button-rounded button-flat-action button-tiny" style="padding: 0 15px"><i class="fa fa-pencil" style="margin: 0 -10px"></i></button>
			</span>
			<span class="hint--rounded hint--bounce hint--bottom" data-hint="Opciones">
				<button onClick="location.href=''" class="button button-rounded button-flat-highlight button-tiny" style="padding: 0 15px"><i class="fa fa-cog" style="margin: 0 -10px"></i></button>
			</span>
			<span class="hint--rounded hint--bounce hint--bottom" data-hint="Eliminar">
				<button onClick="location.href='<?php echo $file['buttons']['delete'] ?>'" class="button button-rounded button-flat-caution button-tiny" style="padding: 0 15px"><i class="fa fa-times" style="margin: 0 -10px"></i></button>
			</span>
		
		</div>

	</div>

<?php endforeach; ?>