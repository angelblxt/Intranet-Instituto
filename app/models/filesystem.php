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

			$hashUsuario = $this->_user->getHash($this->username);

			if(self::isShared($path)){

				$usersShared = self::getUsersShared($path);
				$usersPost   = [];

				foreach($hashes as $user)
					$usersPost[] = $this->_user->getUser($user);

				$hashes = array_merge($usersShared, $usersPost);
				$hashes = array_unique($hashes);

				$hashesFinales = [];

				foreach($hashes as $hash)
					$hashesFinales[] = $this->_user->getHash($hash);

				$update = ['hash_usuarios_compartidos' => implode(';', $hashesFinales)];

				$result = $this->_db->update('comparticiones', $update, ['direccion' => Seguridad::encriptar($path, 1)]);

				return $result;

			} else {

				$hashes = implode(';', $hashes);

				$iterator = FS::iterator($path);

				$pathsInsert = [$path];

				foreach($iterator as $pathToInsert){

					$pathsInsert[] = $pathToInsert;

				}

				foreach($pathsInsert as $insert){

					$data = [
						'hash'                      => md5(microtime()),
						'hash_usuario'              => $hashUsuario,
						'hash_usuarios_compartidos' => $hashes,
						'direccion'                 => Seguridad::encriptar($insert, 1)];

					$this->_db->insert('comparticiones', $data);

				}

				return true;

			}

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
	* Método encargado de comprobar si un path está compartido contigo.
	*
	* @param string $hashCompartidor HASH del Usuario Compartidor.
	* @param string $hashUsuario HASH del Usuario.
	* @param string $path PATH a Comprobar.
	*
	* @return boolean TRUE si está compartido, FALSE si no.
	*
	*/

		public function isSharedWithMe($hashCompartidor, $hashUsuario, $path)
		{

			$path = Seguridad::encriptar($path, 1);

			$result = $this->_db->num("SELECT COUNT(*) FROM comparticiones WHERE hash_usuario = :hashCompartidor AND direccion = :direccion AND hash_usuarios_compartidos LIKE '%". $hashUsuario ."%'", ['hashCompartidor' => $hashCompartidor, 'direccion' => $path]);

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

	/**
	*
	* Método encargado de obtener los PATH de los elementos compartidos 
	* conmigo.
	*
	* @param string $hash HASH del Usuario.
	*
	* @return array PATHs ['hash del compartidor', 'path'].
	*
	*/

		public function getPathsSharedWithMe($hash)
		{

			$result = $this->_db->select("SELECT hash_usuario, direccion FROM comparticiones WHERE hash_usuarios_compartidos LIKE '%". $hash ."%'");

			$return = [];

			if(count($result) > 0){

				foreach($result as $par){

					$path = Seguridad::desencriptar($par->direccion, 1);

					$return[] = [
						'hash' => $par->hash_usuario,
						'path' => $path];

				}

			}

			return $return;

		}

}

?>