<?php namespace controllers;
use core\view,
	helpers\nocsrf as NoCSRF,
	helpers\url as Url,
	helpers\session as Session,
	helpers\security as Seguridad,
	helpers\filesystem as FS;

class Folders extends \core\controller{

	public $username;
	public $templateData;

	public function __construct(){

		parent::__construct();

		$this->language->load('login');

		// Cargamos Modelos.
			$this->_user    = new \models\user();
			$this->_log     = new \models\log();
			$this->_message = new \models\message();

		if($this->_user->isLogged())
			$this->username = Session::get('username');

		// Datos del Template.
			$nombreApellidos = $this->_user->getNameSurname();

			$this->templateData = [
				'nombre'        => $nombreApellidos,
				'inicial'       => utf8_encode($nombreApellidos['nombre'][0]),
				'colorCirculo'  => $this->_user->getCircleColor(),
				'shake_message' => ($this->_message->number_unreaded() > 0)? true : false];

		// Envitamos ataques.
			foreach( $_POST as $key => $value ){

				$_POST[$key] = Seguridad::cleanInput($value);

			}

			foreach( $_GET as $key => $value ){

				$_GET[$key] = Seguridad::cleanInput($value);

			}

	}

	public function index($folder = '')
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			$this->_log->add('Ha entrado en la sección "Carpetas".');

			// SISTEMA DE ARCHIVOS

				FS::personalFS();

				if(!empty($folder)){

					$folderToGo = Seguridad::desencriptar(base64_decode($folder), 2);

					$list = (FS::comprobeFolder($folderToGo))? FS::listFolders($folderToGo) : FS::listFolders();

					/* Manejo de la Carpeta anterior */

						$previous = base64_encode(Seguridad::encriptar(FS::getAnteriorPath($folderToGo), 2));

					/* FIN del Manejo de la Carpeta anterior */

				} else {

					$list = FS::listFolders();

				}

				$files = [];

				foreach($list as $file){

					$extension = FS::getExtension($file['name']);
					$size      = FS::formatBytes($file['size'], 2);
					$icon      = ($file['type'] == 'dir')? '<i class="fa fa-folder"></i>' : '<div class="file-icon" data-type="'. $extension .'"></div>';
					$next      = ($file['type'] == 'dir')? base64_encode(Seguridad::encriptar($file['path'], 2)) : '';

					if($file['type'] == 'dir'){

						$files[] = [
							'name'     => [
								'decrypted' => $file['name'],
								'encrypted' => $encriptedName],
							'icon'     => $icon,
							'size'     => $size,
							'type'     => $file['type'],
							'next'     => $next,
							'previous' => $previous];

					} else {

						$files[] = [
							'name' => [
								'decrypted' => $file['name'],
								'encrypted' => $encriptedName],
							'icon' => $icon,
							'size' => $size,
							'type' => $file['type']];

					}

				}

			// FIN DEL SISTEMA DE ARCHIVOS

			$data = [
				'title' => 'Carpetas'];

			$section = [
				'files'    => $files,
				'previous' => $previous];
			
			Session::set('template', 'user');

			View::rendertemplate('header', $data);
			View::rendertemplate('topHeader', $this->templateData);
			View::rendertemplate('aside', $this->templateData);
			View::render('user/folders/folders', $section);
			View::rendertemplate('footer');

		}

	}

}
