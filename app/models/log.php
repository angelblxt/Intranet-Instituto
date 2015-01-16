<?php namespace models;

use \helpers\security as Seguridad,
	\helpers\session as Session,
	\helpers\system as System;
	 
class Log extends \core\model {
	
	function __construct(){
		
		parent::__construct();

	}

	/**
	*
	* Método encargado de almacenar un LOG.
	*
	* @param string $hashUsuario HASH del Usuario.
	* @param string $log LOG a almacenar.
	*
	* @return boolean TRUE si se ha almacenado, FALSE si no.
	*
	*/

		public function add($hashUsuario, $log)
		{

			if(!empty($hashUsuario) || !empty($log)){

				$ip     = ip2long(System::getIP());
				$tiempo = time();
				$log    = Seguridad::encriptar($log, 2);

				$insert = [
					'hash_usuario' => $hashUsuario,
					'ip'           => $ip,
					'log'          => $log,
					'tiempo'       => $tiempo];

				$result = $this->_db->insert('logs', $insert);

				return ($result)? true : false;

			}

		}

}

?>