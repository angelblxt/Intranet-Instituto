<?php \helpers\messages::comprobarErrores(); ?>

<center>

	<div class="preferences_button green"><a href="<?php echo DIR ?>messages/new"><i class="fa fa-plus-circle"></i><span>Nuevo Mensaje</span></a></div>
	<div class="preferences_button orange"><a href="<?php echo DIR ?>messages/in"><i class="fa fa-chevron-circle-down"></i><span>Bandeja de Entrada <?php echo $sinLeer ?></span></a></div>
	<div class="preferences_button blue"><a href="<?php echo DIR ?>messages/out"><i class="fa fa-chevron-circle-up"></i><span>Bandeja de Salida</span></a></div>

</center>