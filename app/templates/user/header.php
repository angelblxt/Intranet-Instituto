<!DOCTYPE html>
	
	<html xmlns="http://www.w3.org/1999/xhtml" lang="es">

		<head>

			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

			<title> <?php echo $title .' - '. SITETITLE ?> </title>

			<?php

				// Archivos CSS.

					\helpers\assets::css(array(
						\helpers\url::template_path() . 'css/font-awesome/css/font-awesome.min.css',
						\helpers\url::template_path() . 'css/ui.css',
						\helpers\url::template_path() . 'css/buttons.css',
						\helpers\url::template_path() . 'css/forms.css',
						\helpers\url::template_path() . 'css/messages.css',
						\helpers\url::template_path() . 'css/switch.css',
						\helpers\url::template_path() . 'css/tables.css',
						\helpers\url::template_path() . 'css/hint.min.css',
						\helpers\url::template_path() . 'css/csshake.min.css',
						\helpers\url::template_path() . 'css/jquery-ui.css',
						\helpers\url::template_path() . 'css/fileicon.css'
					));

				// Archivos JS.

					\helpers\assets::js(array(
						\helpers\url::template_path() . 'js/prefixfree.min.js',
						\helpers\url::template_path() . 'js/jquery.min.js',
						\helpers\url::template_path() . 'js/jquery-ui.js'
					));

			?>

			<link rel="icon" type="image/png" href="<?php echo \helpers\url::template_path(); ?>img/favicon.png">

		</head>

		<body>

			<div class="container">