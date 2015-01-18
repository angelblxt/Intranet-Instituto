<?php namespace models;

use \helpers\security as Seguridad,
	\helpers\session as Session,
	\helpers\system as System;
	 
class Message extends \core\model {
	
	function __construct(){
		
		parent::__construct();

		// Cargamos modelos.
			$this->_user = new \models\user();

		if($this->_user->isLogged())
			$this->username = Session::get('username');

	}

	/**
	*
	* Método encargado de obtener el número de mensajes privados.
	*
	* @param string $type ['in', 'out'] Tipo de Mensajes Privados.
	*
	* @return int Número de Mensajes Privados.
	*
	*/

		public function number($type)
		{

			$hashUsuario = $this->_user->getHash($this->username);

			switch($type){

				case 'in' : $where = 'hash_receptor'; break;
				case 'out': $where = 'hash_emisor'; break;
				default   : $where = 'hash_receptor'; break;

			}

			$number = $this->_db->num("SELECT COUNT(id) FROM mensajes_privados WHERE ". $where ." = :hashUsuario", [':hashUsuario' => $hashUsuario]);

			return (int)$number;

		}

	/**
	*
	* Método encargado de obtener el Número de mensajes privados 
	* no leídos.
	*
	* @return int Número de Mensajes Privados sin leer.
	*
	*/

		public function number_unreaded()
		{

			$hashUsuario = $this->_user->getHash($this->username);

			$number = $this->_db->num("SELECT COUNT(id) FROM mensajes_privados WHERE hash_receptor = :hashReceptor AND leido_receptor = '0'", [':hashReceptor' => $hashUsuario]);

			return (int)$number;

		}

	/**
	*
	* Método encargado de obtener todos los Mensajes Privados.
	*
	* @param string $type ['in', 'out'] Tipo de Mensajes Privados.
	* @param string $limit Límite de registros.
	*
	* @return array Datos.
	*
	*/

		public function get($type, $limit = '')
		{

			$hashUsuario = $this->_user->getHash($this->username);

			switch($type){

				case 'in' : $where = 'hash_receptor'; break;
				case 'out': $where = 'hash_emisor'; break;
				default   : $where = 'hash_receptor'; break;

			}

			$data = $this->_db->select("SELECT * FROM mensajes_privados WHERE ". $where ." = :hashReceptor AND borrado_receptor = '0' ORDER BY id DESC ". $limit, [':hashReceptor' => $hashUsuario]);

			return $data;

		}

	/**
	*
	* Método encargado de obtener información de un Mensaje Privado.
	*
	* @param string $hash HASH del Mensaje Privado.
	*
	* @return array Información del Mensaje Privado.
	*
	*/

		public function message($hash)
		{

			$data = $this->_db->select("SELECT hash, hash_emisor, hash_receptor, asunto, contenido, tiempo_enviado FROM mensajes_privados WHERE hash = :hash", [':hash' => $hash]);

			return $data;

		}

	/**
	*
	* Método encargado de marcar un mensaje como leído.
	*
	* @param string $hash HASH del Mensaje Privado.
	*
	* @return boolean TRUE si se ha marcado como leído, FALSE si no.
	*
	*/

		public function setReaded($hash)
		{

			$result = $this->_db->update('mensajes_privados', ['leido_receptor' => '1'], ['hash' => $hash]);

			return ($result)? true : false;

		}

}

?>