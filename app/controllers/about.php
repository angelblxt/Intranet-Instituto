<?php namespace controllers;
use core\view,
	helpers\nocsrf as NoCSRF,
	helpers\url as Url,
	helpers\session as Session,
	helpers\security as Seguridad;

class About extends \core\controller{

	public $username;

	public function __construct(){

		parent::__construct();

		$this->language->load('login');

		// Cargamos Modelos.
			$this->_user = new \models\user();

		if($this->_user->isLogged())
			$this->username = Session::get('username');

	}

	public function about()
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			$nombreApellidos = $this->_user->getNameSurname();

			$data = [
				'title' => 'Acerca De'];

			$personalData = [
				'nombre'  => $nombreApellidos,
				'inicial' => utf8_encode($nombreApellidos['nombre'][0])];
			
			Session::set('template', 'user');

			View::rendertemplate('header', $data);
			View::rendertemplate('topHeader', $personalData);
			View::rendertemplate('aside', $personalData);
			View::render('user/about');
			View::rendertemplate('footer');

		}

	}

}
