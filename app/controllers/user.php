<?php namespace controllers;
use core\view,
	helpers\nocsrf as NoCSRF,
	helpers\url as Url,
	helpers\session as Session,
	helpers\security as Seguridad;

class User extends \core\controller{

	public function __construct(){

		parent::__construct();

		$this->language->load('login');

	}

	public function login(){

		// Cargamos Modelos.
			$this->_user = new \models\user();

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

				Url::redirect('user/');

			}

		} else {

			Url::redirect('');

		}

	}

}
