<div class="message_view">
	
	<div class="data">

		<div class="emisor">
			
			<div class="circle" style="background: #<?php echo $mensaje['persona']['circleColor'] ?>"><?php echo $mensaje['persona']['inicial'] ?></div>

		</div>

		<div class="nombreEmisor"> <i class="fa fa-chevron-right"></i> <?php echo $mensaje['persona']['nombre']['nombre'] .' '. $mensaje['persona']['nombre']['apellidos'] ?></div>

	</div>

	<div class="sectionTitle"><?php echo $mensaje['asunto'] ?></div>

	<div class="message_contenido"> <?php echo $mensaje['contenido'] ?> </div>

	<div class="message_sended"><?php echo $mensaje['tiempo'] ?>.</div>

</div>

<center>

	<button onClick="location.href='<?php echo DIR ?>messages'" class="button button-rounded button-flat-primary" style="margin-top: 40px"><i class="fa fa-chevron-left" style="margin-left: -4px"></i> Volver atrás</button>
	
	<span class="hint--rounded hint--bounce hint--bottom" data-hint="Eliminar Mensaje Privado">

		<button onClick="location.href='<?php echo DIR ?>messages/<?php echo $mensaje['hash'] ?>/delete/0'" class="button button-rounded button-flat-caution" style="margin-top: 40px"><i class="fa fa-times-circle"></i></button>

	</span>

</center>