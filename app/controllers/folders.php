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

		// Iniciamos el Sistema de Archivos.
			FS::personalFS();

	}

/*
|-----------------------------------------------
| Listado de Carpetas y Archivos
|-----------------------------------------------
*/

	public function index($folder = '')
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			$this->_log->add('Ha entrado en la sección "Carpetas".');

			// SISTEMA DE ARCHIVOS

				if(!empty($folder)){

					$folderToGo = Seguridad::desencriptar(base64_decode($folder), 2);
					$previous   = base64_encode(Seguridad::encriptar(FS::getAnteriorPath($folderToGo), 2));

					$list = (FS::comprobeFolder($folderToGo))? FS::listFolders($folderToGo) : FS::listFolders();

				} else {

					$list = FS::listFolders();

				}

				$actual = base64_encode(Seguridad::encriptar($folderToGo, 2));

				$files = [];

				foreach($list as $file){

					$extension = FS::getExtension($file['name']);
					$size      = FS::formatBytes($file['size'], 2);
					$next      = base64_encode(Seguridad::encriptar($file['path'], 2));
					$isShared  = ($this->_fs->isShared($file['path']))? '<i class="fa fa-share-alt" title="Carpeta Compartida" style="margin-left: 10px"></i>' : '';

					if($file['type'] == 'dir'){

						$files[] = [
							'name'     => $file['name'] . $isShared,
							'icon'     => '<i class="fa fa-folder"></i>',
							'size'     => '',
							'type'     => $file['type'],
							'next'     => $next,
							'previous' => $previous];

					} else {

						$files[] = [
							'name' => $file['name'] . $isShared,
							'icon' => '<div class="file-icon" data-type="'. $extension .'"></div>',
							'size' => $size,
							'type' => $file['type'],
							'next' => $next];

					}

				}

				$nombreCarpetaActual = str_replace('_', ' ', FS::getFolderName($folderToGo));

			// FIN DEL SISTEMA DE ARCHIVOS

			$data = ['title' => 'Carpetas'];

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

/*
|-----------------------------------------------
| Formulario para Nueva Carpeta.
|-----------------------------------------------
*/

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
			View::render('user/folders/folder/new', $section);
			View::rendertemplate('footer');

		}

	}

/*
|-----------------------------------------------
| Formulario para Renombrar Archivos o Carpetas.
|-----------------------------------------------
*/

	public function rename($path = '')
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			$path = [
				'encriptado'    => $path,
				'desencriptado' => Seguridad::desencriptar(base64_decode($path), 2)];

			$nombreEspaciado = str_replace('_', ' ', $path['desencriptado']);

			if(FS::comprobeFolder($path['desencriptado'])){

				$nombreActual = FS::getFolderName($nombreEspaciado);

				$data = ['title' => 'Renombrar Carpeta'];

				$section = [
					'folder' => [
						'encrypted'  => $path['encriptado'],
						'decrypted'  => (empty($nombreEspaciado))? '/' : $nombreEspaciado,
						'actualName' => $nombreActual]];

				$templateView = 'user/folders/folder/rename';

			} else {

				$extension    = FS::getExtension($nombreEspaciado);
				$nombreActual = FS::getFileName($nombreEspaciado);
				$nombreActual = str_replace('.' . $extension, '', $nombreActual);

				$carpetaContenida = base64_encode(Seguridad::encriptar(FS::getFolderOfFile($nombreEspaciado), 2));

				$data = ['title' => 'Renombrar Archivo'];

				$section = [
					'file'         => [
						'encrypted'  => $path['encriptado'],
						'decrypted'  => (empty($nombreEspaciado))? '/' : $nombreEspaciado,
						'actualName' => $nombreActual],
					'folderOfFile' => $carpetaContenida];

				$templateView = 'user/folders/file/rename';

			}

			$section['token'] = NoCSRF::generate('token');

			Session::set('template', 'user');

			View::rendertemplate('header', $data);
			View::rendertemplate('topHeader', $this->templateData);
			View::rendertemplate('aside', $this->templateData);
			View::render($templateView, $section);
			View::rendertemplate('footer');

		}

	}

