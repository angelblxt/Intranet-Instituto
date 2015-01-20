<?php namespace helpers;

/**
*
* Sistema de Archivos - Funciones de utilidad.
*
* @author Ángel Querol García <angelquerolgarcia@gmail.com>
*
*/

class Filesystem {

	/**
	*
	* Método encargado de Formatear los Bytes a un formato humano.
	*
	* @param integer $bytes Número de Bytes.
	* @param integer $precision Número de decimales de precisión.
	*
	* @return string Formato humano.
	*
	*/

		public function formatBytes($bytes, $precision = 0)
		{

			$unidades = ['B', 'KB', 'MB', 'GB', 'TB'];

			$bytes = max($bytes, 0);
			$pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
			$pow   = min($pow, count($unidades) - 1);

			$bytes /= pow(1024, $pow);

			return round($bytes, $precision) .' '. $unidades[$pow];

		}

	/**
	*
	* Método encargado de obtener la extensión de un archivo a 
	* partir de su nombre.
	*
	* @param string $file Nombre del Fichero.
	*
	* @return string Extensión del archivo.
	*
	*/

		public function getExtension($file)
		{

			return pathinfo($file, PATHINFO_EXTENSION);

		}

	/**
	*
	* Método encargado de crear un nuevo directorio.
	*
	* @param string $path Carpeta a crear.
	*
	* @return boolean TRUE si se ha creado, FALSE si no.
	*
	*/

		public function makeFolder($path)
		{

			$dir = FS . $path;

			$result = (!file_exists($dir))? mkdir($dir, 0777, true) : true;

			return $result;

		}

	/**
	*
	* Método encargado de eliminar un directorio.
	*
	* @param string $path Carpeta a eliminar.
	*
	* @return boolean TRUE si se ha eliminado, FALSE si no.
	*
	*/

		public function deleteFolder($path)
		{

			$dir = FS . $path;
			$dir = str_replace('../', '', $dir);

			if(is_dir($dir)){

				$files = glob($dir . '/*');

				foreach($files as $file){

					$pathToMethod = str_replace(FS, '', $file);

					is_dir($file)? self::deleteFolder($pathToMethod) : unlink($file);

				}

				return rmdir($dir);

			} else {

				return false;

			}

		}

	/**
	*
	* Método encargado de renombrar un archivo o directorio.
	*
	* @param string $path Directorio donde se encuentra el archivo.
	* @param string $old Nombre antiguo.
	* @param string $new Nombre nuevo.
	*
	* @return boolean TRUE si se ha renombrado, FALSE si no.
	*
	*/

		public function rename($path, $old, $new)
		{

			$dir = FS . $path;

			return (file_exists($dir . $old))? rename($dir . $old, $dir . $new) : false;

		}

}