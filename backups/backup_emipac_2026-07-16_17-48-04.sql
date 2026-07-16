-- Backup de Base de Datos Emipac
-- Fecha: 2026-07-16 17:48:04
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
  `encargado` varchar(255) DEFAULT NULL,
  `contrato` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--Datos de tabla: clientes
INSERT INTO `clientes` (`id_cliente`, `nombre`, `no_cuenta`, `direccion`, `encargado`, `contrato`) VALUES ('1', 'Intelligence Berau and Laborator', '291901', 'Av. Antea #1032 Int. 404 Jurica', 'Roberto Alfaro', 'C-0399');
INSERT INTO `clientes` (`id_cliente`, `nombre`, `no_cuenta`, `direccion`, `encargado`, `contrato`) VALUES ('2', 'Nachi Technologies México', '287201', 'Tequisquiapan No.2 Galerias Aerotech Industrial Park Colón', 'Benito Sanchez', 'C-0133');
INSERT INTO `clientes` (`id_cliente`, `nombre`, `no_cuenta`, `direccion`, `encargado`, `contrato`) VALUES ('3', 'Industrial Powder Coatings Mex', '130701', 'Av. de la Noria No. 104 Parque Qro.', 'Norma Luna', '');
INSERT INTO `clientes` (`id_cliente`, `nombre`, `no_cuenta`, `direccion`, `encargado`, `contrato`) VALUES ('4', 'GNS Automotive México', '268501', 'Av. Ing. Antonio Gutierrez Cortina No. 14 Parque Opción SJI', 'Oscar Nazareth', '');
INSERT INTO `clientes` (`id_cliente`, `nombre`, `no_cuenta`, `direccion`, `encargado`, `contrato`) VALUES ('5', 'SEPSA SA de CV.', '232301', 'Espuela del Ferrocarril No. 204 Carrillo Puerto', 'Sarahí Bustamante', 'C-0318');
INSERT INTO `clientes` (`id_cliente`, `nombre`, `no_cuenta`, `direccion`, `encargado`, `contrato`) VALUES ('6', 'María Magdalena Mejía Ruíz', '172401', 'Puente de Alvarado No. 210 Carretas', 'Erika Gudiño', 'C-0080');
INSERT INTO `clientes` (`id_cliente`, `nombre`, `no_cuenta`, `direccion`, `encargado`, `contrato`) VALUES ('7', 'GW Plastics Mexicana S de RL', '294901', 'Circuito Marques #23A Parque IND El Marqués.', 'Mariana Martínez', 'C-0429');

-- Estructura de tabla: componentes
DROP TABLE IF EXISTS `componentes`;
CREATE TABLE `componentes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `componente` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--Datos de tabla: componentes
INSERT INTO `componentes` (`id`, `componente`, `descripcion`) VALUES ('1', 'SER-01', 'Servicio Preventivo');
INSERT INTO `componentes` (`id`, `componente`, `descripcion`) VALUES ('2', 'SER-02', 'Servicio Correctivo');
INSERT INTO `componentes` (`id`, `componente`, `descripcion`) VALUES ('3', 'refaccion', '');
INSERT INTO `componentes` (`id`, `componente`, `descripcion`) VALUES ('4', 'componente', '');

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Estructura de tabla: equipos
DROP TABLE IF EXISTS `equipos`;
CREATE TABLE `equipos` (
  `id_equipo` int(11) NOT NULL AUTO_INCREMENT,
  `no_serie` varchar(100) NOT NULL,
  `modelo` varchar(255) DEFAULT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_equipo`),
  KEY `id_cliente` (`id_cliente`),
  CONSTRAINT `equipos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--Datos de tabla: equipos
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('1', 'C757M500195', 'S-11MPC6004R+', '1');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('2', '3353P254701', 'S-110430F', '2');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('3', 'G746R510164', 'S-11C2004R+', '2');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('4', 'G145R600271', 'S-117008', '3');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('5', 'Y177HB01092', 'S-11402SPF', '4');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('6', '3353PA50400', 'S-110430F', '4');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('7', '3353P352683', 'S-110430F', '5');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('8', '3353P350596', 'S-110430F', '6');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('9', '9264P600643', 'S-11IM460F', '7');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('10', '9264P600645', 'S-11IM460F', '7');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('11', '3148MB00315', 'S-11IMC6000R+', '7');

-- Estructura de tabla: reportes
DROP TABLE IF EXISTS `reportes`;
CREATE TABLE `reportes` (
  `id_reporte` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date DEFAULT NULL,
  `tecnico` varchar(100) DEFAULT NULL,
  `estado` enum('pendiente','atendido') DEFAULT 'pendiente',
  `fecha_atencion` date DEFAULT NULL,
  `observaciones_atencion` text DEFAULT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `id_equipo` int(11) DEFAULT NULL,
  `referencia` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id_reporte`),
  KEY `idx_cliente` (`id_cliente`),
  KEY `idx_equipo` (`id_equipo`),
  CONSTRAINT `reportes_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE SET NULL,
  CONSTRAINT `reportes_ibfk_2` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id_equipo`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--Datos de tabla: reportes
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('1', '2026-07-08', 'Damian', 'atendido', '2026-07-08', '', '1', '1', '291901-0399-0001');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('2', '2026-07-07', 'Jose Luis', 'atendido', '2026-07-07', '', '2', '2', '294901-0429-0001');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('3', '2026-07-07', '', 'atendido', '2026-07-07', '', '4', '6', '');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('4', '2026-07-10', 'Irving', 'atendido', '2026-07-10', '', '5', '7', '232301-0318-0001');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('5', '2026-07-09', 'Jose Luis', 'atendido', '2026-07-09', '', '7', '9', '294901-0429-0001');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('7', '2026-07-09', 'Jose Luis', 'atendido', '2026-07-09', '', '7', '10', '294901-0429-0002');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('11', '2026-07-10', 'Jose Luis', 'atendido', '2026-07-10', '', '1', '1', '291901-0399-0001');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('12', '2026-07-09', 'Jose Luis', 'atendido', '2026-07-09', '', '6', '8', '172401-0080-0002');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('13', '2026-07-07', 'Jose Luis', 'atendido', '2026-07-07', '', '2', '3', '287201-0133-0004');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('21', '2026-07-09', 'Jose Luis', 'atendido', '2026-07-09', '', '7', '11', '294901-0429-0001');

