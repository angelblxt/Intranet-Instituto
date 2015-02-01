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
	* Método encargado de registrar un Usuario.
	*
	*/

		/* public function register()
		{

			$hash = md5(microtime());

			$usuarios = [
				'hash'              => $hash,
				'usuario'           => Seguridad::encriptar('aamellas', 1),
				'password'          => hash('sha512', 'k9cbbzk9cbbz'),
				'color_circulo'     => '607d8b',
				'tiempo_registrado' => time()];

			$rangos = [
				'hash'         => md5(microtime()),
				'hash_usuario' => $hash,
				'rango'        => Seguridad::encriptar('0', 1)];

			$datos_personales = [
				'hash'         => md5(microtime()),
				'hash_usuario' => $hash,
				'nombre'       => 'Adolfo',
				'apellidos'    => 'Amella Santolaria',
				'curso'        => Seguridad::encriptar('BA1', 1)];

			$this->_db->insert('usuarios', $usuarios);
			$this->_db->insert('rangos', $rangos);
			$this->_db->insert('datos_personales', $datos_personales);

			echo 'Ok!';

		} */

	/**
	*
	* Método encargado de obtener la contraseña de un Usuario.
	*
	* @param string $user Usuario.
	*
	* @return string Contraseña.
	*
	*/

		public function getPassword($user)
		{

			$user = Seguridad::encriptar($user, 1);

			$pass = $this->_db->select("SELECT password FROM usuarios WHERE usuario = '". $user ."' LIMIT 1")[0]->password;

			return $pass;

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

			return (hash('sha512', $password) === self::getPassword($user))? true : false;

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
	* Método encargado de obtener el Curso del Usuario.
	*
	* @param string $user Usuario.
	*
	* @return string Curso.
	*
	*/

		public function getCurso($user)
		{

			$hash = self::getHash($user);

			$curso = $this->_db->select("SELECT curso FROM datos_personales WHERE hash_usuario = :hashUsuario", [':hashUsuario' => $hash])[0]->curso;
			$curso = Seguridad::desencriptar($curso, 1);

			return $curso;

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

	/**
	*
	* Método encargado de obtener el usuario por medio del Nombre Completo.
	*
	* @param string $nombreCompleto Nombre Completo del Usuario.
	*
	* @return string Usuario.
	*
	*/

		public function getUserByName($nombreCompleto)
		{

			$result = $this->_db->select("SELECT hash_usuario FROM datos_personales WHERE CONCAT(nombre, ' ', apellidos) = '". $nombreCompleto ."'");

			return $result[0];

		}

	/**
	*
	* Método encargado de comprobar si un usuario existe.
	*
	* @param string $hash HASH del Usuario.
	*
	* @return boolean TRUE si existe, FALSE si no.
	*
	*/

		public function exists($hash)
		{

			$results = $this->_db->num("SELECT COUNT(*) FROM usuarios WHERE hash = :hash LIMIT 1", [':hash' => $hash]);

			return ($results > 0)? true : false;

		}

	/**
	*
	* Método encargado de devolver el Rango de un Usuario.
	*
	* @param string $user Usuario.
	*
	* @return int Número de Rango [0: Alumno, 1: Profesor, 2: Administrador].
	*
	*/

		public function getRank($user)
		{

			$hash = self::getHash($user);

			$rank = $this->_db->select("SELECT rango FROM rangos WHERE hash_usuario = :hashUsuario LIMIT 1", [':hashUsuario' => $hash])[0]->rango;
			$rank = Seguridad::desencriptar($rank, 1);

			return (int)$rank;

		}

	/**
	*
	* Método encargado de comprobar si el usuario es Alumno.
	*
	* @param string $user Usuario.
	*
	* @return boolean TRUE si es cierto, FALSE si no.
	*
	*/

		public function isStudent($user = '')
		{

			$user = (empty($user))? $this->getUser() : $user;

			return (self::getRank($user) === 0)? true : false;

		}

	/**
	*
	* Método encargado de comprobar si el usuario es Profesor.
	*
	* @param string $user Usuario.
	*
	* @return boolean TRUE si es cierto, FALSE si no.
	*
	*/

		public function isTeacher($user = '')
		{

			$user = (empty($user))? $this->getUser() : $user;

			return (self::getRank($user) === 1)? true : false;

		}

	/**
	*
	* Método encargado de comprobar si el usuario es Administrador.
	*
	* @param string $user Usuario.
	*
	* @return boolean TRUE si es cierto, FALSE si no.
	*
	*/

		public function isAdmin($user = '')
		{

			$user = (empty($user))? $this->getUser() : $user;

			return (self::getRank($user) === 2)? true : false;

		}

	/**
	*
	* Método encargado de obtener todos los Usuarios registrados.
	*
	* @param string $limit LIMIT SQL.
	*
	* @return array Datos de los Usuarios.
	*
	*/

		public function getUsers($limit = '')
		{

			$users = $this->_db->select("SELECT hash, usuario, tiempo_registrado FROM usuarios ORDER BY id DESC ". $limit);

			return $users;

		}

	/**
	*
	* Método encargado de obtener el número de Usuarios registrados.
	*
	* @return int Número de Usuarios registrados. 
	*
	*/

		public function getNumber()
		{

			$number = $this->_db->num("SELECT COUNT(*) FROM usuarios");

			return (int)$number;

		}

	/**
	*
	* Método encargado de eliminar a un Usuario del Sistema.
	*
	* @param string $hash HASH del Usuario.
	*
	* @return boolean TRUE si se ha eliminado, FALSE si no.
	*
	*/

		public function delete($hash)
		{

			// Cargamos Modelos
			$fs = new \models\filesystem();

			// RANGOS //
			$rangos = $this->_db->delete('rangos', ['hash_usuario' => $hash]);

			// MENSAJES PRIVADOS //
			$mensajesPrivados1 = $this->_db->delete('mensajes_privados', ['hash_emisor' => $hash]);
			$mensajesPrivados2 = $this->_db->delete('mensajes_privados', ['hash_receptor' => $hash]);

			// DATOS PERSONALES //
			$datosPersonales = $this->_db->delete('datos_personales', ['hash_usuario' => $hash]);

			// COMPARTICIONES //
			$comparticiones = $fs->getPathsSharedWithMe($hash);

			if(count($comparticiones) > 0){

				foreach($comparticiones as $comparticion){

					$fs->unshare($comparticion['path'], $hash);

				}

			}

			// USUARIO //
			$usuario = $this->_db->delete('usuarios', ['hash' => $hash]);

			return ($rangos && $mensajesPrivados1 && $mensajesPrivados2 && $datosPersonales && $usuario)? true : false;

		}

	/**
	*
	* Método encargado de editar un usuario.
	*
	* @param string $hash HASH del Usuario.
	* @param string $user Nuevo Usuario.
	* @param string $nombre Nombre.
	* @param string $apellidos Apellidos.
	* @param string $password Contraseña.
	* @param string $curso Curso.
	* @param string $rango Rango.
	*
	* @return boolean TRUE si se ha editado, FALSE si no.
	*
	*/

		public function edit($hash, $user, $nombre, $apellidos, $password, $curso, $rango)
		{

			$usuarios = [
				'usuario'  => Seguridad::encriptar($user, 1),
				'password' => $password];

			$datosPersonales = [
				'nombre'    => $nombre,
				'apellidos' => $apellidos,
				'curso'     => Seguridad::encriptar($curso, 1)];

			$rangos = [
				'rango' => Seguridad::encriptar($rango, 1)];

			$resultUsuarios        = $this->_db->update('usuarios', $usuarios, ['hash' => $hash]);
			$resultDatosPersonales = $this->_db->update('datos_personales', $datosPersonales, ['hash_usuario' => $hash]);
			$resultRangos          = $this->_db->update('rangos', $rangos, ['hash_usuario' => $hash]);

			return ($resultUsuarios && $resultDatosPersonales && $resultRangos)? true : false;

		}

}

?>