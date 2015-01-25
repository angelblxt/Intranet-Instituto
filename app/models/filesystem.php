<?php namespace models;

use \helpers\security as Seguridad,
	\helpers\session as Session,
	\helpers\system as System;
	 
class Filesystem extends \core\model {
	
	function __construct(){
		
		parent::__construct();

		// Cargamos modelos.
			$this->_user = new \models\user();

		if($this->_user->isLogged())
			$this->username = Session::get('username');

	}

	/**
	*
	* Método encargado de compartir un archivo o carpeta con usuarios.
	*
	* @param string $path PATH de lo compartido.
	* @param array $hashes HASHes de las personas con las que se comparte.
	*
	* @return boolean TRUE si se ha compartido, FALSE si no. 
	*
	*/

		public function share($path, $hashes)
		{

			$hash_usuario = $this->_user->getHash($this->username);

			$path = Seguridad::encriptar($path, 1);

			$insert = [
				'hash'                      => md5(microtime()),
				'hash_usuario'              => $hash_usuario,
				'hash_usuarios_compartidos' => implode(';', $hashes),
				'direccion'                 => $path];

			$result = $this->_db->insert('comparticiones', $insert);

			return ($result)? true : false;

		}

	/**
	*
	* Método encargado de comprobar si un archivo o carpeta está compartido.
	*
	* @param string $path PATH de lo compartido.
	*
	* @return boolean TRUE si está compartido, FALSE si no.
	*
	*/

		public function isShared($path = '')
		{

			$path = Seguridad::encriptar($path, 1);

			$result = $this->_db->num('SELECT COUNT(*) FROM comparticiones WHERE direccion = :direccion', [':direccion' => $path]);

			return ($result > 0)? true : false;

		}

}

?>