/*
|-----------------------------------------------
| Formulario para borrar Archivos o Carpetas.
|-----------------------------------------------
*/

	public function delete($path = '', $action = 0)
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			$path = [
				'encriptado'    => $path,
				'desencriptado' => Seguridad::desencriptar(base64_decode($path), 2)];

			$nombreEspaciado = str_replace('_', ' ', $path['desencriptado']);

			$templateView = '';

			if(FS::comprobeFolder($path['desencriptado'])){

				$anteriorPath = FS::getAnteriorPath($nombreEspaciado);

				if($action == 0){

					$nombreActual = FS::getFolderName($nombreEspaciado);

					$data = ['title' => 'Eliminar Carpeta'];

					$section = [
						'folder'   => [
							'encrypted'  => $path['encriptado'],
							'decrypted'  => (empty($nombreEspaciado))? '/' : $nombreEspaciado,
							'actualName' => $nombreActual],
						'previous' => $anteriorPath];

					$templateView = 'user/folders/folder/delete';

				} else {

					if(FS::deleteFolder($path['desencriptado'])){

						$this->_log->add('Ha eliminado una Carpeta "'. $path['desencriptado'] .'".');

						$_SESSION['error'] = ['Carpeta eliminada con éxito.', 'bien'];

					} else {

						$_SESSION['error'] = ['¡Oops! Hubo un error al intentar hacer eso.', 'mal'];

					}

					Url::redirect('folders/'. $anteriorPath);

				}

			} else {

				$nombreActual = FS::getFileName($path['desencriptado']);
				$anteriorPath = FS::getFolderOfFile($path['desencriptado']);

				if($action == 0){

					$anteriorPathEncriptado = base64_encode(Seguridad::encriptar($anteriorPath, 2));

					$data = ['title' => 'Eliminar Archivo'];

					$section = [
						'file'     => [
							'encrypted'  => $path['encriptado'],
							'actualName' => $nombreActual],
						'previous' => $anteriorPathEncriptado];

					$templateView = 'user/folders/file/delete';

				} else {

					$anteriorPathEncriptado = base64_encode(Seguridad::encriptar($anteriorPath, 2));

					if(FS::deleteFile($path['desencriptado'])){

						$this->_log->add('Ha eliminado un Archivo "'. $nombreActual .'".');

						$_SESSION['error'] = ['Archivo eliminado con éxito.', 'bien'];

					} else {

						$_SESSION['error'] = ['¡Oops! Hubo un error al intentar hacer eso.', 'mal'];

					}

					Url::redirect('folders/'. $anteriorPathEncriptado);

				}

			}

			if(!empty($templateView)){

				Session::set('template', 'user');

				View::rendertemplate('header', $data);
				View::rendertemplate('topHeader', $this->templateData);
				View::rendertemplate('aside', $this->templateData);
				View::render($templateView, $section);
				View::rendertemplate('footer');

			}

		}

	}

/*
|-----------------------------------------------
| Sección para Descargar Archivos o Carpetas.
|-----------------------------------------------
*/

	public function download($file = '')
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			$fileDecrypted = Seguridad::desencriptar(base64_decode($file), 2);

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

/*
|-----------------------------------------------
| Formulario para subir Archivos.
|-----------------------------------------------
*/

	public function upload($folder = '')
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			$fileDecrypted = Seguridad::desencriptar(base64_decode($folder), 2);

			if(isset($_POST) && $_SERVER['REQUEST_METHOD'] == 'POST'){

				$total  = count($_FILES['files']['error']);
				$errors = FS::upload($fileDecrypted, $_FILES);

				if($errors === 0){

					$_SESSION['error'] = ['Archivos subidos correctamente.', 'bien'];

				} else {

					$_SESSION['error'] = [$errors . ' de '. $total .' archivos no han podido ser subidos.', 'precaucion'];

				}

				Url::redirect('folders/'. $folder);

			} else {

				Url::redirect('');

			}

		}

	}

