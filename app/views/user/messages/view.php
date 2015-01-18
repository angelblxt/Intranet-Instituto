<div class="private_message large">

	<div class="emisor">

				<div class="circle" style="background: #<?php echo $mensaje['persona']['circleColor'] ?>"><?php echo $mensaje['persona']['inicial'] ?></div>

	</div>

	<div class="message_content">

		<div class="message_title"><?php echo $mensaje['asunto'] ?></div>

		<div class="message_text"><?php echo $mensaje['contenido'] ?></div>

		<div class="message_options">

			<div class="message_sender">Enviado a: <b><?php echo $mensaje['persona']['nombre']['nombre'] .' '. $mensaje['persona']['nombre']['apellidos'] ?></b></div>
			<div class="message_time"><?php echo $mensaje['tiempo'] ?></div>

			<div style="clear: both"></div>

		</div>

	</div>

</div>

<center>

	<button onClick="location.href='<?php echo DIR ?>messages'" class="button button-rounded button-flat-primary" style="margin-top: 40px"><i class="fa fa-chevron-left" style="margin-left: -4px"></i> Volver atrás</button>
	
	<span class="hint--rounded hint--bounce hint--bottom" data-hint="Eliminar Mensaje Privado">

		<button onClick="location.href='<?php echo DIR ?>messages/<?php echo $mensaje['hash'] ?>/delete/0'" class="button button-rounded button-flat-caution" style="margin-top: 40px"><i class="fa fa-times-circle"></i></button>

	</span>

</center>