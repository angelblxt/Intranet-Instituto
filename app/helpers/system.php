<?php namespace helpers;

use helpers\session as Session,
	helpers\security as Seguridad;

/**
*
* Clase de manejo de algunas funciones de la Intranet.
* 
* @author Ángel Querol García
*
*/

class System {

	public function __construct()
	{

		// Cargamos Modelos.
			$this->_log = new \models\logs();

	}

	/**
	*
	* Método que obtiene la dirección IP real de un usuario.
	*
	* @return Dirección IP.
	*
	*/

		public function getIP()
		{

			if( !empty($_SERVER['HTTP_CLIENT_IP']) )

				$ip = $_SERVER['HTTP_CLIENT_IP'];

			elseif( !empty($_SERVER['HTTP_X_FORWARDED_FOR']) )

				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

			else

				$ip = $_SERVER['REMOTE_ADDR'];

			return $ip;

		}

}