/*
|-----------------------------------------------
| Sección de Opciones de un Archivo o Carpeta.
|-----------------------------------------------
*/

	public function share($path = '')
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			$path = [
				'encriptado'    => $path,
				'desencriptado' => Seguridad::desencriptar(base64_decode($path), 2)];

			$nombreEspaciado = str_replace('_', ' ', $path['desencriptado']);

			if(FS::comprobeFolder($path['desencriptado'])){

				$nombreActual = FS::getFolderName($nombreEspaciado);

				$data = ['title' => 'Compartir Carpeta'];

				$section = [
					'folder' => [
						'encrypted'  => $path['encriptado'],
						'decrypted'  => (empty($nombreEspaciado))? '/' : $nombreEspaciado,
						'actualName' => $nombreActual]];

				$templateView = 'user/folders/folder/share';

			} else {

				$extension    = FS::getExtension($nombreEspaciado);
				$nombreActual = FS::getFileName($nombreEspaciado);
				$nombreActual = str_replace('.' . $extension, '', $nombreActual);

				$carpetaContenida = base64_encode(Seguridad::encriptar(FS::getFolderOfFile($path['desencriptado']), 2));

				$data = ['title' => 'Compartir Archivo'];

				$section = [
					'file'         => [
						'encrypted'  => $path['encriptado'],
						'decrypted'  => (empty($nombreEspaciado))? '/' : $nombreEspaciado,
						'actualName' => $nombreActual],
					'folderOfFile' => $carpetaContenida];

				$templateView = 'user/folders/file/share';

			}

			if($this->_fs->isShared($path['desencriptado'])){

				$users = $this->_fs->getUsersShared($path['desencriptado']);
				$names = [];

				foreach($users as $user){

					$nombreApellidos = $this->_user->getNameSurname($user);

					$names[] = [
						'hash'        => $this->_user->getHash($user),
						'name'        => $nombreApellidos,
						'circleColor' => $this->_user->getCircleColor($user),
						'inicial'     => utf8_encode($nombreApellidos['nombre'][0])];

				}

				$section['sharedWith'] = $names;

			}

			$section['token'] = NoCSRF::generate('token');

			Session::set('template', 'user');

			View::rendertemplate('header', $data);
			View::rendertemplate('topHeader', $this->templateData);
			View::rendertemplate('aside', $this->templateData);
			View::render($templateView, $section);
			View::rendertemplate('footer');

		}

	}

/*
|-----------------------------------------------
| Dejar de Compartir con una Persona.
|-----------------------------------------------
*/

	public function unshare($path, $hash){

		if(!$this->_user->isLogged() || !$this->_user->exists($hash)){

			Url::redirect('');

		} else {

			$pathDesencriptado = Seguridad::desencriptar(base64_decode($path), 2);

			/* Obtención del nombre del Usuario */

				$user = $this->_user->getUser($hash);
				$name = $this->_user->getNameSurname($user);
				$name = $name['nombre'] .' '. $name['apellidos'];

			/* Fin de la Obtención del nombre del Usuario */

			if($this->_fs->unshare($pathDesencriptado, $hash)){

				$_SESSION['error'] = ['Has dejade de compartir el Elemento con <b>'. $name .'</b>', 'bien'];

			} else {

				$_SESSION['error'] = ['¡Oops! Hubo un error al intentar hacer eso.', 'mal'];

			}

			Url::redirect('folders/'. $path .'/share');

		}

	}

/*
|-----------------------------------------------
| Procesamiento de una Nueva Carpeta.
|-----------------------------------------------
*/

	public function postNewFolder()
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

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

