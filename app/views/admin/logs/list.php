<div class="sectionTitle">Actividad de los Usuarios</div>

<?php \helpers\messages::comprobarErrores(); ?>

<?php if(count($logs) == 0): ?>

	<center>

		<i class="fa fa-times-circle-o messages_no"></i><br/>

		<div class="messages_no_text">No hay Registro de Actividad</div>

		<p>

			<button onClick="location.href='<?php echo DIR ?>admin'" class="button button-rounded button-flat-primary" style="margin-top: 10px"><i class="fa fa-chevron-left" style="margin-left: -4px"></i> Volver atrás</button>

		</p>

	</center>

<?php else: ?>

	<center>

		<button onClick="location.href='<?php echo DIR ?>admin'" class="button button-rounded button-flat-primary" style="margin: 10px 0"><i class="fa fa-chevron-left" style="margin-left: -4px"></i> Volver atrás</button><br/>

		<?php echo $page_links; ?><br/>

	</center>

	<table width="100%">

		<tr class="titulo">

			<td> <i class="fa fa-user"></i> Alumno </td>
			<td> <i class="fa fa-cog"></i> Acción </td>
			<td> <i class="fa fa-laptop"></i> Dirección I.P </td>
			<td> <i class="fa fa-calendar"></i> Fecha </td>

		</tr>

		<?php foreach($logs as $log): ?>

			<tr>

				<td> <?php echo $log['name']['nombre'] ?> <?php echo $log['name']['apellidos'] ?> </td>
				<td> <?php echo $log['contenido'] ?> </td>
				<td> <?php echo $log['ip'] ?> </td>
				<td> <?php echo $log['fecha'] ?> </td>

			</tr>

		<?php endforeach; ?>

	</table><br/>

	<center>

		<?php echo $page_links; ?><br/>

		<?php if($isAdmin === true): ?>

			<button onClick="location.href='<?php echo DIR ?>admin/logs/download'" class="button button-rounded button-flat-highlight"><i class="fa fa-download" style="margin-left: -4px"></i> Descargar Archivo CSV</button> 
			<button onClick="location.href='<?php echo DIR ?>admin/logs/delete/0'" class="button button-rounded button-flat-caution"><i class="fa fa-times-circle" style="margin-left: -4px"></i> Vaciar Registro de Actividad</button><br/><br/>

		<?php endif; ?>

		<button onClick="location.href='<?php echo DIR ?>admin'" class="button button-rounded button-flat-primary" style="margin-top: 10px"><i class="fa fa-chevron-left" style="margin-left: -4px"></i> Volver atrás</button>

	</center>

<?php endif; ?>