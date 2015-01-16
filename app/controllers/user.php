<?php namespace controllers;
use core\view,
	helpers\nocsrf as NoCSRF,
	helpers\url as Url,
	helpers\session as Session,
	helpers\security as Seguridad;

class User extends \core\controller{

	public $username;
	public $templateData;

	public function __construct(){

		parent::__construct();

		$this->language->load('login');

		// Cargamos Modelos.
			$this->_user = new \models\user();
			$this->_log  = new \models\log();

		if($this->_user->isLogged())
			$this->username = Session::get('username');

		// Datos del Template.
			$nombreApellidos = $this->_user->getNameSurname();

			$this->templateData = [
				'nombre'       => $nombreApellidos,
				'inicial'      => utf8_encode($nombreApellidos['nombre'][0]),
				'colorCirculo' => $this->_user->getCircleColor()];

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

				$this->_log->add('Ha iniciado sesión.', $user);

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

			$data = [
				'title' => 'Inicio'];
			
			Session::set('template', 'user');

			View::rendertemplate('header', $data);
			View::rendertemplate('topHeader', $this->templateData);
			View::rendertemplate('aside', $this->templateData);
			View::render('user/me');
			View::rendertemplate('footer');

		}

	}

	public function logout()
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			$this->_log->add('Ha cerrado sesión.', $this->username);

			Session::destroy('user');
			Session::destroy('username');

			Url::redirect('');

		}

	}

	public function changePassword()
	{

		$passActual = $_POST['pass_actual'];
		$passNueva1 = $_POST['pass_nueva_1'];
		$passNueva2 = $_POST['pass_nueva_2'];

		if(isset($_POST['change']) && NoCSRF::check( 'token', $_POST, false, 60*10, false ) === true){

			if(empty($passActual) || empty($passNueva1) || empty($passNueva2)){

				$_SESSION['error'] = ['No puedes dejar ningún campo vacío.', 'precaucion'];

			} elseif(!$this->_user->checkPassword($this->username, $passActual)) {

				$_SESSION['error'] = ['La Contraseña actual no es correcta.', 'mal'];

			} elseif($passNueva1 !== $passNueva2){

				$_SESSION['error'] = ['Las nuevas Contraseñas no coinciden.', 'mal'];

			} else {

				$result = $this->_user->setPassword($this->username, $passNueva2);

				if($result){

					$this->_log->add('Ha cambiado su contraseña.');

					$_SESSION['error'] = ['Contraseña cambiada con éxito.', 'bien'];

				} else {

					$_SESSION['error'] = ['¡Oops! Hubo un error al intentar hacer eso.', 'mal'];

				}

			}

			Url::redirect('preferences/password');

		} else {

			Url::redirect('');

		}

	}

}
