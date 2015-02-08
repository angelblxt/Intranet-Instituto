## Instalación ##
* Cercionarse de que PHP está disponible en su última versión.
* Comprobar que el Módulo Rewrite de Apache2.2 esté activado.
* En la Intranet, en el fichero **app/core/config.php** cambiar lo siguiente:
```
define('DIR', 'http://localhost/intranet/');
```
```
define('DB_HOST', 'localhost');
define('DB_NAME', 'intranet');
define('DB_USER', 'root');
define('DB_PASS', 'pass');
```
* Editar la siguiente línea del archivo **.htaccess**

```
RewriteBase /intranet/
```
* En el archivo **php.ini** de configuración del PHP del Servidor cambiar **post_max_filesize** a un valor de **128M**.
* Dar permisos de lectura y escritura a la carpeta **app/filesystem/** y al archivo **errorlog.html**.