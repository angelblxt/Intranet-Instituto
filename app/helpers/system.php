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
	* Método encargado de devolver los colores del Círculo en array.
	*
	* @return array Colores del Círculo.
	*
	*/

		public function circleColors()
		{

			$colors = ['f44336', 'e91e63', '9c27b0', '3f51b5', '009688', '8bc34a', 'ff9800', '795548', '607d8b'];

			return $colors;

		}

	/**
	*
	* Método encargado de devolver array con los Cursos.
	*
	* @return array ['ID Curso' => 'Nombre Legible'].
	*
	*/

		public function cursos()
		{

			$cursos = [
				'1A'   => '1º ESO A',
				'1B'   => '1º ESO B',
				'1C'   => '1º ESO C',
				'1D'   => '1º ESO D',
				'1E'   => '1º ESO E',
				'1F'   => '1º ESO F',
				'1G'   => '1º ESO G',
				'1PAB' => '1º P.A.B',
				'2A'   => '2º ESO A',
				'2B'   => '2º ESO B',
				'2C'   => '2º ESO C',
				'2D'   => '2º ESO D',
				'2E'   => '2º ESO E',
				'2F'   => '2º ESO F',
				'2PAB' => '2º P.A.B',
				'3A'   => '3º ESO A',
				'3B'   => '3º ESO B',
				'3C'   => '3º ESO C',
				'3D'   => '3ª ESO D',
				'3DIV' => '3º Diversificación',
				'4A'   => '4º ESO A',
				'4B'   => '4º ESO B',
				'4C'   => '4º ESO C',
				'4DIV' => '4º Diversificación',
				'B1A'  => '1º Bachillerato A',
				'B1B1' => '1º Bachillerato B1',
				'B1B2' => '1º Bachillerato B2',
				'B1C'  => '1º Bachillerato C',
				'B2A'  => '2º Bachillerato A',
				'B2B1' => '2º Bachillerato B1',
				'B2B2' => '2º Bachillerato B2',
				'B2C'  => '2º Bachillerato C',
				'CAD1' => '1º G.M Administrativo',
				'CAD2' => '2º G.M Administrativo',
				'CEL1' => '1º G.M Electricidad',
				'CEL2' => '2º G.M Electricidad',
				'PCA1' => '1º P.C.P.I Administrativo',
				'PCE1' => '1º P.C.P.I Electricidad'];

			return $cursos;

		}

	/**
	*
	* Método encargado de obtener el nombre legible de un Rango.
	*
	* @param int $rango Número del Rango (0, 1, 2).
	*
	* @return string Nombre del Rango.
	*
	*/

		public function getRango($rango)
		{

			$rangos = ['Alumno', 'Profesor', 'Administrador'];

			return $rangos[$rango];

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

			$cursos = self::cursos();

			return $cursos[$curso];

		}

}