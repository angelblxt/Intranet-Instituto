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
							'hash'              => $user->hash,
							'user'              => $username,
							'tiempo_registrado' => date('d/m/Y H:i', $user->tiempo_registrado),
							'name'              => $this->_user->getNameSurname($username),
							'curso'             => System::getCurso($this->_user->getCurso($username))];

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

}