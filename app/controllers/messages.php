<?php namespace controllers;
use core\view,
	helpers\nocsrf as NoCSRF,
	helpers\url as Url,
	helpers\session as Session,
	helpers\security as Seguridad,
	helpers\system as System;

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
			$mensajesSinLeer = $this->_message->number_unreaded();

			$this->templateData = [
				'nombre'        => $nombreApellidos,
				'inicial'       => utf8_encode($nombreApellidos['nombre'][0]),
				'colorCirculo'  => $this->_user->getCircleColor(),
				'shake_message' => ($mensajesSinLeer > 0)? true : false];

	}

	public function index()
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			$this->_log->add('Ha entrado en la sección "Mensajes Privados".');

			$mensajesSinLeer = $this->_message->number_unreaded();

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

	public function in()
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			$this->_log->add('Ha entrado en la sección "Bandeja de Entrada".');

			$data = ['title' => 'Bandeja de Entrada'];

			// PAGINADOR //

				$pages = new \helpers\paginator('10', 'p');

				$mensajes = $this->_message->get('in', $pages->get_limit());

				$pages->set_total($this->_message->number('in'));

				$mensajesArray = [];

				if(count($mensajes) > 0){

					foreach($mensajes as $mensaje){

						$cssClassLeido = ($mensaje->leido_receptor == 0)? 'no_leido' : '';

						$user = $this->_user->getUser($mensaje->hash_emisor);
						
						$nombreEmisor = $this->_user->getNameSurname($user);
						$circleColor  = $this->_user->getCircleColor($user);

						$desencriptado = [
							'asunto'    => Seguridad::desencriptar($mensaje->asunto, 1),
							'contenido' => Seguridad::desencriptar($mensaje->contenido, 2)];

						$mensajesArray[] = [
							'hash'          => $mensaje->hash,
							'emisor'        => [
								'hash'        => $mensaje->hash_emisor,
								'nombre'      => $nombreEmisor,
								'inicial'     => utf8_encode($nombreEmisor['nombre'][0]),
								'circleColor' => $circleColor],
							'asunto'        => $desencriptado['asunto'],
							'contenido'     => System::cortarTexto($desencriptado['contenido'], 10),
							'tiempo'        => System::timeAgo($mensaje->tiempo_enviado),
							'cssClassLeido' => $cssClassLeido];

					}

				}

			// FIN DEL PAGINADOR //

			$section = [
				'mensajes'   => $mensajesArray,
				'page_links' => $pages->page_links()];
			
			Session::set('template', 'user');

			View::rendertemplate('header', $data);
			View::rendertemplate('topHeader', $this->templateData);
			View::rendertemplate('aside', $this->templateData);
			View::render('user/messages/in', $section);
			View::rendertemplate('footer');

		}

	}

	public function out()
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			$this->_log->add('Ha entrado en la sección "Bandeja de Salida".');

			// PAGINADOR //

				$pages = new \helpers\paginator('10', 'p');

				$mensajes = $this->_message->get('out', $pages->get_limit());

				$pages->set_total($this->_message->number('out'));

				$mensajesArray = [];

				if(count($mensajes) > 0){

					foreach($mensajes as $mensaje){

						$user = $this->_user->getUser($mensaje->hash_receptor);
						
						$nombreReceptor = $this->_user->getNameSurname($user);
						$circleColor    = $this->_user->getCircleColor($user);

						$desencriptado = [
							'asunto'    => Seguridad::desencriptar($mensaje->asunto, 1),
							'contenido' => Seguridad::desencriptar($mensaje->contenido, 2)];

						$mensajesArray[] = [
							'hash'          => $mensaje->hash,
							'receptor'      => [
								'hash'        => $mensaje->hash_receptor,
								'nombre'      => $nombreReceptor,
								'inicial'     => utf8_encode($nombreReceptor['nombre'][0]),
								'circleColor' => $circleColor],
							'asunto'        => $desencriptado['asunto'],
							'contenido'     => System::cortarTexto($desencriptado['contenido'], 10),
							'tiempo'        => System::timeAgo($mensaje->tiempo_enviado)];

					}

				}

			// FIN DEL PAGINADOR //

			$data = ['title' => 'Bandeja de Salida'];

			$section = [
				'mensajes'   => $mensajesArray,
				'page_links' => $pages->page_links()];
			
			Session::set('template', 'user');

			View::rendertemplate('header', $data);
			View::rendertemplate('topHeader', $this->templateData);
			View::rendertemplate('aside', $this->templateData);
			View::render('user/messages/out', $section);
			View::rendertemplate('footer');

		}

	}

	public function message($hash)
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			$this->_log->add('Ha entrado al contenido de un Mensaje Privado.');

			$hashUsuario = $this->_user->getHash($this->username);
			$mensaje     = $this->_message->message($hash)[0];

			if(count($mensaje) == 0 || ($hashUsuario != $mensaje->hash_emisor && $hashUsuario != $mensaje->hash_receptor)){

				Url::redirect('');

			} else {

				$soyEmisor = ($hashUsuario != $mensaje->hash_receptor)? true : false;

				if(!$soyEmisor && $mensaje->leido_receptor == 0)
					$this->_message->setReaded($hash);

				$emisor   = $this->_user->getUser($mensaje->hash_emisor);
				$receptor = $this->_user->getUser($mensaje->hash_receptor);
						
				$nombreEmisor   = $this->_user->getNameSurname($emisor);
				$nombreReceptor = $this->_user->getNameSurname($receptor);
				
				$circleColorEmisor   = $this->_user->getCircleColor($emisor);
				$circleColorReceptor = $this->_user->getCircleColor($receptor);

				$desencriptado = [
					'asunto'    => Seguridad::desencriptar($mensaje->asunto, 1),
					'contenido' => Seguridad::desencriptar($mensaje->contenido, 2)];

				$dataMensaje = [
					'hash'          => $mensaje->hash,
					'persona'       => [
						'hash'        => ($soyEmisor)? $mensaje->hash_receptor : $mensaje->hash_emisor,
						'nombre'      => ($soyEmisor)? $nombreEmisor : $nombreReceptor,
						'inicial'     => ($soyEmisor)? utf8_encode($nombreReceptor['nombre'][0]) : utf8_encode($nombreEmisor['nombre'][0]),
						'circleColor' => ($soyEmisor)? $circleColorReceptor : $circleColorEmisor],
					'asunto'        => $desencriptado['asunto'],
					'contenido'     => $desencriptado['contenido'],
					'tiempo'        => System::timeAgo($mensaje->tiempo_enviado)];

				$section = [
					'mensaje' => $dataMensaje];

				$data = ['title' => 'Mensaje'];

				Session::set('template', 'user');

				View::rendertemplate('header', $data);
				View::rendertemplate('topHeader', $this->templateData);
				View::rendertemplate('aside', $this->templateData);
				View::render('user/messages/view', $section);
				View::rendertemplate('footer');

			}

		}

	}

	public function delete($hash, $action)
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			$hashUsuario = $this->_user->getHash($this->username);
			$mensaje     = $this->_message->message($hash)[0];

			if(count($mensaje) == 0 || ($hashUsuario != $mensaje->hash_emisor && $hashUsuario != $mensaje->hash_receptor)){

				Url::redirect('');

			} else {

				if($action == 0){

					$data = ['title' => 'Eliminar Mensaje'];

					$section = ['hash' => $hash];

					Session::set('template', 'user');

					View::rendertemplate('header', $data);
					View::rendertemplate('topHeader', $this->templateData);
					View::rendertemplate('aside', $this->templateData);
					View::render('user/messages/delete', $section);
					View::rendertemplate('footer');

				} else {

					$soyEmisor = ($hashUsuario != $mensaje->hash_receptor)? true : false;

					$for = ($soyEmisor)? 'e' : 'r';

					$result = $this->_message->delete($hash, $for);

					if($result){

						$this->_log->add('Ha eliminado un Mensaje Privado.');

						$_SESSION['error'] = ['Mensaje Privado eliminado con éxito.', 'bien'];

					} else {

						$_SESSION['error'] = ['¡Oops! Hubo un error al intentar hacer eso.', 'mal'];

					}

					Url::redirect('messages');

				}

			}

		}

	}

	public function newMessage()
	{

		if(!$this->_user->isLogged()){

			Url::redirect('');

		} else {

			$data = ['title' => 'Nuevo Menasje'];

			$section = ['token' => NoCSRF::generate('token')];

			Session::set('template', 'user');

			View::rendertemplate('header', $data);
			View::rendertemplate('topHeader', $this->templateData);
			View::rendertemplate('aside', $this->templateData);
			View::render('user/messages/new', $section);
			View::rendertemplate('footer');

		}

	}

	public function send()
	{

		if(isset($_POST['send']) && NoCSRF::check( 'token', $_POST, false, 60*10, false ) === true){

			$nombreCompleto = $_POST['nombreCompleto'];
			$asunto         = $_POST['asunto'];
			$contenido      = $_POST['contenido'];

			$receptor = $this->_user->getUserByName($nombreCompleto)->hash_usuario;

			if(empty($nombreCompleto) || empty($asunto) || empty($contenido)){

				$_SESSION['error'] = ['No puedes dejar ningún campo vacío.', 'precaucion'];

			} else {

				if($receptor == NULL){

					$_SESSION['error'] = ['El Destinatario especificado no existe.', 'mal'];

				} else {

					$result = $this->_message->send($receptor, $asunto, $contenido);

					if($result){

						$this->_log->add('Ha enviado un Mensaje Privado a '. $nombreCompleto);

						$_SESSION['error'] = ['Mensaje enviado a <b>'. $nombreCompleto .'</b> con éxito.', 'bien'];

					} else {

						$_SESSION['error'] = ['¡Oops! Hubo un error al intentar hacer eso.', 'mal'];

					}

				}

			}

			Url::redirect('messages/new');

		} else {

			Url::redirect('');

		}

	}

}
