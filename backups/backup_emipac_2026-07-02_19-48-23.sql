-- Backup de Base de Datos Emipac
-- Fecha: 2026-07-02 19:48:23
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
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--Datos de tabla: clientes
INSERT INTO `clientes` (`id_cliente`, `nombre`, `no_cuenta`, `direccion`) VALUES ('8', 'pedro', '11111', 'lojkhijuiyv');
INSERT INTO `clientes` (`id_cliente`, `nombre`, `no_cuenta`, `direccion`) VALUES ('10', 'ju', '666', 'uytyy');
INSERT INTO `clientes` (`id_cliente`, `nombre`, `no_cuenta`, `direccion`) VALUES ('14', 'ch', '2', 'si');
INSERT INTO `clientes` (`id_cliente`, `nombre`, `no_cuenta`, `direccion`) VALUES ('15', 'piojo', '20', 'su casa');
INSERT INTO `clientes` (`id_cliente`, `nombre`, `no_cuenta`, `direccion`) VALUES ('27', 'antonio', '0987', 'uvgu');
INSERT INTO `clientes` (`id_cliente`, `nombre`, `no_cuenta`, `direccion`) VALUES ('40', 'Hospital General de Querétaropo', '123249', 'Marqués de Miraflores 462, Fraccionamiento Real del Marqués, 76118 Santiago de Querétaro, Qro');
INSERT INTO `clientes` (`id_cliente`, `nombre`, `no_cuenta`, `direccion`) VALUES ('70', 'Karla', '987', 'kln');

-- Estructura de tabla: contactos
DROP TABLE IF EXISTS `contactos`;
CREATE TABLE `contactos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(20) DEFAULT NULL,
  `correos` varchar(200) DEFAULT NULL,
  `t_princ` tinyint(1) DEFAULT 0,
  `c_princ` tinyint(1) DEFAULT 0,
  `id_cliente` int(11) DEFAULT NULL,
  `contacto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_cliente` (`id_cliente`),
  CONSTRAINT `contactos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--Datos de tabla: contactos
