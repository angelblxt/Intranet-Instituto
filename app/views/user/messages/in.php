<?php

	if(count($mensajes) == 0){

		?>

			<center>

				<i class="fa fa-times-circle-o messages_no"></i><br/>

				<div class="messages_no_text">No tienes Mensajes Recibidos</div>

				<p>

					<button onClick="location.href='<?php echo DIR ?>messages'" class="button button-rounded button-flat-primary" style="margin-top: 10px"><i class="fa fa-chevron-left" style="margin-left: -4px"></i> Volver atrás</button>

				</p>

			</center>

		<?php

	} else {

		foreach($mensajes as $mensaje){

			?>

				<a href="<?php echo DIR; ?>messages/<?php echo $mensaje['hash'] ?>">

					<div class="private_message <?php echo $mensaje['cssClassLeido'] ?>">

						<div class="emisor">

							<div class="circle" style="background: #<?php echo $mensaje['emisor']['circleColor'] ?>"><?php echo $mensaje['emisor']['inicial'] ?></div>

						</div>

						<div class="message_content">

							<div class="message_title"><?php echo $mensaje['asunto'] ?></div>

							<div class="message_text"><?php echo $mensaje['contenido'] ?></div>

							<div class="message_options">

								<div class="message_sender">Enviado por: <b><?php echo $mensaje['emisor']['nombre']['nombre'] .' '. $mensaje['emisor']['nombre']['apellidos'] ?></b></div>
								<div class="message_time"><?php echo $mensaje['tiempo'] ?></div>

								<div style="clear: both"></div>

							</div>

						</div>

					</div>

				</a>

			<?php

		}

		?> 

			<center>

				<?php echo $page_links ?>

				<button onClick="location.href='<?php echo DIR ?>messages'" class="button button-rounded button-flat-primary" style="margin-top: 10px"><i class="fa fa-chevron-left" style="margin-left: -4px"></i> Volver atrás</button>

			</center>

		<?php

	}

?>