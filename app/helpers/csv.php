<?php namespace helpers;

	/**
	*
	* Clase encargada de la generación de archivos CSV.
	*
	* @author Ángel Querol García
	*
	*/

	class CSV
	{

		protected $data;

		/**
		*
		* Constructor de la Clase CSV();
		*
		* @param array $columns Títulos de cada columna.
		*
		*/

			public function addTitles( $columns )
			{

				$this->data = '"'. implode('";"', $columns) .'"'. "\n";

			}

		/**
		*
		* Método encargado de añadir una nueva fila de datos al documento.
		*
		* @param array $row Nueva fila con los datos encajados en sus columnas.
		*
		*/

			public function addRow( $row )
			{

				$this->data .= '"'. implode('";"', $row) .'"'. "\n";

			}

		/**
		*
		* Método encargado de exportar el archivo completo.
		*
		* @param string $filename Nombre del archivo final sin la extensión CSV.
		*
		* @return blob Archivo generado.
		*
		*/

			public function exportar( $filename )
			{

				header('Content-type: application/csv');
				header('Content-Disposition: attachment; filename="'. $filename .'.csv"');

				echo $this->data;
				die();

			}

	}

?>