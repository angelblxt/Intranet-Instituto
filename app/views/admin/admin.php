<div class="sectionTitle">Panel de Administración</div>

<center>

	<?php if($isAdmin === true): ?>

		<div class="preferences_button blue"><a href="<?php echo DIR; ?>admin/users"><i class="fa fa-user"></i><span>Usuarios</span></a></div>

	<?php endif; ?>

	<div class="preferences_button green"><a href="<?php echo DIR; ?>admin/logs"><i class="fa fa-list-alt"></i><span>Actividad</span></a></div>

</center>