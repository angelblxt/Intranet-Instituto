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

		}

	}

}