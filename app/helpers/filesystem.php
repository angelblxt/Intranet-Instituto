<?php namespace helpers;

/**
*
* Sistema de Archivos - Funciones de utilidad.
*
* @author Ángel Querol García <angelquerolgarcia@gmail.com>
*
*/

class Filesystem {

	private $fs;

	/**
	*
	* Método encargado de inicializar el Sistema de Archivos Personal.
	*
	* Debe ser llamado al principio.
	*
	* @param string $user Usuario para poder acceder a su Sistema de Archivos.
	*
	*/

		public function personalFS($user = '')
		{

			$user = (empty($user))? $this->username : $user;

			$hashUsuario = $this->_user->getHash($user);

			$personalFolder = FS_ROOT . $hashUsuario .'/';

			if(!file_exists($personalFolder)){

				mkdir($personalFolder, 0777, true);
				chmod($personalFolder, 0777);

				$this->fs = $personalFolder;

			} else {

				$this->fs = $personalFolder;

			}

		}

	/**
	*
	* Método encargado de obtener el tamaño de un archivo.
	*
	* @param string $path Dirección hasta el archivo.
	*
	* @return mixed Número de Bytes, FALSE si no se puede obtener.
	*
	*/

		public function getFileSize($path)
		{

			$dir = $this->fs . $path;

			return filesize($dir);

		}

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

			$dir = $this->fs . $path;

			if(!file_exists($dir)){

				$result = mkdir($dir, 0777, true);

				chmod($dir, 0777);

			} else {

				$result = false;
				
			}

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

			$dir = $this->fs . $path;
			$dir = str_replace('../', '', $dir);

