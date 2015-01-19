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

	/**
	*
	* Método encargado de mostrar de forma mas COOL una fecha.
	*
	* @param string $timestamp
	*
	* @return Hace X tiempo.
	*
	*/

		public function timeAgo($timestamp)
		{

			$intervalos = ['segundo', 'minuto', 'hora', 'día', 'semana', 'mes', 'año'];
			$duraciones = [60, 60, 24, 7, 4.35, 12];

			$ahora = time();

			if($ahora > $timestamp){

				$diferencia = $ahora - $timestamp;
				$tiempo = 'Hace';

			} else {

				$diferencia = $timestamp - $ahora;
				$tiempo = 'Dentro de';

			}

			for($j = 0; $diferencia >= $duraciones[$j] && $j < count($duraciones); $j++){

				$diferencia /= $duraciones[$j];

			}

			$diferencia = round($diferencia);

			if($diferencia != 1){

				$intervalos[5] .= 'e';
				$intervalos[$j] .= 's';

			}

			return $tiempo .' '. $diferencia .' '. $intervalos[$j];

		}

	/**
	*
	* Método encargado de cortar un texto a X palabras.
	*
	* @param string $texto Texto a cortar.
	* @param int $limite Límite de palabras.
	*
	* @return string Texto cortado.
	*
	*/

		public function cortarTexto($texto, $limite)
		{

			if (str_word_count($texto, 0) > $limite) {
				
				$words = str_word_count($texto, 2);
				$pos   = array_keys($words);
				$texto = substr($texto, 0, $pos[$limite]) . '...';

			}

			return $texto;

		}

}