-- --------------------------------------------------------
-- Host:                         localhost
-- Versión del servidor:         5.5.41-0ubuntu0.14.04.1 - (Ubuntu)
-- SO del servidor:              debian-linux-gnu
-- HeidiSQL Versión:             9.1.0.4867
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Volcando estructura de base de datos para intranet
CREATE DATABASE IF NOT EXISTS `intranet` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `intranet`;


-- Volcando estructura para tabla intranet.comparticiones
CREATE TABLE IF NOT EXISTS `comparticiones` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hash` char(32) NOT NULL,
  `hash_usuario` char(32) NOT NULL,
  `hash_usuarios_compartidos` text NOT NULL,
  `direccion` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.


-- Volcando estructura para tabla intranet.datos_personales
CREATE TABLE IF NOT EXISTS `datos_personales` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hash` char(32) NOT NULL,
  `hash_usuario` char(32) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellidos` varchar(50) NOT NULL,
  `curso` varchar(200) NOT NULL COMMENT 'Encriptado (1)',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `nombre_completo` (`nombre`,`apellidos`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.


-- Volcando estructura para tabla intranet.logs
CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hash_usuario` char(32) NOT NULL,
  `ip` int(10) unsigned NOT NULL,
  `log` text NOT NULL COMMENT 'Encriptado (2)',
  `tiempo` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.


-- Volcando estructura para tabla intranet.mensajes_privados
CREATE TABLE IF NOT EXISTS `mensajes_privados` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hash` char(32) NOT NULL,
  `hash_emisor` char(32) NOT NULL,
  `hash_receptor` char(32) NOT NULL,
  `asunto` text NOT NULL COMMENT 'Encriptado (1)',
  `contenido` mediumtext NOT NULL COMMENT 'Encriptado (2)',
  `tiempo_enviado` int(10) unsigned NOT NULL,
  `leido_receptor` int(1) unsigned NOT NULL DEFAULT '0',
  `borrado_emisor` int(1) unsigned NOT NULL DEFAULT '0',
  `borrado_receptor` int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.


-- Volcando estructura para tabla intranet.rangos
CREATE TABLE IF NOT EXISTS `rangos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hash` char(32) NOT NULL,
  `hash_usuario` char(32) NOT NULL,
  `rango` varchar(200) NOT NULL COMMENT 'Encriptado (1); 0: Alumno; 1: Profesor; 2: Administrador',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.


-- Volcando estructura para tabla intranet.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hash` char(32) NOT NULL,
  `usuario` varchar(200) NOT NULL COMMENT 'Encriptado (1)',
  `password` char(128) NOT NULL,
  `color_circulo` char(6) NOT NULL,
  `tiempo_registrado` int(10) NOT NULL,
  `tiempo_ultima_conexion` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
