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
    Router::post('post/login', 										'\controllers\user@login');
    Router::post('post/changePassword', 							'\controllers\user@changePassword');
    Router::any('post/changeCircleColor/(:any)', 					'\controllers\user@changeCircleColor');
    Router::post('post/sendMessage', 								'\controllers\messages@send');
    Router::any('post/searchUser', 									'\controllers\user@search');
    Router::post('post/new/folder', 								'\controllers\folders@postNewFolder');
    Router::post('post/rename/folder', 								'\controllers\folders@postRenameFolder');
    Router::post('post/rename/file', 								'\controllers\folders@postRenameFile');
    Router::post('post/upload/(:any)', 								'\controllers\folders@upload');
    Router::post('post/share', 										'\controllers\folders@postShare');
    Router::post('post/editUser',									'\controllers\admin\users@postEditUser');
    Router::post('post/addUser',									'\controllers\admin\users@postAddUser');
    Router::post('post/importUsers',								'\controllers\admin\users@postImportUsers');

// Rutas primarias del Usuario.
	Router::any('', 												'\controllers\login@index');
	Router::any('user', 											'\controllers\user@me');
	Router::any('user/logout', 										'\controllers\user@logout');
	
// Ruta de la sección de "Acerca De".
	Router::any('about', 											'\controllers\about@about');

// Ruta de la sección de "Huevo de Pascua".
	Router::any('easter',											'\controllers\about@easter');

// Ruta de las secciones de "Preferencias".
	Router::any('preferences', 										'\controllers\preferences@preferences');
	Router::any('preferences/password', 							'\controllers\preferences@password');
	Router::any('preferences/circleColor', 							'\controllers\preferences@circleColor');

// Rutas de las secciones de "Mensajes Privados".
	Router::any('messages', 										'\controllers\messages@index');
	Router::any('messages/in', 										'\controllers\messages@in');
	Router::any('messages/out', 									'\controllers\messages@out');
	Router::any('messages/(:any)', 									'\controllers\messages@message');
	Router::any('messages/(:any)/delete/(:any)', 					'\controllers\messages@delete');
	Router::any('messages/new', 									'\controllers\messages@newMessage');

// Rutas de las secciones de "Archivos".
	Router::any('cloud', 											'\controllers\folders@index');

	Router::any('folders', 											'\controllers\folders@folders');
	Router::any('folders/(:any)', 									'\controllers\folders@folders');
	Router::any('folders/(:any)/new/folder', 						'\controllers\folders@newFolder');
	Router::any('folders/(:any)/rename', 							'\controllers\folders@rename');
	Router::any('folders/(:any)/delete/(:num)', 					'\controllers\folders@delete');
	Router::any('folders/(:any)/download', 							'\controllers\folders@download');
	Router::any('folders/(:any)/share', 							'\controllers\folders@share');
	Router::any('folders/(:any)/unshare/(:any)', 					'\controllers\folders@unshare');

	Router::any('shared', 											'\controllers\folders@folders');
	Router::any('shared/(:any)/(:any)/(:any)', 						'\controllers\folders@folders');
	Router::any('shared/(:any)/(:any)/(:any)/download', 			'\controllers\folders@download');

// Rutas de las secciones de "Admin".
	Router::any('admin', 											'\controllers\admin\admin@index');

	Router::any('admin/logs', 										'\controllers\admin\logs@logs');
	Router::any('admin/logs/download', 								'\controllers\admin\logs@download');
	Router::any('admin/logs/delete/(:num)',							'\controllers\admin\logs@delete');

	Router::any('admin/users',										'\controllers\admin\users@users');
	Router::any('admin/users/download',								'\controllers\admin\users@download');
	Router::any('admin/users/(:any)/edit',							'\controllers\admin\users@edit');
	Router::any('admin/users/(:any)/delete/(:num)',					'\controllers\admin\users@delete');
	Router::any('admin/users/new',									'\controllers\admin\users@add');
	Router::any('admin/users/import',								'\controllers\admin\users@import');

//if no route found
Router::error('\core\error@index');

//turn on old style routing
Router::$fallback = false;

//execute matched routes
Router::dispatch();