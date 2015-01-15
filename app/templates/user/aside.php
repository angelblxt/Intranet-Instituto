<?php

	$actual = \helpers\url::actual();

	if(preg_match('/^user*/i', $actual)){

		$userSelected = 'seleccionado';

	} elseif(preg_match('/^folders*/i', $actual)){

		$foldersSelected = 'seleccionado';

	} elseif(preg_match('/^messages*/i', $actual)){

		$messagesSelected = 'seleccionado';

	} elseif(preg_match('/^preferences*/i', $actual)){

		$preferencesSelected = 'seleccionado';

	} elseif(preg_match('/^about*/i', $actual)){

		$aboutSelected = 'seleccionado';

	}

?>

<aside>
					
	<div class="topZone">

		<div class="circle" style="background-color: #607d8b"><?php echo $inicial ?></div>

		<div class="logout"><a href="user/logout"><i class="fa fa-sign-out"></i> Salir</a></div>

		<div class="mediumBar"></div>

	</div>
	<div class="menu">

		<div class="boton <?php echo $userSelected ?>">
							
			<span class="hint--rounded hint--bounce hint--right" data-hint="Inicio"><a href="user"><i class="fa fa-home"></i></a></span>
						
		</div>
		<div class="boton <?php echo $foldersSelected ?>">
							
			<span class="hint--rounded hint--bounce hint--right" data-hint="Carpetas"><a href="folders"><i class="fa fa-folder"></i></a></span>

		</div>
		<div class="boton <?php echo $messagesSelected ?>">
							
			<span class="hint--rounded hint--bounce hint--right" data-hint="Mensajes Privados"><a href="messages"><i class="fa fa-envelope"></i></a></span>

		</div>
		<div class="boton <?php echo $preferencesSelected ?>">
							
			<span class="hint--rounded hint--bounce hint--right" data-hint="Preferencias"><a href="preferences"><i class="fa fa-cogs"></i></a></span>

		</div>
		<div class="boton <?php echo $aboutSelected ?>">
							
			<span class="hint--rounded hint--bounce hint--right" data-hint="Acerca De"><a href="about"><i class="fa fa-info-circle"></i></a></span>

		</div>

	</div>

</aside>

<div class="contenido">
					
	<div class="box">