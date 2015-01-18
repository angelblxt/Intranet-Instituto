<?php
if(file_exists('vendor/autoload.php')){
	require 'vendor/autoload.php';
} else {
	echo "<h1>Please install via composer.json</h1>";
	echo "<p>Install Composer instructions: <a href='https://getcomposer.org/doc/00-intro.md#globally'>https://getcomposer.org/doc/00-intro.md#globally</a></p>";
	echo "<p>Once composer is installed navigate to the working directory in your terminal/command promt and enter 'composer install'</p>";
	exit;
}

if (!is_readable('app/core/config.php')) {
	die('No config.php found, configure and rename config.example.php to config.php in app/core.');
}

/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 *
 */
	define('ENVIRONMENT', 'development');
/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but production will hide them.
 */

if (defined('ENVIRONMENT')){

	switch (ENVIRONMENT){
		case 'development':
			error_reporting(E_ALL);
		break;

		case 'production':
			error_reporting(0);
		break;

		default:
			exit('The application environment is not set correctly.');
	}

}

//initiate config
new \core\config();

//create alias for Router
use \core\router,
    \helpers\url,
    \helpers\security as Seguridad;

// Rutas de los Formularios.
    Router::post('post/login', '\controllers\user@login');
    Router::post('post/changePassword', '\controllers\user@changePassword');
    Router::any('post/changeCircleColor/(:any)', '\controllers\user@changeCircleColor');

// Rutas primarias del Usuario.
	Router::any('', '\controllers\login@index');
	Router::any('user', '\controllers\user@me');
	Router::any('user/logout', '\controllers\user@logout');
	
// Ruta de la sección de "Acerca De".
	Router::any('about', '\controllers\about@about');

// Ruta de las secciones de "Preferencias".
	Router::any('preferences', '\controllers\preferences@preferences');
	Router::any('preferences/password', '\controllers\preferences@password');
	Router::any('preferences/circleColor', '\controllers\preferences@circleColor');

// Rutas de las secciones de "Mensajes Privados".
	Router::any('messages', '\controllers\messages@index');
	Router::any('messages/in', '\controllers\messages@in');
	Router::any('messages/out', '\controllers\messages@out');
	Router::any('messages/(:any)', '\controllers\messages@message');
	Router::any('messages/(:any)/delete/(:any)', '\controllers\messages@delete');

//if no route found
Router::error('\core\error@index');

//turn on old style routing
Router::$fallback = false;

//execute matched routes
Router::dispatch();

// Evitamos ataques SQLi.

	foreach( $_POST as $key => $value ){

		$_POST[$key] = Seguridad::cleanInput($value);

	}

	foreach( $_GET as $key => $value ){

		$_GET[$key] = Seguridad::cleanInput($value);

	}