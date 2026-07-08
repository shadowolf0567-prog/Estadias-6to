-- Backup de Base de Datos Emipac
-- Fecha: 2026-07-08 18:32:10
DROP DATABASE IF EXISTS emipac;
CREATE DATABASE emipac;
USE emipac;
SET FOREIGN_KEY_CHECKS=0;

-- Estructura de tabla: clientes
DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `no_cuenta` varchar(100) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Estructura de tabla: correos
DROP TABLE IF EXISTS `correos`;
CREATE TABLE `correos` (
  `correo` varchar(200) DEFAULT NULL,
  `es_principal` tinyint(1) DEFAULT 0,
  `id_cliente` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contacto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_cliente` (`id_cliente`),
  CONSTRAINT `correos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Estructura de tabla: equipos
DROP TABLE IF EXISTS `equipos`;
CREATE TABLE `equipos` (
  `id_equipo` int(11) NOT NULL AUTO_INCREMENT,
  `no_serie` varchar(100) NOT NULL,
  `modelo` varchar(255) DEFAULT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `inicio_contrato` date DEFAULT NULL,
  `fin_contrato` date DEFAULT NULL,
  PRIMARY KEY (`id_equipo`),
  KEY `id_cliente` (`id_cliente`),
  CONSTRAINT `equipos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Estructura de tabla: reportes
DROP TABLE IF EXISTS `reportes`;
CREATE TABLE `reportes` (
  `id_reporte` int(11) NOT NULL AUTO_INCREMENT,
  `reporte` varchar(100) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `tecnico` varchar(100) DEFAULT NULL,
  `refaccion` varchar(100) DEFAULT NULL,
  `estado` enum('pendiente','atendido') DEFAULT 'pendiente',
  `fecha_atencion` date DEFAULT NULL,
  `observaciones_atencion` text DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `id_equipo` int(11) DEFAULT NULL,
  `acciones` text DEFAULT NULL,
  PRIMARY KEY (`id_reporte`),
  KEY `idx_cliente` (`id_cliente`),
  KEY `idx_equipo` (`id_equipo`),
  CONSTRAINT `reportes_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE SET NULL,
  CONSTRAINT `reportes_ibfk_2` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id_equipo`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Estructura de tabla: telefonos
DROP TABLE IF EXISTS `telefonos`;
CREATE TABLE `telefonos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(25) DEFAULT NULL,
  `es_principal` tinyint(1) DEFAULT 0,
  `id_cliente` int(11) DEFAULT NULL,
  `contacto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_cliente` (`id_cliente`),
  CONSTRAINT `telefonos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Estructura de tabla: usuarios
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id_usr` int(11) NOT NULL AUTO_INCREMENT,
  `nom_usr` varchar(255) NOT NULL,
  `mail` varchar(255) DEFAULT NULL,
  `pass` varchar(255) DEFAULT NULL,
  `tip_usr` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_usr`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--Datos de tabla: usuarios
INSERT INTO `usuarios` (`id_usr`, `nom_usr`, `mail`, `pass`, `tip_usr`) VALUES ('10', 'a', 'serviciotecnico@gmail.com', '12345', '2');
INSERT INTO `usuarios` (`id_usr`, `nom_usr`, `mail`, `pass`, `tip_usr`) VALUES ('11', 'b', 'administracion@gmail.com', '12345', '1');
INSERT INTO `usuarios` (`id_usr`, `nom_usr`, `mail`, `pass`, `tip_usr`) VALUES ('12', 'yo', 'test@gmail.com', '12345', '2');

SET FOREIGN_KEY_CHECKS=1;
