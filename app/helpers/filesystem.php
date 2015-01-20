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

}