<script>

	$(function() {

		$( "#search" ).autocomplete(
		{

			source: '<?php echo DIR; ?>post/searchUser'

		});

	});

</script>

<div class="sectionTitle">Nuevo Mensaje Privado</div>

<?php \helpers\messages::comprobarErrores(); ?>

<form method="post" action="<?php echo DIR; ?>post/sendMessage" autocomplete="off">

	<center>

		<div class="wrapper">
			<div class="inputIcon"><i class="fa fa-user"></i></div>
			<input type="text" name="nombreCompleto" id="search" placeholder="Nombre del Receptor"/>
		</div>

		<div class="wrapper">
			<div class="inputIcon"><i class="fa fa-bookmark"></i></div>
			<input type="text" name="asunto" id="search" placeholder="Asunto del Mensaje"/>
		</div>

		<textarea name="contenido" placeholder="Contenido del Mensaje"></textarea>

		<input type="hidden" name="token" value="<?php echo $token; ?>">

		<button type="submit" name="send" class="button button-rounded button-flat-action" style="margin-top: 10px"><i class="fa fa-send" style="margin-left: -4px"></i> Enviar Mensaje</button>

	</center>

</form>

<center>

	<button onClick="location.href='<?php echo DIR ?>messages'" class="button button-rounded button-flat-primary" style="margin-top: 10px"><i class="fa fa-chevron-left" style="margin-left: -4px"></i> Volver atrás</button>

</center>