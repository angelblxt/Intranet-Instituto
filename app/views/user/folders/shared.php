<script>

	function uploadForm()
	{

		$('#uploadForm').slideToggle('slow');

	}

</script>

<div class="sectionTitle"><?php echo $titleSection; ?></div>

<?php 

\helpers\messages::comprobarErrores();

if(!empty($previous)): ?>

	<button onClick="location.href='<?php echo DIR ?>folders/<?php echo $previous ?>'" class="button button-rounded button-flat-primary button-tiny" style="padding: 0 10px; margin-bottom: 15px"><i class="fa fa-chevron-left"></i> Atrás</button>

<?php endif; ?>

<?php if(count($files) == 0): ?>

	<div class="folders_nothing"><i class="fa fa-times-circle"></i> No hay elementos compartidos contigo.</div>

<?php else: ?>

	<?php foreach($files as $file): ?>

		<div class="folders_file">

			<div class="icono"> <?php echo $file['icon'] ?> </div>

			<?php if($file['type'] == 'dir'): ?>

				<a href="<?php echo DIR ?>folders/<?php echo $file['next'] ?>"><div class="nombre <?php echo $file['type'] ?>"> <?php echo $file['name'] ?> </div></a>

			<?php else: ?>

				<div class="nombre <?php echo $file['type'] ?>"> <?php echo $file['name'] ?> </div>

			<?php endif; ?>

			<div class="opciones">

				<div class="size"> <?php echo $file['size'] ?> </div>
				
				<span class="hint--rounded hint--bounce hint--bottom" data-hint="Descargar">
					<button onClick="location.href='<?php echo DIR ?>folders/<?php echo $file['next'] ?>/download'" class="button button-rounded button-flat-primary button-tiny" style="padding: 0 15px"><i class="fa fa-download" style="margin: 0 -10px"></i></button>
				</span>
				<span class="hint--rounded hint--bounce hint--bottom" data-hint="Renombrar">
					<button onClick="location.href='<?php echo DIR ?>folders/<?php echo $file['next'] ?>/rename'" class="button button-rounded button-flat-action button-tiny" style="padding: 0 15px"><i class="fa fa-pencil" style="margin: 0 -10px"></i></button>
				</span>
				<span class="hint--rounded hint--bounce hint--bottom" data-hint="Compartir">
					<button onClick="location.href='<?php echo DIR ?>folders/<?php echo $file['next'] ?>/share'" class="button button-rounded button-flat-highlight button-tiny" style="padding: 0 15px"><i class="fa fa-share-alt" style="margin: 0 -10px"></i></button>
				</span>
				<span class="hint--rounded hint--bounce hint--bottom" data-hint="Eliminar">
					<button onClick="location.href='<?php echo DIR ?>folders/<?php echo $file['next'] ?>/delete/0'" class="button button-rounded button-flat-caution button-tiny" style="padding: 0 15px"><i class="fa fa-times" style="margin: 0 -10px"></i></button>
				</span>
			
			</div>

		</div>

	<?php endforeach; ?>

<?php endif; ?>