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

	/**
	*
	* Método encargado de obtener el Texto Legible del Curso de un Alumno.
	*
	* @param string $curso Curso en formato no legible. Ej.: BA1.
	*
	* @return string Curso en formato legible.
	*
	*/

		public function getCurso($curso)
		{

			switch( $curso ){

				case '1A'  : $nombre = '1º ESO A'; break;
				case '1B'  : $nombre = '1º ESO B'; break;
				case '1C'  : $nombre = '1º ESO C'; break;
				case '1D'  : $nombre = '1º ESO D'; break;
				case '1E'  : $nombre = '1º ESO E'; break;
				case '1F'  : $nombre = '1º ESO F'; break;
				case '1G'  : $nombre = '1º ESO G'; break;
				case '1PAB': $nombre = '1º P.A.B'; break;
				case '2A'  : $nombre = '2º ESO A'; break;
				case '2B'  : $nombre = '2º ESO B'; break;
				case '2C'  : $nombre = '2º ESO C'; break;
				case '2D'  : $nombre = '2º ESO D'; break;
				case '2E'  : $nombre = '2º ESO E'; break;
				case '2F'  : $nombre = '2º ESO F'; break;
				case '2PAB': $nombre = '2º P.A.B'; break;
				case '3A'  : $nombre = '3º ESO A'; break;
				case '3B'  : $nombre = '3º ESO B'; break;
				case '3C'  : $nombre = '3º ESO C'; break;
				case '3D'  : $nombre = '3ª ESO D'; break;
				case '3DIV': $nombre = '3º Diversificación'; break;
				case '4A'  : $nombre = '4º ESO A'; break;
				case '4B'  : $nombre = '4º ESO B'; break;
				case '4C'  : $nombre = '4º ESO C'; break;
				case '4DIV': $nombre = '4º Diversificación'; break;
				case 'B1A' : $nombre = '1º Bachillerato A'; break;
				case 'B1B1': $nombre = '1º Bachillerato B1'; break;
				case 'B1B2': $nombre = '1º Bachillerato B2'; break;
				case 'B1C' : $nombre = '1º Bachillerato C'; break;
				case 'B2A' : $nombre = '2º Bachillerato A'; break;
				case 'B2B1': $nombre = '2º Bachillerato B1'; break;
				case 'B2B2': $nombre = '2º Bachillerato B2'; break;
				case 'B2C' : $nombre = '2º Bachillerato C'; break;
				case 'CAD1': $nombre = '1º G.M Administrativo'; break;
				case 'CAD2': $nombre = '2º G.M Administrativo'; break;
				case 'CEL1': $nombre = '1º G.M Electricidad'; break;
				case 'CEL2': $nombre = '2º G.M Electricidad'; break;
				case 'PCA1': $nombre = '1º P.C.P.I Administrativo'; break;
				case 'PCE1': $nombre = '1º P.C.P.I Electricidad'; break;

			}

				return $nombre;

		}

}