<div class="sectionTitle">Compartir Carpeta</div>

<?php \helpers\messages::comprobarErrores(); ?>

<div class="folders_showName"><i class="fa fa-folder"></i> <?php echo $folder['decrypted'] ?></div>

<form method="post" action="<?php echo DIR ?>post/share">

	<center>

		<div class="wrapper">
			<div class="inputIcon"><i class="fa fa-user"></i></div>
			<input type="text" id="search" name="personas" placeholder="Personas con las que Compartir ésto."/>
		</div>

		<input type="hidden" name="token" value="<?php echo $token; ?>">
		<input type="hidden" name="folder" value="<?php echo $folder['encrypted'] ?>">

		<button type="submit" name="share" class="button button-rounded button-flat-action" style="margin-top: 10px"><i class="fa fa-check-circle" style="margin-left: -4px"></i> Compartir</button>

	</center>

</form>

<?php

	if(isset($sharedWith)){

		?>

			<center>

				<button onClick="location.href='<?php echo DIR ?>folders/<?php echo $folderOfFile ?>'" class="button button-rounded button-flat-primary" style="margin: 10px 0"><i class="fa fa-chevron-left" style="margin-left: -4px"></i> Volver atrás</button>

			</center>

			<div class="sectionTitle">Compartido con:</div>

		<?php

		foreach($sharedWith as $user){

			?>

				<div class="message_view">
	
					<div class="data">

						<div class="emisor">
							
							<div class="circle" style="background: #<?php echo $user['circleColor'] ?>"><?php echo $user['inicial'] ?></div>

						</div>

						<div class="nombreEmisor">

							<i class="fa fa-chevron-right"></i> <?php echo $user['name']['nombre'] .' '. $user['name']['apellidos'] ?> 
							<span class="hint--rounded hint--bounce hint--right" style="font-family: Helvetica" data-hint="Dejar de Compartir">
								<a href="<?php echo DIR ?>folders/<?php echo $folder['encrypted'] ?>/unshare/<?php echo $user['hash'] ?>"><i class="fa fa-times-circle" style="margin-left: 20px"></i></a>
							</span>

						</div>

					</div>

				</div>

			<?php

		}

	}

?>

<center>

	<button onClick="location.href='<?php echo DIR ?>folders/<?php echo $folder['encrypted'] ?>'" class="button button-rounded button-flat-primary" style="margin-top: 10px"><i class="fa fa-chevron-left" style="margin-left: -4px"></i> Volver atrás</button>

</center>

<script>

	$(function() {

		function split( val ) {
			return val.split( /,\s*/ );
		}

		function extractLast( term ) {
			return split( term ).pop();
		}

		$( "#search" ).autocomplete(
		{

			source: function( request, response ) {

				$.getJSON( '<?php echo DIR; ?>post/searchUser', {
					term: extractLast( request.term )
				}, response );

			},
			search: function() {

				var term = extractLast( this.value );
				if ( term.length < 2 ) {
					return false;
				}

			},
			focus: function() {

				return false;

			},
			select: function( event, ui ) {

				var terms = split( this.value );

				terms.pop();

				terms.push( ui.item.value );

				terms.push( "" );
				this.value = terms.join( ", " );

				return false;

			}

		});

	});

</script>