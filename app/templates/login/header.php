<!DOCTYPE html>
	
	<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo LANGUAGE_CODE ?>">

		<head>

			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

			<title> <?php echo $title .' - '. SITETITLE ?> </title>

			<?php

				// Archivos CSS.

					\helpers\assets::css(array(
						\helpers\url::template_path() . 'css/font-awesome/css/font-awesome.min.css',
						\helpers\url::template_path() . 'css/login.css',
						\helpers\url::template_path() . 'css/buttons.css',
						\helpers\url::template_path() . 'css/forms.css',
						\helpers\url::template_path() . 'css/messages.css'
					));

				// Archivos JS.

					\helpers\assets::js(array(
						\helpers\url::template_path() . 'js/prefixfree.min.js',
						\helpers\url::template_path() . 'js/jquery.min.css',
					));

			?>

			<link rel="icon" type="image/png" href="<?php echo \helpers\url::template_path(); ?>img/favicon.png">

		</head>

		<body>

			<div class="center">

				<div class="logo"></div>

				<div class="box">