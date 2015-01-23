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

				$actual = base64_encode(Seguridad::encriptar($folderToGo, 2));

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
							'size'     => '',
							'type'     => $file['type'],
							'next'     => $next,
							'previous' => $previous,
							'buttons'  => [
								'rename' => DIR . 'folders/'. $next .'/rename/folder',
								'delete' => DIR . 'folders/'. $next .'/delete/folder/0']];

					} else {

						$files[] = [
							'name' => [
								'decrypted' => $file['name'],
								'encrypted' => $encriptedName],
							'icon' => $icon,
							'size' => $size,
							'type' => $file['type'],
							'buttons' => [
								'rename' => '',
								'delete' => '']];

					}

				}

				$nombreCarpetaActual = str_replace('_', ' ', FS::getFolderName($folderToGo));

			// FIN DEL SISTEMA DE ARCHIVOS

			$data = [
				'title' => 'Carpetas'];

			$section = [
				'files'        => $files,
				'previous'     => $previous,
				'actual'       => $actual,
				'titleSection' => (empty($nombreCarpetaActual))? 'Carpeta Personal' : $nombreCarpetaActual];
			
			Session::set('template', 'user');

			View::rendertemplate('header', $data);
			View::rendertemplate('topHeader', $this->templateData);
			View::rendertemplate('aside', $this->templateData);
			View::render('user/folders/folders', $section);
			View::rendertemplate('footer');

		}

	}

	public function newFolder($folder = '')
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			$folderDecrypted = str_replace('_', ' ', Seguridad::desencriptar(base64_decode($folder), 2));

			$data = [
				'title' => 'Nueva Carpeta'];

			$section = [
				'folder' => [
					'encrypted' => $folder,
					'decrypted' => (empty($folderDecrypted))? '/' : $folderDecrypted],
				'token' => NoCSRF::generate('token')];
				
			Session::set('template', 'user');

			View::rendertemplate('header', $data);
			View::rendertemplate('topHeader', $this->templateData);
			View::rendertemplate('aside', $this->templateData);
			View::render('user/folders/newFolder', $section);
			View::rendertemplate('footer');

		}

	}

	public function postNewFolder()
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			FS::personalFS();

			$name   = str_replace(' ', '_', $_POST['nombre']);
			$folder = $_POST['folder'];

			$folderDecrypted = Seguridad::desencriptar(base64_decode($folder), 2);

			$pathFinal = $folderDecrypted . $name . '/';

			if(isset($_POST['create']) && NoCSRF::check( 'token', $_POST, false, 60*10, false ) === true){

				if(empty($name)){

					$_SESSION['error'] = ['No puedes dejar ningún campo vacío.', 'precaucion'];

					Url::redirect('folders/'. $folder .'/new/folder');

				} elseif(!FS::makeFolder($pathFinal)) {

					$_SESSION['error'] = ['La carpeta ya existe o ha habido un error al crearla.', 'mal'];

					Url::redirect('folders/'. $folder .'/new/folder');

				} else {

					$this->_log->add('Ha creado una nueva carpeta "'. $pathFinal .'".');

					$_SESSION['error'] = ['Carpeta creada con éxito.', 'bien'];

					Url::redirect('folders/'. $folder);

				}

			} else {

				Url::redirect('');

			}

		}

	}

	public function renameFolder($folder = '')
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			$folderDecrypted = str_replace('_', ' ', Seguridad::desencriptar(base64_decode($folder), 2));

			$nombreActual = FS::getFolderName($folderDecrypted);

			$data = [
				'title' => 'Renombrar Carpeta'];

			$section = [
				'folder' => [
					'encrypted'  => $folder,
					'decrypted'  => (empty($folderDecrypted))? '/' : $folderDecrypted,
					'actualName' => $nombreActual],
				'token' => NoCSRF::generate('token')];
				
			Session::set('template', 'user');

			View::rendertemplate('header', $data);
			View::rendertemplate('topHeader', $this->templateData);
			View::rendertemplate('aside', $this->templateData);
			View::render('user/folders/renameFolder', $section);
			View::rendertemplate('footer');

		}

	}

	public function postRenameFolder()
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			FS::personalFS();

			$name   = str_replace(' ', '_', $_POST['nombre']);
			$folder = $_POST['folder'];

			$folderDecrypted = Seguridad::desencriptar(base64_decode($folder), 2);

			$anteriorPath = FS::getAnteriorPath($folderDecrypted);
			$nombreActual = FS::getFolderName($folderDecrypted);

			if(isset($_POST['create']) && NoCSRF::check( 'token', $_POST, false, 60*10, false ) === true){

				if(empty($name)){

					$_SESSION['error'] = ['No puedes dejar ningún campo vacío.', 'precaucion'];

					Url::redirect('folders/'. $folder .'/rename/folder');

				} elseif(!FS::rename($anteriorPath, $nombreActual, $name)) {

					$this->_log->add('Ha renombrado una Carpeta "'. $nombreActual .'" > "'. $name .'".');

					$_SESSION['error'] = ['No ha sido posible renombrar la Carpeta.', 'mal'];

					Url::redirect('folders/'. $folder .'/rename/folder');

				} else {

					$_SESSION['error'] = ['Carpeta renombrada con éxito.', 'bien'];

					Url::redirect('folders/'. $folder);

				}

			} else {

				Url::redirect('');

			}

		}

	}

	public function deleteFolder($folder = '', $action = 0)
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			$anteriorPath = FS::getAnteriorPath($folderDecrypted);

			if($action == 0){

				$folderDecrypted = str_replace('_', ' ', Seguridad::desencriptar(base64_decode($folder), 2));

				$nombreActual = FS::getFolderName($folderDecrypted);

				$data = [
					'title' => 'Eliminar Carpeta'];

				$section = [
					'folder' => [
						'encrypted'  => $folder,
						'decrypted'  => (empty($folderDecrypted))? '/' : $folderDecrypted,
						'actualName' => $nombreActual],
						'previous'   => $anteriorPath];
					
				Session::set('template', 'user');

				View::rendertemplate('header', $data);
				View::rendertemplate('topHeader', $this->templateData);
				View::rendertemplate('aside', $this->templateData);
				View::render('user/folders/deleteFolder', $section);
				View::rendertemplate('footer');

			} else {

				$folderDecrypted = Seguridad::desencriptar(base64_decode($folder), 2);

				FS::personalFS();

				if(FS::deleteFolder($folderDecrypted)){

					$this->_log->add('Ha eliminado una Carpeta "'. $folderDecrypted .'".');

					$_SESSION['error'] = ['Carpeta eliminada con éxito.', 'bien'];

				} else {

					$_SESSION['error'] = ['¡Oops! Hubo un error al intentar hacer eso.', 'mal'];

				}

				Url::redirect('folders/'. $anteriorPath);

			}

		}

	}

}
