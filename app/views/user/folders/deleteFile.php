<center>

	<i class="fa fa-warning messages_no"></i><br/>

	<div class="messages_no_text">¿Estás seguro de eliminar éste Archivo?<br/><b style="font-size: 20px"><?php echo $file['actualName'] ?></b></div>

	<p>

		<button onClick="location.href='<?php echo DIR ?>folders/<?php echo $file['encrypted'] ?>/delete/file/1'" class="button button-rounded button-flat-action" style="margin-top: 10px"><i class="fa fa-check-circle" style="margin-left: -4px"></i> Si</button>
		<button onClick="location.href='<?php echo DIR ?>folders/<?php echo $previous ?>'" class="button button-rounded button-flat-caution" style="margin-top: 10px"><i class="fa fa-check-circle" style="margin-left: -4px"></i> No</button>
	
	</p>

</center>