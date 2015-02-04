<center>

	<i class="fa fa-warning messages_no"></i><br/>

	<div class="messages_no_text">¿Estás seguro de eliminar a <b><?php echo $name['nombre'] ?> <?php echo $name['apellidos'] ?></b>?</div>

	<p>

		<button onClick="location.href='<?php echo DIR ?>admin/users/<?php echo $hash ?>/delete/1'" class="button button-rounded button-flat-action" style="margin-top: 10px"><i class="fa fa-check-circle" style="margin-left: -4px"></i> Si</button>
		<button onClick="location.href='<?php echo DIR ?>admin/users'" class="button button-rounded button-flat-caution" style="margin-top: 10px"><i class="fa fa-check-circle" style="margin-left: -4px"></i> No</button>
	
	</p>

</center>