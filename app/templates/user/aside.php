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

		<div class="circle" style="background-color: #<?php echo $colorCirculo ?>"><?php echo $inicial ?>

			<div class="circle_edit">

				<span class="hint--rounded hint--bounce hint--right" data-hint="Editar Color"><a href="<?php echo DIR; ?>preferences/circleColor"><i class="fa fa-pencil"></i></a></span>

			</div>

		</div>

		<div class="logout"><a href="<?php echo DIR; ?>user/logout"><i class="fa fa-sign-out"></i> Salir</a></div>

		<div class="mediumBar"></div>

	</div>
	<div class="menu">

		<div class="boton <?php echo $userSelected ?>">
							
			<span class="hint--rounded hint--bounce hint--right" data-hint="Inicio"><a href="<?php echo DIR; ?>user"><i class="fa fa-home"></i></a></span>
						
		</div>
		<div class="boton <?php echo $foldersSelected ?>">
							
			<span class="hint--rounded hint--bounce hint--right" data-hint="Carpetas"><a href="<?php echo DIR; ?>folders"><i class="fa fa-folder"></i></a></span>

		</div>
		<div class="boton <?php echo $messagesSelected ?>">
							
			<span class="hint--rounded hint--bounce hint--right" data-hint="Mensajes Privados"><a href="<?php echo DIR; ?>messages">

				<?php if($shake_message){ ?> <div class="shake shake-slow shake-constant"> <?php } ?>
					
					<i class="fa fa-envelope"></i>

				<?php if($shake_message){ ?> </div> <?php } ?>

			</a></span>

		</div>
		<div class="boton <?php echo $preferencesSelected ?>">
							
			<span class="hint--rounded hint--bounce hint--right" data-hint="Preferencias"><a href="<?php echo DIR; ?>preferences"><i class="fa fa-cogs"></i></a></span>

		</div>
		<div class="boton <?php echo $aboutSelected ?>">
							
			<span class="hint--rounded hint--bounce hint--right" data-hint="Acerca De"><a href="<?php echo DIR; ?>about"><i class="fa fa-info-circle"></i></a></span>

		</div>

	</div>

</aside>

<div class="contenido">
					
	<div class="box">