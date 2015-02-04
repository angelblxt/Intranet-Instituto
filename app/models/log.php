<?php namespace models;

use \helpers\security as Seguridad,
	\helpers\session as Session,
	\helpers\system as System;
	 
class Log extends \core\model {
	
	function __construct(){
		
		parent::__construct();

		// Cargamos modelos.
			$this->_user = new \models\user();

		if($this->_user->isLogged())
			$this->username = Session::get('username');

	}

	/**
	*
	* Método encargado de almacenar un LOG.
	*
	* @param string $log LOG a almacenar.
	* @param string $user Usuario.
	*
	* @return boolean TRUE si se ha almacenado, FALSE si no.
	*
	*/

		public function add($log, $user = '')
		{

			if(!empty($log)){

				$user = (empty($user))? $this->username : $user;

				$hashUsuario = $this->_user->getHash($user);
				$ip          = ip2long(System::getIP());
				$tiempo      = time();
				$log         = Seguridad::encriptar($log, 2);

				$insert = [
					'hash_usuario' => $hashUsuario,
					'ip'           => $ip,
					'log'          => $log,
					'tiempo'       => $tiempo];

				$result = $this->_db->insert('logs', $insert);

				return ($result)? true : false;

			}

		}

	/**
	*
	* Método encargado de obtener los LOGS.
	*
	* @param string $limit LIMIT SQL.
	*
	* @return array LOGS.
	*
	*/

		public function get($limit = '')
		{

			$logs = $this->_db->select("SELECT * FROM logs ORDER BY id DESC ". $limit);

			return $logs;

		}

	/**
	*
	* Método encargado de obtener el número de LOGS.
	*
	* @return int Número de LOGS. 
	*
	*/

		public function number()
		{

			$number = $this->_db->num("SELECT COUNT(*) FROM logs");

			return (int)$number;

		}

	/**
	*
	* Método encargado de eliminar los LOGS.
	*
	* @return boolean TRUE si se han eliminado, FALSE si no.
	*
	*/

		public function delete()
		{

			$this->_db->truncate('logs');

			return true;

		}

}

?>