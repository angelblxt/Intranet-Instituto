<?php namespace controllers\admin;
use core\view,
	helpers\nocsrf as NoCSRF,
	helpers\url as Url,
	helpers\session as Session,
	helpers\security as Seguridad,
	helpers\csv as CSV;

class Logs extends \core\controller{

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

	public function logs()
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			// PAGINADOR DE LOGS //

				$this->_log->add('Ha entrado en la sección de "Actividad de Usuarios".');

				$pages = new \helpers\paginator('30', 'p');

				$logs = $this->_log->get($pages->get_limit());

				$pages->set_total($this->_log->number());

				$logsData = [];

				if(count($logs) > 0){

					foreach($logs as $log){

						$logsData[] = [
							'name'      => $this->_user->getNameSurname($this->_user->getUser($log->hash_usuario)),
							'ip'        => long2ip($log->ip),
							'contenido' => Seguridad::desencriptar($log->log, 2),
							'fecha'     => date('d/m/Y H:i:s', $log->tiempo)];

					}

				}


			// FIN DEL PAGINADOR DE LOGS //

			$data = [
				'title' => 'Actividad'];

			$section = [
				'isAdmin'    => $this->templateData['isAdmin'],
				'logs'       => $logsData,
				'page_links' => $pages->page_links()];
			
			Session::set('template', 'user');

			View::rendertemplate('header', $data);
			View::rendertemplate('topHeader', $this->templateData);
			View::rendertemplate('aside', $this->templateData);
			View::render('admin/logs/list', $section);
			View::rendertemplate('footer');

		}

	}

	public function download()
	{

		if(!$this->_user->isLogged() || $this->templateData['isAdmin'] === false){

			Url::redirect('');

		} else {

			CSV::addTitles(['I.D', 'Alumno', 'Acción', 'Dirección I.P', 'Fecha']);

			$logs = $this->_log->get();

			if(count($logs) > 0){

				foreach($logs as $log){

					$name      = $this->_user->getNameSurname($this->_user->getUser($log->hash_usuario));
					$name      = $name['nombre'] .' '. $name['apellidos'];
					
					$ip        = long2ip($log->ip);
					$contenido = Seguridad::desencriptar($log->log, 2);
					$fecha     = date('d/m/Y H:i:s', $log->tiempo);

					CSV::addRow([$log->id, $name, $contenido, $ip, $fecha]);

				}

			}

			CSV::exportar('logs-'. date('dmYHis'));

			$this->_log->add('Ha descargado un archivo CSV de la Actividad de los Usuarios.');

		}

	}

	public function delete($confirm = 0)
	{

		if(!$this->_user->isLogged() || $this->templateData['isAdmin'] === false){

			Url::redirect('');

		} else {

			if($confirm == 0){

				$data = ['title' => 'Vaciar Registro'];

				Session::set('template', 'user');

				View::rendertemplate('header', $data);
				View::rendertemplate('topHeader', $this->templateData);
				View::rendertemplate('aside', $this->templateData);
				View::render('admin/logs/delete');
				View::rendertemplate('footer');

			} else {

				if($this->_log->delete()){

					$this->_log->add('Ha vaciado la tabla de Actividad de Usuarios.');

					$_SESSION['error'] = ['El Registro de Actividad ha sido vaciado.', 'bien'];

				} else {

					$_SESSION['error'] = ['¡Oops! Hubo un error al intentar hacer eso.', 'mal'];

				}

				Url::redirect('admin/logs');

			}

		}

	}

}