/*
|-----------------------------------------------
| Procesamiento de Renombrar Carpeta.
|-----------------------------------------------
*/

	public function postRenameFolder()
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			$name   = $_POST['nombre'];
			$folder = $_POST['folder'];

			$folderDecrypted = Seguridad::desencriptar(base64_decode($folder), 2);

			$nuevoNombre  = str_replace(' ', '_', $name);
			$nombreActual = FS::getFolderName($folderDecrypted);

			$pathAnterior = FS::getAnteriorPath($folderDecrypted);

			if(isset($_POST['rename']) && NoCSRF::check( 'token', $_POST, false, 60*10, false ) === true){

				if(empty($nuevoNombre)){

					$_SESSION['error'] = ['No puedes dejar ningún campo vacío.', 'precaucion'];

					Url::redirect('folders/'. $folder .'/rename/folder');

				} elseif(!FS::rename($pathAnterior, $nombreActual, $nuevoNombre)) {

					$_SESSION['error'] = ['No ha sido posible renombrar la Carpeta.', 'mal'];

					Url::redirect('folders/'. $folder .'/rename/folder');

				} else {

					$this->_log->add('Ha renombrado una Carpeta "'. $nombreActual .'" > "'. $name .'".');

					$_SESSION['error'] = ['Carpeta renombrada con éxito.', 'bien'];

					Url::redirect('folders/'. $folder);

				}

			} else {

				Url::redirect('');

			}
		}

	}

/*
|-----------------------------------------------
| Procesamiento de Renombrar Archivo.
|-----------------------------------------------
*/

	public function postRenameFile()
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			$file   = $_POST['file'];
			$nombre = $_POST['nombre'];

			$fileDecrypted = Seguridad::desencriptar(base64_decode($file), 2);

			$extension    = FS::getExtension($fileDecrypted);
			$nuevoNombre  = str_replace(' ', '_', $nombre .'.'. $extension);
			$nombreActual = FS::getFileName($fileDecrypted);

			$pathAnterior = [
				'desencriptado' => FS::getFolderOfFile($fileDecrypted),
				'encriptado'    => base64_encode(Seguridad::encriptar(FS::getFolderOfFile($fileDecrypted)))];

			if(isset($_POST['rename']) && NoCSRF::check( 'token', $_POST, false, 60*10, false ) === true){

				if(empty($nuevoNombre)){

					$_SESSION['error'] = ['No puedes dejar ningún campo vacío.', 'precaucion'];

					Url::redirect('folders/'. $file .'/rename/file');

				} elseif(!FS::rename($pathAnterior['desencriptado'], $nombreActual, $nuevoNombre)){

					$_SESSION['error'] = ['No ha sido posible renombrar el Archivo.', 'mal'];

					Url::redirect('folders/'. $file .'/rename/file');

				} else {

					$this->_log->add('Ha renombrado un Archivo "'. $nombreActual .'" > "'. $name .'".');

					$_SESSION['error'] = ['Archivo renombrado con éxito.', 'bien'];

					Url::redirect('folders/'. $pathAnterior['encriptado']);

				}

			} else {

				Url::redirect('');

			}

		}

	}

/*
|-----------------------------------------------
| Procesamiento de Compartir Archivo.
|-----------------------------------------------
*/

	public function postShare()
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			if(isset($_POST['share']) && NoCSRF::check( 'token', $_POST, false, 60*10, false ) === true){

				$personas = $_POST['personas'];
				$path     = (isset($_POST['file']))? $_POST['file'] : $_POST['folder'];

				$pathToMethod = Seguridad::desencriptar(base64_decode($path), 2);

				if(empty($personas)){

					$_SESSION['error'] = ['No puedes dejar ningún campo vacío.', 'precaucion'];

				} else {

					$miHash = $this->_user->getHash($this->username);

					$hashes = [];

					$personas = explode(',', $personas);

					foreach($personas as $persona){

						if(!empty($persona)){

							$persona = trim($persona);

							$hash = $this->_user->getUserByName($persona)->hash_usuario;

							if(!is_null($hash) && $hash != $miHash)
								$hashes[] = $hash;

						}

					}

					if(count($hashes) == 0){

						$_SESSION['error'] = ['El elemento no se ha compartido con nadie.', 'precaucion'];

					} elseif($this->_fs->share($pathToMethod, $hashes)) {

						$_SESSION['error'] = ['El Elemento ha sido compartido con éxito.', 'bien'];

					} else {

						$_SESSION['error'] = ['¡Oops! Hubo un error al intentar hacer eso.', 'mal'];

					}

				}

				Url::redirect('folders/'. $path .'/share');

			} else {

				Url::redirect('');

			}

		}

	}

}
