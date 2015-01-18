<?php namespace models;

use \helpers\security as Seguridad,
	\helpers\session as Session;
	 
class User extends \core\model {

	public $username;
	
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

	/**
	*
	* Método encargado de cambiar la Contraseña de un Usuario.
	*
	* @param string $user Usuario.
	* @param string $password Nueva Contraseña.
	*
	* @return boolean TRUE si se cambió, FALSE si no.
	*
	*/

		public function setPassword($user, $password)
		{

			$hash = $this->getHash($user);

			$update = [
				'password' => hash('sha512', $password)];

			$query = $this->_db->update('usuarios', $update, ['hash' => $hash]);

			return ($query)? true : false;

		}

	/**
	*
	* Método encargado de obtener el color del círculo del 
	* Menú Izquierdo de la Interfaz.
	*
	* @param string $user Usuario.
	*
	* @return string Hexadecimal del color.
	*
	*/

		public function getCircleColor($user = '')
		{

			$user = (empty($user))? Session::get('username') : $user;

			$hashUsuario = $this->getHash($user);

			$hexadecimal = $this->_db->select("SELECT color_circulo FROM usuarios WHERE hash = '". $hashUsuario ."'")[0]->color_circulo;

			return $hexadecimal;

		}

	/**
	*
	* Método encargado de cambiar el color del círculo 
	* del Menú Izquierdo de la Interfaz.
	*
	* @param string $hex Color Hexadecimal.
	* @param string $user Usuario.
	*
	* @return boolean TRUE si se ha cambiado, FALSE si no.
	*
	*/

		public function setCircleColor($hex, $user = '')
		{

			$user = (empty($user))? Session::get('username') : $user;

			$hashUsuario = $this->getHash($user);

			$update = [
				'color_circulo' => $hex];

			$result = $this->_db->update('usuarios', $update, ['hash' => $hashUsuario]);

			return ($result)? true : false;

		}

	/**
	*
	* Método encargado de obtener usuarios cercanos a un término de 
	* búsqueda por Nombre y Apellidos.
	*
	* @param string $termino Término de Búsqueda.
	*
	* @return array Nombres y Apellidos encontrados.
	*
	*/

		public function searchLike($termino)
		{

			$results = $this->_db->select("SELECT nombre, apellidos FROM datos_personales WHERE MATCH(nombre,apellidos) AGAINST ('". $termino ."' IN BOOLEAN MODE)");

			return $results;

		}

}

?>