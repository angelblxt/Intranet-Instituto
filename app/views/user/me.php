<center>

	<?php if($isTeacher === true || $isAdmin === true): ?>

		<div class="preferences_button brown"><a href="<?php echo DIR; ?>admin"><i class="fa fa-shield"></i><span>Administración</span></a></div>

	<?php endif; ?>

	<div class="preferences_button green"><a href="<?php echo DIR; ?>cloud"><i class="fa fa-folder"></i><span>Carpetas</span></a></div>
	<div class="preferences_button blue"><a href="<?php echo DIR; ?>messages"><i class="fa fa-envelope"></i><span>Mensajes Privados</span></a></div>
	<div class="preferences_button orange"><a href="<?php echo DIR; ?>preferences"><i class="fa fa-cogs"></i><span>Preferencias</span></a></div>
	<div class="preferences_button brown"><a href="<?php echo DIR; ?>about"><i class="fa fa-info-circle"></i><span>Acerca de</span></a></div>

</center>