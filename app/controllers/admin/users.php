<?php namespace controllers\admin;
use core\view,
	helpers\nocsrf as NoCSRF,
	helpers\url as Url,
	helpers\session as Session,
	helpers\security as Seguridad,
	helpers\csv as CSV,
	helpers\system as System;

class Users extends \core\controller{

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

		$this->_user->setLastConnection();

		// Datos del Template.
			$nombreApellidos = $this->_user->getNameSurname();
			$isTeacher       = $this->_user->isTeacher();
			$isAdmin         = $this->_user->isAdmin();

			$this->templateData = [
				'nombre'        => $nombreApellidos,
				'inicial'       => utf8_encode($nombreApellidos['nombre'][0]),
				'colorCirculo'  => $this->_user->getCircleColor(),
				'shake_message' => ($this->_message->number_unreaded() > 0)? true : false,
				'isTeacher'     => $isTeacher,
				'isAdmin'       => $isAdmin];

			if($isTeacher === false && $isAdmin === false)
				Url::redirect('');

		// Envitamos ataques.
			foreach( $_POST as $key => $value ){

				$_POST[$key] = Seguridad::cleanInput($value);

			}

			foreach( $_GET as $key => $value ){

				$_GET[$key] = Seguridad::cleanInput($value);

			}

	}

	public function users()
	{

		if(!$this->_user->isLogged() || $this->templateData['isAdmin'] === false){

			Url::redirect('');

		} else {

			$this->_log->add('Ha entrado en la sección de "Listado de Usuarios".');

			// PAGINADOR DE USUARIOS //

				$pages = new \helpers\paginator('20', 'p');

				$users = $this->_user->getUsers($pages->get_limit());

				$pages->set_total($this->_user->getNumber());

				$usersData = [];

				if(count($users) > 0){

					foreach($users as $user){

						$username = Seguridad::desencriptar($user->usuario, 1);

						$usersData[] = [
							'hash'           => $user->hash,
							'user'           => $username,
							'ultimaConexion' => System::timeAgo($user->tiempo_ultima_conexion),
							'name'           => $this->_user->getNameSurname($username),
							'curso'          => System::getCurso($this->_user->getCurso($username)),
							'rango'          => System::getRango($this->_user->getRank($username))];

					}

				}

			// FIN DEL PAGINADOR DE USUARIOS //

			$data = ['title' => 'Usuarios'];

			$section = [
				'isAdmin'    => $this->templateData['isAdmin'],
				'users'      => $usersData,
				'page_links' => $pages->page_links()];
			
			Session::set('template', 'user');

			View::rendertemplate('header', $data);
			View::rendertemplate('topHeader', $this->templateData);
			View::rendertemplate('aside', $this->templateData);
			View::render('admin/users/list', $section);
			View::rendertemplate('footer');

		}

	}

	public function download()
	{

		if(!$this->_user->isLogged() || $this->templateData['isAdmin'] === false){

			Url::redirect('');

		} else {

			CSV::addTitles(['Usuario', 'Nombre', 'Curso', 'Fecha de Registro']);

			$users = $this->_user->getUsers();

			if(count($users) > 0){

				foreach($users as $user){

					$username          = Seguridad::desencriptar($user->usuario, 1);
					$name              = $this->_user->getNameSurname($username);
					$name              = $name['nombre'] .' '. $name['apellidos'];
					
					$curso             = $this->_user->getCurso($username);
					$tiempo_registrado = date('d/m/Y H:i', $user->tiempo_registrado);

					CSV::addRow([$username, $name, $curso, $tiempo_registrado]);

				}

			}

			CSV::exportar('users-'. date('dmYHis'));

			$this->_log->add('Ha descargado un archivo CSV de Usuarios.');

		}

	}

	public function add()
	{

		if(!$this->_user->isLogged() || $this->templateData['isAdmin'] === false){

			Url::redirect('');

		} else {

			$data = ['title' => 'Nuevo Usuario'];

			$section = [
				'token'  => NoCSRF::generate('token'),
				'cursos' => System::cursos(),
				'rangos' => ['Alumno', 'Profesor', 'Administrador']];

			Session::set('template', 'user');

			View::rendertemplate('header', $data);
			View::rendertemplate('topHeader', $this->templateData);
			View::rendertemplate('aside', $this->templateData);
			View::render('admin/users/add', $section);
			View::rendertemplate('footer');

		}

	}

	public function import()
	{

		if(!$this->_user->isLogged() || $this->templateData['isAdmin'] === false){

			Url::redirect('');

		} else {

			$data = ['title' => 'Importar Usuarios'];

			Session::set('template', 'user');

			View::rendertemplate('header', $data);
			View::rendertemplate('topHeader', $this->templateData);
			View::rendertemplate('aside', $this->templateData);
			View::render('admin/users/import');
			View::rendertemplate('footer');

		}

	}

	public function delete($hash, $confirm = 0)
	{

		if(!$this->_user->isLogged() || $this->templateData['isAdmin'] === false){

			Url::redirect('');

		} else {

			$name = $this->_user->getNameSurname($this->_user->getUser($hash));

			if($confirm == 0){

				$data = ['title' => 'Eliminar Usuario'];

				$section = [
					'hash' => $hash,
					'name' => $name];

				Session::set('template', 'user');

				View::rendertemplate('header', $data);
				View::rendertemplate('topHeader', $this->templateData);
				View::rendertemplate('aside', $this->templateData);
				View::render('admin/users/delete', $section);
				View::rendertemplate('footer');

			} else {

				if($this->_user->delete($hash)){

					$this->_log->add('Ha eliminado del Sistema a '. $name .'.');

					$_SESSION['error'] = ['El Usuario ha sido eliminado con éxito.', 'bien'];

				} else {

					$_SESSION['error'] = ['¡Oops! Hubo un error al intentar hacer eso.', 'mal'];

				}

				Url::redirect('admin/users');

			}

		}

	}

	public function edit($hash)
	{

		if(!$this->_user->isLogged() || $this->templateData['isAdmin'] === false){

			Url::redirect('');

		} else {

			$user = $this->_user->getUser($hash);

			$data = ['title' => 'Editar Usuario'];

			$section = [
				'hash'   => $hash,
				'name'   => $this->_user->getNameSurname($user),
				'user'   => $user,
				'cursos' => System::cursos(),
				'curso'  => $this->_user->getCurso($user),
				'rangos' => ['Alumno', 'Profesor', 'Administrador'],
				'rango'  => $this->_user->getRank($user),
				'token'  => NoCSRF::generate('token')];

			Session::set('template', 'user');

			View::rendertemplate('header', $data);
			View::rendertemplate('topHeader', $this->templateData);
			View::rendertemplate('aside', $this->templateData);
			View::render('admin/users/edit', $section);
			View::rendertemplate('footer');

		}

	}

	public function postEditUser()
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			if(isset($_POST['edit']) && NoCSRF::check( 'token', $_POST, false, 60*10, false ) === true){

				$hash      = $_POST['hash'];
				$nombre    = $_POST['nombre'];
				$apellidos = $_POST['apellidos'];
				$nuevoUser = $_POST['user'];
				$password  = $_POST['password'];
				$curso     = $_POST['curso'];
				$rango     = $_POST['rango'];

				if(empty($nombre) || empty($apellidos) || empty($nuevoUser) || empty($curso) || empty($rango)){

					$_SESSION['error'] = ['Hay campos que no puedes dejar vacíos.', 'precaucion'];

				} elseif(!$this->_user->exists($hash)) {

					$_SESSION['error'] = ['¡Oops! Hubo un error al intentar hacer eso.', 'mal'];

				} else {

					$user = $this->_user->getUser($hash);

					$password = (empty($password))? $this->_user->getPassword($user) : hash('sha512', $password);

					if($this->_user->edit($hash, $nuevoUser, $nombre, $apellidos, $password, $curso, $rango)){

						$this->_log->add('Ha editado Información de '. $nombre .' '. $apellidos .'.');

						$_SESSION['error'] = ['El Usuario ha sido editado con éxito.', 'bien'];

					} else {

						$_SESSION['error'] = ['¡Oops! Hubo un error al intentar hacer eso.', 'mal'];

					}

				}

				Url::redirect('admin/users/'. $hash .'/edit');

			} else {

				Url::redirect('');

			}

		}

	}

	public function postAddUser()
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			if(isset($_POST['add']) && NoCSRF::check( 'token', $_POST, false, 60*10, false ) === true){

				$nombre    = $_POST['nombre'];
				$apellidos = $_POST['apellidos'];
				$user      = $_POST['user'];
				$password1 = $_POST['password1'];
				$password2 = $_POST['password2'];
				$curso     = $_POST['curso'];
				$rango     = $_POST['rango'];

				if(empty($nombre) || empty($apellidos) || empty($user) || empty($password1) || empty($password2) || empty($curso) || $rango == NULL){

					$_SESSION['error'] = ['No puedes dejar ningún campo vacío', 'precaucion'];

				} else {

					if($this->_user->checkUser($user)){

						$_SESSION['error'] = ['El Usuario <b>'. $user .'</b> ya existe en el Sistema.', 'mal'];

					} elseif(md5($password1) != md5($password2)) {

						$_SESSION['error'] = ['Las Contraseñas especificadas no coinciden.', 'mal'];

					} elseif(!$this->_user->register($user, $nombre, $apellidos, $password2, $curso, $rango)) {

						$_SESSION['error'] = ['¡Oops! Hubo un error al intentar hacer eso.', 'mal'];

					} else {

						$this->_log->add('Ha registrado un Usuario nuevo en el Sistema "'. $user .'".');

						$_SESSION['error'] = ['<b>'. $nombre .' '. $apellidos .'</b> ha sido registrada en el Sistema.', 'bien'];

					}

				}

				Url::redirect('admin/users/new');

			} else {

				Url::redirect('');

			}

		}

	}

	public function postImportUsers()
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			if(count($_FILES) == 0){

				$_SESSION['error'] = ['El tamaño total de todos los archivos supera los 128MB.', 'mal'];

			} else {

				$validFormats = ['csv'];
				$maxFileSize  = MAX_SIZE * 1024 * 1024;

				$errors = 0;

				if($_FILES['csv']['error'] != 0){

					$_SESSION['error'] = ['Error al subir el Archivo.', 'mal'];

				} else {

					if($_FILES['csv']['size'] > $maxFileSize){

						$_SESSION['error'] = ['El archivo CSV supera el tamaño permitido de <b>'. MAX_SIZE .'MB</b>.', 'mal'];

					} elseif(!in_array(pathinfo($_FILES['csv']['name'], PATHINFO_EXTENSION), $validFormats)) {

						$_SESSION['error'] = ['El formato del archivo no es válido. Solo se permiten archivos <b>CSV</b>.', 'mal'];

					} else {

						if(($handle = fopen($_FILES['csv']['tmp_name'], 'r')) !== false){

							$total = 0;
							$exito = 0;

							while(($data = fgetcsv($handle, 0, ';')) !== false){

								$user = $data[0];

								if(!$this->_user->checkUser($user)){

									$this->_user->register($user, $data[1], $data[2], 'IESBENJAMINJARNES', $data[3], 0);

									$exito++;

								}

								$total++;

							}

							fclose($handle);

							$_SESSION['error'] = [$exito .' de '. $total .' usuarios registrados exitosamente.', 'bien'];

						} else {

							$_SESSION['error'] = ['¡Oops! Hubo un error al intentar hacer eso.', 'mal'];

						}

					}

				}

			}

			Url::redirect('admin/users/import');

		}

	}

}