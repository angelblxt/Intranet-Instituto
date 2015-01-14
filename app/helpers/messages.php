<?php namespace helpers;

/**
 * Clase de manejo de los mensajes.
 * 
 * @author volter9
 * @date 14th January, 2015
 */

class Messages {

	/**
	*
	* Método encargado de procesar el error devuelto por el formulario e imprimirlo.
	* La $_SESSION['error'] tendrá un array como valor, los cuales serán [tipo, string].
	*
	* @return string Error devuelto por X formulario.
	*
	* @see System::mensaje();
	*
	*/

		public function comprobarErrores()
		{

			if( isset($_SESSION['error']) ){

				echo self::mensaje($_SESSION['error'][0], $_SESSION['error'][1]);

				unset( $_SESSION['error'] );

			}

		}

	/**
	*
	* Método encargado de mostrar los mensajes de Error, Éxito y Precaución.
	*
	* @param string $string Texto que aparecerá en el mensaje.
	* @param string $tipo Tipo de mensaje (bien / mal / precaución).
	*
	* @return string Mensajes de Error/Éxito/Precaución.
	*
	*/

		public function mensaje( $string, $tipo )
		{

			switch( $tipo ){
				
				case 'bien'      : $icon = 'check-circle'; break;
				case 'mal'       : $icon = 'times-circle'; break;
				case 'precaucion': $icon = 'warning'; break;

			}

			return '<div class="mensaje stripes '. $tipo .'"> <i class="fa fa-'. $icon .'" style="margin-right: 5px"></i> '. $string .' </div>';

		}

}