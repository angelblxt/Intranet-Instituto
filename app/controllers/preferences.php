<?php namespace controllers;
use core\view,
	helpers\nocsrf as NoCSRF,
	helpers\url as Url,
	helpers\session as Session,
	helpers\security as Seguridad;

class Preferences extends \core\controller{

	public $username;

	public function __construct(){

		parent::__construct();

		$this->language->load('login');

		// Cargamos Modelos.
			$this->_user = new \models\user();
			$this->_log  = new \models\log();

		if($this->_user->isLogged())
			$this->username = Session::get('username');

	}

	public function preferences()
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			$nombreApellidos = $this->_user->getNameSurname();

			$data = [
				'title' => 'Preferencias'];

			$personalData = [
				'nombre'  => $nombreApellidos,
				'inicial' => utf8_encode($nombreApellidos['nombre'][0])];
			
			Session::set('template', 'user');

			View::rendertemplate('header', $data);
			View::rendertemplate('topHeader', $personalData);
			View::rendertemplate('aside', $personalData);
			View::render('user/preferences/preferences');
			View::rendertemplate('footer');

		}

	}

	public function password()
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			$nombreApellidos = $this->_user->getNameSurname();

			$data = [
				'title' => 'Contraseña'];

			$personalData = [
				'nombre'  => $nombreApellidos,
				'inicial' => utf8_encode($nombreApellidos['nombre'][0])];

			$sectionData = [
				'token' => NoCSRF::generate('token')];
			
			Session::set('template', 'user');

			View::rendertemplate('header', $data);
			View::rendertemplate('topHeader', $personalData);
			View::rendertemplate('aside', $personalData);
			View::render('user/preferences/password', $sectionData);
			View::rendertemplate('footer');

		}

	}

}
