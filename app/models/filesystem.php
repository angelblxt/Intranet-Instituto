<?php namespace models;

use \helpers\security as Seguridad,
	\helpers\session as Session,
	\helpers\system as System,
	\helpers\filesystem as FS;
	 
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

			FS::personalFS();

			$hash_usuario = $this->_user->getHash($this->username);

			if(self::isShared($path)){

				$usersShared = self::getUsersShared($path);
				$usersPost = [];

				foreach($hashes as $user)
					$usersPost[] = $this->_user->getUser($user);

				$hashes = array_merge($usersShared, $usersPost);
				$hashes = array_unique($hashes);

				$hashesFinales = [];

				foreach($hashes as $hash)
					$hashesFinales[] = $this->_user->getHash($hash);

				$update = ['hash_usuarios_compartidos' => implode(';', $hashesFinales)];

				$result = $this->_db->update('comparticiones', $update, ['direccion' => Seguridad::encriptar($path, 1)]);

			} else {

				$insert = [
					'hash'                      => md5(microtime()),
					'hash_usuario'              => $hash_usuario,
					'hash_usuarios_compartidos' => implode(';', $hashes),
					'direccion'                 => Seguridad::encriptar($path, 1)];

				$result = $this->_db->insert('comparticiones', $insert);

			}

			return ($result)? true : false;

		}

	/**
	*
	* Método encargado de quitar la compartición de un usuario sobre 
	* un PATH.
	*
	* @param string $path PATH a Des-compartir.
	* @param string $hash HASH del Usuario.
	*
	* @return boolean TRUE si se ha descompartido, FALSE si no.
	*
	*/

		public function unshare($path, $hash)
		{

			if(self::isShared($path)){

				$user = $this->_user->getUser($hash);

				$usersShared = self::getUsersShared($path);

				if(($key = array_search($user, $usersShared)) !== false)
					unset($usersShared[$key]);

				$hashes = [];

				if(count($usersShared) > 0){

					foreach($usersShared as $shared)
						$hashes[] = $this->_user->getHash($shared);

				} else {

					$hashes = '';

				}

				$path   = Seguridad::encriptar($path, 1);
				$update = ['hash_usuarios_compartidos' => implode(';', $hashes)];
				$where  = ['direccion' => $path];

				$result = (empty($hashes))? $this->_db->delete('comparticiones', $where) : $this->_db->update('comparticiones', $update, $where);

				return ($result)? true : false;

			} else {

				return false;

			}

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

	/**
	*
	* Método encargado de obtener los usuarios de los que están compartidos
	* en una carpeta/archivo.
	*
	* @param string $path PATH de lo compartido.
	*
	* @return array Usuarios compartidos.
	*
	*/

		public function getUsersShared($path = '')
		{

			$path = Seguridad::encriptar($path, 1);

			$hashes = $this->_db->select("SELECT hash_usuarios_compartidos FROM comparticiones WHERE direccion = :direccion LIMIT 1", [':direccion' => $path])[0]->hash_usuarios_compartidos;
			$hashes = explode(';', $hashes);

			$users = [];

			foreach($hashes as $hash){

				$users[] = $this->_user->getUser($hash);

			}

			return $users;

		}

}

?>