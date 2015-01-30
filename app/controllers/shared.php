<?php namespace controllers;
use core\view,
	helpers\nocsrf as NoCSRF,
	helpers\url as Url,
	helpers\session as Session,
	helpers\security as Seguridad,
	helpers\filesystem as FS;

class Shared extends \core\controller{

	public $username;
	public $templateData;

	public function __construct(){

		parent::__construct();

		$this->language->load('login');

		// Cargamos Modelos.
			$this->_user    = new \models\user();
			$this->_log     = new \models\log();
			$this->_message = new \models\message();
			$this->_fs      = new \models\filesystem();

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

/*
|-----------------------------------------------
| Listado de Carpetas y Archivos
|-----------------------------------------------
*/

	public function folders($folder = '', $userCompartidor = '', $father = '')
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			$this->_log->add('Ha entrado en la sección "Carpetas Privadas".');

			$myHash = $this->_user->getHash($this->username);

			$foldersShared = $this->_fs->getPathsSharedWithMe($myHash);

			$toShow = [];

			if(empty($folder) || empty($userCompartidor)){

				foreach($foldersShared as $par){

					$user = $this->_user->getUser($par['hash']);

					FS::personalFS($user);

					$folders = FS::listFolders(FS::getAnteriorPath($par['path']));

					foreach($folders as $dir){

						if($this->_fs->isSharedWithMe($par['hash'], $myHash, $dir['path'])){
							
							$toShow[] = [
								'user'   => $user,
								'data'   => $dir,
								'father' => $dir['path']];
						
						}

					}

				}

			} else {

				$desencriptado = [
					'folder'          => Seguridad::desencriptar(base64_decode($folder), 2),
					'userCompartidor' => Seguridad::desencriptar(base64_decode($userCompartidor), 2),
					'father'          => Seguridad::desencriptar(base64_decode($father), 2)];

				$previous = ($desencriptado['folder'] == $desencriptado['father'])? '' : base64_encode(Seguridad::encriptar(FS::getAnteriorPath($desencriptado['folder']), 2));
				$previous = (empty($previous))? $previous : $previous .'/'. $userCompartidor .'/'. $father;

				FS::personalFS($desencriptado['userCompartidor']);

				$folders = FS::listFolders($desencriptado['folder']);

				foreach($folders as $dir){
							
					$toShow[] = [
						'user'   => $desencriptado['userCompartidor'],
						'data'   => $dir,
						'father' => $desencriptado['father']];

				}

			}

				$files = [];

				foreach($toShow as $file){

					$compartidor       = $file['user'];
					$nombreCompartidor = $this->_user->getNameSurname($compartidor);

					$extension   = FS::getExtension($file['data']['name']);
					$size        = FS::formatBytes($file['data']['size'], 2);
					$next        = base64_encode(Seguridad::encriptar($file['data']['path'], 2));
					$compartidor = base64_encode(Seguridad::encriptar($file['user'], 2));
					$father      = base64_encode(Seguridad::encriptar($file['father'], 2));
					$isShared    = ($this->_fs->isShared($file['data']['path']))? '<i class="fa fa-share-alt" title="Compartido" style="margin-left: 10px"></i> <i>'. $nombreCompartidor['nombre'] .' '. $nombreCompartidor['apellidos'] .'</i>' : '';

					if($file['data']['type'] == 'dir'){

						$files[] = [
							'name'     => $file['data']['name'] . $isShared,
							'icon'     => '<i class="fa fa-folder"></i>',
							'size'     => '',
							'type'     => $file['data']['type'],
							'next'     => $next .'/'. $compartidor .'/'. $father,
							'previous' => $previous];

					} else {

						$files[] = [
							'name' => $file['data']['name'] . $isShared,
							'icon' => '<div class="file-icon" data-type="'. $extension .'"></div>',
							'size' => $size,
							'type' => $file['data']['type'],
							'next' => $next .'/'. $compartidor .'/'. $father];

					}

				}

				$nombreCarpetaActual = str_replace('_', ' ', FS::getFolderName($folder));

			// FIN DEL SISTEMA DE ARCHIVOS

			$data = ['title' => 'Carpetas Compartidas'];

			$section = [
				'files'        => $files,
				'previous'     => $previous,
				'actual'       => $actual,
				'titleSection' => (empty($nombreCarpetaActual))? 'Carpetas Compartidas Conmigo' : $nombreCarpetaActual];
			
			Session::set('template', 'user');

			View::rendertemplate('header', $data);
			View::rendertemplate('topHeader', $this->templateData);
			View::rendertemplate('aside', $this->templateData);
			View::render('user/folders/shared', $section);
			View::rendertemplate('footer');

		}

	}

/*
|-----------------------------------------------
| Sección para Descargar Archivos o Carpetas.
|-----------------------------------------------
*/

	public function download($file = '', $userCompartidor = '', $father = '')
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			$userCompartidor = Seguridad::desencriptar(base64_decode($userCompartidor), 2);
			$fileDecrypted   = Seguridad::desencriptar(base64_decode($file), 2);

			FS::personalFS($userCompartidor);

			if(FS::comprobeFolder($fileDecrypted)){

				$name = date('d-m-Y-h-i-s');

				$carpetaAnterior = FS::getAnteriorPath($fileDecrypted);

				if(FS::comprimeFolder($fileDecrypted, $name) === true){

					if(!FS::download($name . '.zip'))
						Url::redirect('folders/'. $carpetaAnterior);

				}

			} else {

				FS::download($fileDecrypted, false, false);

			}

		}

	}

}
