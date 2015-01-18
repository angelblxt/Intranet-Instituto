<?php namespace controllers;
use core\view,
	helpers\nocsrf as NoCSRF,
	helpers\url as Url,
	helpers\session as Session,
	helpers\security as Seguridad;

class Messages extends \core\controller{

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

			$this->templateData = [
				'nombre'       => $nombreApellidos,
				'inicial'      => utf8_encode($nombreApellidos['nombre'][0]),
				'colorCirculo' => $this->_user->getCircleColor()];

	}

	public function index()
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			$this->_log->add('Ha entrado en la sección "Mensajes Privados".');

			$mensajesSinLeer = $this->_message->number_unreaded();

			/* // PAGINADOR //

				$pages = new \helpers\paginator('10', 'p');

				$logs = $this->_message->get('in', $pages->get_limit());

				$pages->set_total($this->_message->number('in'));

				$logsArray = [];

				if(count($logs) > 0){

					foreach($logs as $log){

						$logsArray[] = $log;

					}

				}

			// FIN DEL PAGINADOR //

			$data = [
				'title'      => 'Mensajes',
				'logs'       => $logsArray,
				'page_links' => $pages->page_links()]; */

			$this->templateData['shake_message'] = ($mensajesSinLeer > 0)? true : false;

			$data = ['title' => 'Mensajes'];

			$section = [
				'sinLeer' => ($mensajesSinLeer > 0)? '(<b>'. $mensajesSinLeer .'</b>)' : ''];
			
			Session::set('template', 'user');

			View::rendertemplate('header', $data);
			View::rendertemplate('topHeader', $this->templateData);
			View::rendertemplate('aside', $this->templateData);
			View::render('user/messages/messages', $section);
			View::rendertemplate('footer');

		}

	}

}
