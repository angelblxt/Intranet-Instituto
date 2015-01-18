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

			$number = $this->_db->num("SELECT id FROM mensajes_privados WHERE ". $where ." = :hashUsuario", [':hashUsuario' => $hashUsuario]);

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

			$number = $this->_db->num("SELECT id FROM mensajes_privados WHERE hash_receptor = :hashReceptor AND leido_receptor = '0'", [':hashReceptor' => $hashUsuario]);

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

			$data = $this->_db->select("SELECT * FROM mensajes_privados WHERE ". $where ." = :hashReceptor AND borrado_receptor = '0'". $limit, [':hashReceptor' => $hashUsuario]);

			return $data;

		}

}

?>