<?php namespace controllers;
use core\view,
	helpers\nocsrf as NoCSRF,
	helpers\url as Url,
	helpers\session as Session,
	helpers\security as Seguridad;

class User extends \core\controller{

	public $username;

	public function __construct(){

		parent::__construct();

		$this->language->load('login');

		// Cargamos Modelos.
			$this->_user = new \models\user();

		if($this->_user->isLogged())
			$this->username = Session::get('username');

	}

	public function login(){

		if(isset($_POST['login']) && NoCSRF::check( 'token', $_POST, false, 60*10, false ) === true){

			$user     = $_POST['user'];
			$password = $_POST['password'];

			if(empty($user) || empty($password)){

				$_SESSION['error'] = ['No puedes dejar ningún campo vacío.', 'precaucion'];

				Url::redirect('');

			} elseif($this->_user->checkPassword($user, $password) === false) {

				$_SESSION['error'] = ['La Contraseña no es correcta.', 'mal'];

				Url::redirect('');

			} else {

				$encriptado = [
					'user' => Seguridad::encriptar($user, 1)];

				$hashUser = $this->_user->getHash($user);

				Session::set('user', [$encriptado['user'], $hashUser]);
				Session::set('username', $user);

				Url::redirect('user/');

			}

		} else {

			Url::redirect('');

		}

	}

	public function me()
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			$nombreApellidos = $this->_user->getNameSurname();

			$data = [
				'title' => 'Inicio'];

			$personalData = [
				'nombre'  => $nombreApellidos,
				'inicial' => utf8_encode($nombreApellidos['nombre'][0])];
			
			Session::set('template', 'user');

			View::rendertemplate('header', $data);
			View::rendertemplate('topHeader', $personalData);
			View::rendertemplate('aside', $personalData);
			View::render('user/me', $data);
			View::rendertemplate('footer');

		}

	}

	public function logout()
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			Session::destroy('user');
			Session::destroy('username');

			Url::redirect('');

		}

	}

}