INSERT INTO `contactos` (`id`, `telefono`, `correos`, `t_princ`, `c_princ`, `id_cliente`, `contacto`) VALUES ('1', '984567', 'yfkhfk', '0', '0', '40', 'maria');

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--Datos de tabla: correos
INSERT INTO `correos` (`correo`, `es_principal`, `id_cliente`, `id`, `contacto`) VALUES ('kjhv@a.com', '1', '40', '3', 'gerardo');
INSERT INTO `correos` (`correo`, `es_principal`, `id_cliente`, `id`, `contacto`) VALUES ('davi6d@gmail.com', '0', '40', '4', 'gerardo');
INSERT INTO `correos` (`correo`, `es_principal`, `id_cliente`, `id`, `contacto`) VALUES ('bjk@mail.com', '1', '10', '5', 'gerardo');
INSERT INTO `correos` (`correo`, `es_principal`, `id_cliente`, `id`, `contacto`) VALUES ('kjhv@a.com', '1', '70', '8', NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--Datos de tabla: equipos
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('22', '987', 'j', '10', NULL, NULL);
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('24', '987878', 'mo', '8', NULL, NULL);
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('25', '987899', 'mo', '8', NULL, NULL);
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('26', '123456', 'n ,j', '10', NULL, NULL);
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('29', 'ml k', 'm0ikn', '14', NULL, NULL);
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('38', '123456', 'n ,j', '14', NULL, NULL);
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('39', '123456', 'n ,j', '40', '0000-00-00', '0000-00-00');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('46', 'b?n', 'ty', '27', NULL, NULL);
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('47', '987', '234t', '8', '2026-06-23', '2026-07-02');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('48', '987', 'dcvbn', '40', '2026-06-15', '2026-06-29');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('49', '123456', 'n ,j', '27', '2026-06-13', '2026-06-20');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('50', '6789', 'mole', '8', '2026-06-18', '2026-07-18');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('52', '976976', 'jgf', '8', '2026-06-29', '2026-06-30');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('60', '0123', 'jop', '40', '2026-06-30', '2026-07-07');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('64', '123456', '', '27', '0000-00-00', '0000-00-00');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('66', '76567', 'kjnj', '40', '2026-07-10', '2026-07-17');

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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--Datos de tabla: reportes
INSERT INTO `reportes` (`id_reporte`, `reporte`, `fecha`, `tecnico`, `refaccion`, `estado`, `fecha_atencion`, `observaciones_atencion`, `descripcion`, `id_cliente`, `id_equipo`, `acciones`) VALUES ('2', 'si', '2026-05-20', 'k', 'p', 'atendido', '2026-05-26', 'pknpk', 'no', '8', NULL, NULL);
INSERT INTO `reportes` (`id_reporte`, `reporte`, `fecha`, `tecnico`, `refaccion`, `estado`, `fecha_atencion`, `observaciones_atencion`, `descripcion`, `id_cliente`, `id_equipo`, `acciones`) VALUES ('3', 'iuy', '2026-06-17', '', '', 'pendiente', NULL, '', 'pimpñ', '27', NULL, NULL);
INSERT INTO `reportes` (`id_reporte`, `reporte`, `fecha`, `tecnico`, `refaccion`, `estado`, `fecha_atencion`, `observaciones_atencion`, `descripcion`, `id_cliente`, `id_equipo`, `acciones`) VALUES ('4', 'lknpubnj', '2026-05-25', '', '', 'atendido', '2026-05-28', 'lj lj', '0kkl', '10', NULL, NULL);
INSERT INTO `reportes` (`id_reporte`, `reporte`, `fecha`, `tecnico`, `refaccion`, `estado`, `fecha_atencion`, `observaciones_atencion`, `descripcion`, `id_cliente`, `id_equipo`, `acciones`) VALUES ('6', '', '2026-05-28', 'nklbjhk', '', 'atendido', '2026-05-28', '.m .m', 'k,n.bjlkhvgjvh', '10', NULL, NULL);
INSERT INTO `reportes` (`id_reporte`, `reporte`, `fecha`, `tecnico`, `refaccion`, `estado`, `fecha_atencion`, `observaciones_atencion`, `descripcion`, `id_cliente`, `id_equipo`, `acciones`) VALUES ('11', 'si', '2026-06-05', '', '', 'atendido', '2026-06-10', '', 'iigy', '8', NULL, NULL);
INSERT INTO `reportes` (`id_reporte`, `reporte`, `fecha`, `tecnico`, `refaccion`, `estado`, `fecha_atencion`, `observaciones_atencion`, `descripcion`, `id_cliente`, `id_equipo`, `acciones`) VALUES ('18', 'lj', '2026-06-16', '', '', 'atendido', '2026-06-18', '', 'jlonhi', '8', '50', NULL);
INSERT INTO `reportes` (`id_reporte`, `reporte`, `fecha`, `tecnico`, `refaccion`, `estado`, `fecha_atencion`, `observaciones_atencion`, `descripcion`, `id_cliente`, `id_equipo`, `acciones`) VALUES ('20', 'kñjo', '2026-06-23', '', '', 'pendiente', NULL, '', 'ui', '27', '49', NULL);
INSERT INTO `reportes` (`id_reporte`, `reporte`, `fecha`, `tecnico`, `refaccion`, `estado`, `fecha_atencion`, `observaciones_atencion`, `descripcion`, `id_cliente`, `id_equipo`, `acciones`) VALUES ('21', 'kñjo', '2026-06-23', '', '', 'pendiente', NULL, NULL, 'mmmm', '8', '25', NULL);
INSERT INTO `reportes` (`id_reporte`, `reporte`, `fecha`, `tecnico`, `refaccion`, `estado`, `fecha_atencion`, `observaciones_atencion`, `descripcion`, `id_cliente`, `id_equipo`, `acciones`) VALUES ('22', 'kñjo', '2026-06-29', '', '', 'pendiente', NULL, '', 'ejemplo', '40', '60', NULL);
INSERT INTO `reportes` (`id_reporte`, `reporte`, `fecha`, `tecnico`, `refaccion`, `estado`, `fecha_atencion`, `observaciones_atencion`, `descripcion`, `id_cliente`, `id_equipo`, `acciones`) VALUES ('23', 'no', '2026-07-01', 'k', 'jjjj', 'atendido', '2026-07-02', 'l', 'tyvg', '8', '50', 'k');
INSERT INTO `reportes` (`id_reporte`, `reporte`, `fecha`, `tecnico`, `refaccion`, `estado`, `fecha_atencion`, `observaciones_atencion`, `descripcion`, `id_cliente`, `id_equipo`, `acciones`) VALUES ('24', 'no', '2026-07-02', '', '', 'pendiente', NULL, NULL, 'nose', '40', '66', NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--Datos de tabla: telefonos
INSERT INTO `telefonos` (`id`, `telefono`, `es_principal`, `id_cliente`, `contacto`) VALUES ('3', '56', '1', '40', 'gerardo');
INSERT INTO `telefonos` (`id`, `telefono`, `es_principal`, `id_cliente`, `contacto`) VALUES ('4', '9898', '0', '40', NULL);
INSERT INTO `telefonos` (`id`, `telefono`, `es_principal`, `id_cliente`, `contacto`) VALUES ('5', '4427966353', '1', '27', 'gerardo');
INSERT INTO `telefonos` (`id`, `telefono`, `es_principal`, `id_cliente`, `contacto`) VALUES ('7', '56', '1', '70', NULL);

-- Estructura de tabla: usuarios
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id_usr` int(11) NOT NULL AUTO_INCREMENT,
  `nom_usr` varchar(255) NOT NULL,
  `mail` varchar(255) DEFAULT NULL,
  `pass` varchar(255) DEFAULT NULL,
  `tip_usr` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_usr`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--Datos de tabla: usuarios
INSERT INTO `usuarios` (`id_usr`, `nom_usr`, `mail`, `pass`, `tip_usr`) VALUES ('1', 'a', 'a@mail.com', '1234', '1');
INSERT INTO `usuarios` (`id_usr`, `nom_usr`, `mail`, `pass`, `tip_usr`) VALUES ('2', 'b', 'b@mail.com', '1234', '2');
INSERT INTO `usuarios` (`id_usr`, `nom_usr`, `mail`, `pass`, `tip_usr`) VALUES ('3', 'c', 'c@mail.com', '1234', '3');

SET FOREIGN_KEY_CHECKS=1;