			if(is_dir($dir)){

				$files = glob($dir . '/*');

				foreach($files as $file){

					$pathToMethod = str_replace($this->fs, '', $file);

					is_dir($file)? self::deleteFolder($pathToMethod) : unlink($file);

				}

				return rmdir($dir);

			} else {

				return false;

			}

		}

	/**
	*
	* Método encargado de eliminar un archivo.
	*
	* @param string $path PATH.
	*
	* @return boolean TRUE si se ha eliminado, FALSE si no.
	*
	*/

		public function deleteFile($path)
		{

			$dir = $this->fs . $path;
			$dir = str_replace('../', '', $dir);

			if(file_exists($dir)){

				return (unlink($dir))? true : false;

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

			$dir = $this->fs . $path;

			$dir = str_replace('../', '', $dir);
			$old = str_replace('../', '', $old);
			$new = str_replace('../', '', $new);

			return (file_exists($dir . $old))? rename($dir . $old, $dir . $new) : false;

		}

	/**
	*
	* Método encargado de mover un archivo o directorio.
	*
	* @param string $old Archivo o Directorio a mover.
	* @param string $new Archivo o Directorio de destino.
	*
	* @return boolean TRUE si se ha movido, FALSE si no.
	*
	*/

		public function move($old, $new)
		{

			$old = $this->fs . $old;
			$new = $this->fs . $new;

			$old = str_replace('../', '', $old);
			$new = str_replace('../', '', $new);

			return (file_exists($old))? rename($old, $new) : false;

		}

	/**
	*
	* Método encargado de mostrar un listado de los archivos 
	* y directorios de una carpeta.
	*
	* @param string $path Carpeta a la que se ha movido.
	*
	* @return array Directorios.
	*
	*/

		public function listFolders($path = '')
		{

			$dir     = $this->fs . $path;
			$opendir = opendir($dir);

			$return = [];

			if(is_dir($dir)){

				while($archivo = readdir($opendir)){

					if(is_dir($dir . $archivo)){

						if($archivo != '.' && $archivo != '..'){
							
							$return[] = [
								'type' => 'dir',
								'name' => str_replace('_', ' ', $archivo),
								'size' => self::getFileSize($path . $archivo),
								'path' => $path . $archivo .'/'];
						
						}

					} else {

						$return[] = [
							'type' => 'file',
							'name' => $archivo,
							'size' => self::getFileSize($path . $archivo),
							'path' => $path . $archivo];

					}

				}

				closedir($opendir);

				array_multisort($return, SORT_REGULAR);

				return $return;

			} else {

				return false;

			}

		}

	/**
	*
	* Método encargado de comprobar la existencia de una carpeta.
	*
	* @param string $folder Carpeta.
	*
	* @return boolean TRUE si existe, FALSE si no.
	*
	*/

		public function comprobeFolder($folder)
		{

			return is_dir($this->fs . $folder);

		}

	/**
	*
	* Método encargado de obtener la carpeta anterior de un PATH.
	*
	* @param string $path PATH.
	*
	* @return string Dirección.
	*
	*/

		public function getAnteriorPath($path)
		{

			$dirs   = explode('/', $path);
			$number = count($dirs);

			if($number > 1){
			
				unset($dirs[$number - 2]);
				
				$previous = implode('/', $dirs);

			} else {

				$previous = '';

			}

			return $previous;

		}

	/**
	*
	* Método encargado de obtener el nombre de la carpeta de un PATH.
	*
	* @param string $path PATH.
	*
	* @return string Nombre de la Carpeta.
	*
	*/

		public function getFolderName($path)
		{

			$explode = explode('/', $path);
			$numero = count($explode);

			$nombre = $explode[$numero - 2];

			return $nombre;

		}

	/**
	*
	* Método encargado de obtener el nombre de un archivo.
	*
	* @param string $path PATH.
	*
	* @return string Nombre del Archivo.
	*
	*/

		public function getFileName($path)
		{

			$explode = explode('/', $path);
			$numero = count($explode);

			$nombre = $explode[$numero - 1];

			return $nombre;

		}

	/**
	*
	* Método encargado de obtener la carpeta en la que está un archivo.
	*
	* @param string $path PATH.
	*
	* @return string PATH de la Carpeta.
	*
	*/

		public function getFolderOfFile($path)
		{

			$explode = explode('/', $path);
			$numero = count($explode);

			unset($explode[$numero - 1]);

			$newPath = implode('/', $explode);

			return $newPath .'/';

		}

	/**
	*
	* Método encargado de comprimir en un ZIP un Directorio.
	*
	* @param string $path PATH a Comprimir.
	* @param string $nombre Nombre del archivo ZIP.
	*
	* @return boolean TRUE si se ha comprimido, FALSE si no.
	*
	*/

		public function comprimeFolder($path = '', $nombre)
		{

			$zipSalida = $nombre .'.zip';

			$pathComprimir = realpath($this->fs . $path);
			$pathSalida    = FS_ROOT . 'tmp/' . $zipSalida;

			if(file_exists($pathSalida))
				unlink($pathSalida);

			$zip = new \ZipArchive;

			if(!$zip->open($pathSalida, \ZIPARCHIVE::CREATE))
				return false;

			if(is_dir($pathComprimir)){

				$files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($pathComprimir), \RecursiveIteratorIterator::SELF_FIRST);

				foreach($files as $file){

					if( in_array(substr($file, strrpos($file, '/') + 1), ['.', '..']) )
						continue;

					$file = realpath($file);

					if(is_dir($file)){

						$zip->addEmptyDir(str_replace($pathComprimir, '', $file . '/'));

					} elseif(is_file($file)){

						$zip->addFromString(str_replace($pathComprimir, '', $file), file_get_contents($file));

					}

				}

			} elseif(is_file($pathComprimir)) {

				$zip->addFromString(basename($pathComprimir), file_get_contents($pathComprimir));

			}

			return $zip->close();

		}

	/**
	*
	* Método encargado de forzar la descarga de un archivo.
	*
	* @param boolean $path PATH del Archivo.
	* @param string $tmp TRUE: está en tmp/; FALSE: no está en tmp/.
	* @param string $delete TRUE se eliminará después de descargar. FALSE: No se eliminará.
	*
	* @return Archivo.
	*
	*/

		public function download($path, $delete = true, $tmp = true)
		{

			$dir = ($tmp === true)? FS_ROOT . 'tmp/' . $path : $this->fs . $path;

			if(file_exists($dir)){

				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename='. basename($dir));
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($dir));
				readfile($dir);

				if($delete === true)
					unlink($dir);

				exit;

			} else {

				return false;

			}

		}

	/**
	*
	* Método encargado de subir archivos.
	*
	* @param string $path PATH.
	* @param array $files $_FILES.
	*
	* @return boolean TRUE si se han subido los archivo, FALSE si no.
	*
	*/

		public function upload($path, $files)
		{

			// Datos iniciales.
			$invalidFormats = [
				'php', 'exe', 'pif', 'application', 'gadget', 'msi', 'msp', 'com', 'scr', 'hta', 
				'sh', 'bash', 'jar', 'bat', 'cmd', 'vb', 'vbs', 'js', 'jse', 'css', 'jse', 'lnk', 
				'reg', 'bin', 'dll', 'sys', 'ocx', 'sql'];
			$maxFileSize = MAX_SIZE * 1024 * 1024;

			$errors = 0;

			$dir = $this->fs . $path;

			foreach($files['files']['name'] as $f => $name){

				if($files['files']['error'][$f] == 4)
					continue;

				if($files['files']['error'][$f] == 0){

					if($files['files']['size'][$f] > $maxFileSize){

						$errors++;
						continue;

					} elseif(in_array(pathinfo($name, PATHINFO_EXTENSION), $invalidFormats)){

						$errors++;
						continue;

					} else {

						if(!move_uploaded_file($files['files']['tmp_name'][$f], $dir . $name)){

							$errors++;
							continue;

						}

					}

				}

			}

			return $errors;

		}

	/**
	*
	* Método encargado de iterar dentro de una Carpeta.
	*
	* @param string $path PATH.
	*
	* @return array Interación del Directorio.
	*
	*/

		public function iterator($path)
		{

			$path = $this->fs . $path;

			$paths = [];

			$objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::SELF_FIRST);
			
			foreach($objects as $name => $object){

				$name = str_replace($this->fs, '', $name);

				if(substr($name, -1) != '.' && substr($name, -2) != '..')
					$paths[] = $name;
			
			}

			return $paths;

		}

}