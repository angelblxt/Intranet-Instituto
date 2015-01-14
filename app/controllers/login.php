<?php namespace controllers;
use core\view,
	helpers\nocsrf as NoCSRF,
	helpers\url as Url,
	helpers\session as Session;

class Login extends \core\controller{

	public function __construct(){

		parent::__construct();

		$this->language->load('login');

		// Cargamos Modelos por Defecto.
			$this->_user = new \models\user();

		if($this->_user->isLogged())
			Url::redirect('user/');

	}

	public function index() {

		$data = [
			'title' => 'Login',
			'token' => NoCSRF::generate('token')];
		
		View::rendertemplate('header', $data);
		View::render('login/index', $data);
		View::rendertemplate('footer', $data);

	}

}