-- Estructura de tabla: reportes_componentes
DROP TABLE IF EXISTS `reportes_componentes`;
CREATE TABLE `reportes_componentes` (
  `id_reporte_componente` int(11) NOT NULL AUTO_INCREMENT,
  `id_reporte` int(11) DEFAULT NULL,
  `componente` varchar(100) DEFAULT NULL,
  `tipo` varchar(20) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  PRIMARY KEY (`id_reporte_componente`),
  KEY `id_reporte` (`id_reporte`),
  CONSTRAINT `1` FOREIGN KEY (`id_reporte`) REFERENCES `reportes` (`id_reporte`)
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--Datos de tabla: reportes_componentes
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('84', '13', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('90', '4', 'Servicio Correctivo', 'SER-02', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('91', '4', '130948', 'componente', '1', 'Unidad de Imagen 501/ IM430');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('93', '5', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('94', '1', 'Entrega Refacción/Consumible', 'SER-03', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('95', '1', 'Servicio Correctivo', 'SER-02', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('96', '1', '139999', 'componente', '1', 'Elemento de Equipo');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('97', '1', '120978G+', 'componente', '1', 'Toner MPC6003 Cyan Ikon/CET+');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('99', '12', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('126', '2', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('128', '11', 'Servicio Correctivo', 'SER-02', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('129', '7', 'Servicio Preventivo', 'SER-01', '1', 'moino');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('132', '21', 'Servicio Preventivo', 'SER-01', '1', '');

-- Estructura de tabla: telefonos
DROP TABLE IF EXISTS `telefonos`;
CREATE TABLE `telefonos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(25) DEFAULT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `contacto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_cliente` (`id_cliente`),
  CONSTRAINT `telefonos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--Datos de tabla: telefonos
INSERT INTO `telefonos` (`id`, `telefono`, `id_cliente`, `contacto`) VALUES ('18', '4422133388', '1', '');
INSERT INTO `telefonos` (`id`, `telefono`, `id_cliente`, `contacto`) VALUES ('22', '4421532410', '2', '');
INSERT INTO `telefonos` (`id`, `telefono`, `id_cliente`, `contacto`) VALUES ('24', '4422389600', '3', '');
INSERT INTO `telefonos` (`id`, `telefono`, `id_cliente`, `contacto`) VALUES ('25', '4421338069', '6', '');
INSERT INTO `telefonos` (`id`, `telefono`, `id_cliente`, `contacto`) VALUES ('27', '4423948804', '4', '');
INSERT INTO `telefonos` (`id`, `telefono`, `id_cliente`, `contacto`) VALUES ('32', '4422531069', '7', '');
INSERT INTO `telefonos` (`id`, `telefono`, `id_cliente`, `contacto`) VALUES ('34', '4422199244', '5', '');

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
INSERT INTO `usuarios` (`id_usr`, `nom_usr`, `mail`, `pass`, `tip_usr`) VALUES ('1', 'a', 'Servicio tecnico', '1234', '1');
INSERT INTO `usuarios` (`id_usr`, `nom_usr`, `mail`, `pass`, `tip_usr`) VALUES ('2', 'b', 'Administracion', '1234', '2');
INSERT INTO `usuarios` (`id_usr`, `nom_usr`, `mail`, `pass`, `tip_usr`) VALUES ('3', 'c', 'yo', '1234', '1');

SET FOREIGN_KEY_CHECKS=1;
