<?php namespace controllers;
use core\view,
	helpers\nocsrf as NoCSRF,
	helpers\url as Url,
	helpers\session as Session,
	helpers\security as Seguridad;

class Preferences extends \core\controller{

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

	public function preferences()
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			$this->_log->add('Ha entrado en la sección "Preferencias".');

			$data = [
				'title' => 'Preferencias'];
			
			Session::set('template', 'user');

			View::rendertemplate('header', $data);
			View::rendertemplate('topHeader', $this->templateData);
			View::rendertemplate('aside', $this->templateData);
			View::render('user/preferences/preferences');
			View::rendertemplate('footer');

		}

	}

	public function password()
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			$this->_log->add('Ha entrado en la sección "Cambio de Contraseña".');

			$data = [
				'title' => 'Contraseña'];

			$sectionData = [
				'token' => NoCSRF::generate('token')];
			
			Session::set('template', 'user');

			View::rendertemplate('header', $data);
			View::rendertemplate('topHeader', $this->templateData);
			View::rendertemplate('aside', $this->templateData);
			View::render('user/preferences/password', $sectionData);
			View::rendertemplate('footer');

		}

	}

}
