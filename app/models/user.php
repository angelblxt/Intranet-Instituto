<?php namespace models;

use \helpers\security as Seguridad,
	\helpers\session as Session;
	 
class User extends \core\model {
	
	function __construct(){
		
		parent::__construct();

	}

	/**
	*
	* Método encargado de comprobar un usuario y una contraseña.
	*
	* @param string $user Usuario.
	* @param string $password Contraseña.
	*
	* @return boolean TRUE si es correcto, FALSE si no.
	*
	*/
		
		public function checkPassword($user, $password)
		{

			$user = Seguridad::encriptar($user, 1);

			$truePassword = $this->_db->select("SELECT password FROM usuarios WHERE usuario = '". $user ."' LIMIT 1")[0]->password;

			return (hash('sha512', $password) === $truePassword)? true : false;

		}

	/**
	*
	* Método encargado de obtener el HASH de un usuario.
	*
	* @param string $user Usuario.
	*
	* @return string HASH del Usuario.
	*
	*/

		public function getHash($user)
		{

			$user = Seguridad::encriptar($user, 1);

			$hash = $this->_db->select("SELECT hash FROM usuarios WHERE usuario = '". $user ."' LIMIT 1")[0]->hash;

			return $hash;

		}

	/**
	*
	* Método encargado de comprobar si un usuario está logueado.
	*
	* @return boolean TRUE si es cierto, FALSE si no lo es.
	*
	*/

		public function isLogged()
		{

			if(Session::exists('user')){
				
				$session = Session::get('user');

				$user = Seguridad::desencriptar($session[0], 1);
				$hash = $session[1];

				$hashUser = $this->getHash($user);

				return ($hash === $hashUser)? true : false;

			} else {

				return false;

			}

		}

	/**
	*
	* Método encargado de obtener el Usuario actual.
	*
	* @param string $hash HASH del Usuario.
	*
	* @return string Usuario.
	*
	*/

		public function getUser($hash = '')
		{

			if(empty($hash)){

				return ($this->isLogged())? Session::get('username') : false;

			} else {

				$username = $this->_db->select("SELECT usuario FROM usuarios WHERE hash = '". $hash ."' LIMIT 1")[0]->usuario;
				$username = Seguridad::desencriptar($username, 1);

				return $username;

			}

		}

	/**
	*
	* Método encargado de obtener el Nombre y los Apellidos del Usuario.
	*
	* @param string $user Usuario.
	*
	* @return array [nombre, apellidos].
	*
	*/

		public function getNameSurname($user = '')
		{

			if(empty($user))
				$user = $this->getUser();

			$hash = $this->getHash($user);

			$data = $this->_db->select("SELECT nombre, apellidos FROM datos_personales WHERE hash_usuario = '". $hash ."' LIMIT 1")[0];

			$nombre = [
				'nombre'    => $data->nombre,
				'apellidos' => $data->apellidos];

			return $nombre;

		}

}

?>