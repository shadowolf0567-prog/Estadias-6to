-- Backup de Base de Datos Emipac
-- Fecha: 2026-07-22 17:02:25
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--Datos de tabla: clientes
INSERT INTO `clientes` (`id_cliente`, `nombre`, `no_cuenta`, `direccion`, `encargado`, `contrato`) VALUES ('1', 'Intelligence Berau and Laborator', '291901', 'Av. Antea #1032 Int. 404 Jurica', 'Roberto Alfaro', 'C-0399');
INSERT INTO `clientes` (`id_cliente`, `nombre`, `no_cuenta`, `direccion`, `encargado`, `contrato`) VALUES ('2', 'Nachi Technologies México', '287201', 'Tequisquiapan No.2 Galerias Aerotech Industrial Park Colón', 'Benito Sanchez', 'C-0133');
INSERT INTO `clientes` (`id_cliente`, `nombre`, `no_cuenta`, `direccion`, `encargado`, `contrato`) VALUES ('3', 'Industrial Powder Coatings Mex', '130701', 'Av. de la Noria No. 104 Parque Qro.', 'Norma Luna', '');
INSERT INTO `clientes` (`id_cliente`, `nombre`, `no_cuenta`, `direccion`, `encargado`, `contrato`) VALUES ('4', 'GNS Automotive México', '268501', 'Av. Ing. Antonio Gutierrez Cortina No. 14 Parque Opción SJI', 'Oscar Nazareth', '');
INSERT INTO `clientes` (`id_cliente`, `nombre`, `no_cuenta`, `direccion`, `encargado`, `contrato`) VALUES ('5', 'SEPSA SA de CV.', '232301', 'Espuela del Ferrocarril No. 204 Carrillo Puerto', 'Sarahí Bustamante', 'C-0318');
INSERT INTO `clientes` (`id_cliente`, `nombre`, `no_cuenta`, `direccion`, `encargado`, `contrato`) VALUES ('6', 'María Magdalena Mejía Ruíz', '172401', 'Puente de Alvarado No. 210 Carretas', 'Erika Gudiño', 'C-0080');
INSERT INTO `clientes` (`id_cliente`, `nombre`, `no_cuenta`, `direccion`, `encargado`, `contrato`) VALUES ('7', 'GW Plastics Mexicana S de RL', '294901', 'Circuito Marques #23A Parque IND El Marqués.', 'Mariana Martínez', 'C-0429');
INSERT INTO `clientes` (`id_cliente`, `nombre`, `no_cuenta`, `direccion`, `encargado`, `contrato`) VALUES ('12', 'Clarton Horn México', '271301', 'Av. de la Cruz 103 Col. Buenavista', 'Abigail Cabrera', 'C-0020');
INSERT INTO `clientes` (`id_cliente`, `nombre`, `no_cuenta`, `direccion`, `encargado`, `contrato`) VALUES ('13', 'Gerresheimer Querétaro', '230001', 'Av. Coahuila No. 9 Industrial Benito Juarez', 'Luis Culebro', 'C-0046');
INSERT INTO `clientes` (`id_cliente`, `nombre`, `no_cuenta`, `direccion`, `encargado`, `contrato`) VALUES ('14', 'Flex N Gate México S de RL de CV', '246401', 'Principal No. 1 Parque Industrial Opción SJI', 'Nathanael Torres', 'C-0213');

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
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('18', 'Y177HC01816', 'S-11402SPFR+', '12');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('20', 'C328R300429', 'S-11MP4055R+', '13');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('21', 'C299R200008', 'S-11MP2555R+', '13');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('22', '3350P301149', 'S-110430F', '13');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('23', 'W915PA03579', 'S-11301SPF', '13');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('24', 'W916P903286', 'S-11301SPF', '13');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('25', 'Y176HC00121', 'S-11402SPF', '13');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('26', 'Y178HA02593', 'S-11402SPF+', '13');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('27', 'G989X213198', 'S-11501SPF+', '13');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('28', 'S9058500168', 'S-115200R+', '13');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('29', 'G144RB00118', 'S-117008', '13');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('30', 'G145R600752', 'S-117008', '13');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('31', 'C767R610043', 'S-11C2004R+', '13');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('32', '3351P500840', 'S-11IM430R+', '13');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('33', 'C298R220838', 'S-11MP2555R+', '13');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('34', 'Y176HC00140', 'S-11402SPF', '13');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('35', 'C329R901156', 'S-11MP4055R+', '13');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('36', 'G716M510138', 'S-11MPC4504R+', '13');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('37', '3359PA00288', 'S-11IM430R+', '13');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('38', '3354PA51353', 'S-110430F', '13');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('39', '3354P450921', 'S-110430F', '13');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('40', '3354PA51351', 'S-110430F', '13');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('41', '3353PA50395', 'S-110430F', '13');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('42', '3354PA51348', 'S-110430F', '13');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('43', 'G737M160847', 'S-11MPC6004R+', '13');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('45', 'Y176HC00154', 'S-11402SPF', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('46', 'Y177H100762', 'S-11402SPF', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('47', 'Y177H100781', 'S-11402SPF', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('48', 'Y177HA01385', 'S-11402SPF', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('49', 'Y177HA02425', 'S-11402SPF', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('50', 'Y177HA02428', 'S-11402SPF', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('51', 'Y177HA02429', 'S-11402SPF', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('52', 'Y177HC01591', 'S-11402SPF', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('53', 'Y177HC01623', 'S-11402SPF', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('54', 'Y176H901864', 'S-11402SPFR+', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('55', 'Y176HA01102', 'S-11402SPFR+', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('56', 'Y176HC02439', 'S-11402SPFR+', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('57', 'Y177H101011', 'S-11402SPFR+', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('58', 'C768R311165', 'S-11C2004R', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('59', 'C307R600045', 'S-11MP3055R+', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('60', 'C508P301958', 'S-11MPC307R+', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('61', 'C508P301961', 'S-11MPC307R+', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('62', 'C508P800341', 'S-11MPC307R+', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('63', 'C738M640993', 'S-11MPC4504R', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('64', 'Y177H702834', 'S-1140SPFR+', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('65', 'Y176HA03560', 'S-11402SPFR+', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('66', 'C757M500164', 'S-11MPC6004R+', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('67', 'G736MA60670', 'S-11MPC6004R+', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('68', 'Y177HA02430', 'S-11402SPF+', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('69', 'Y178H401620', 'S-11402SPF+', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('70', 'Y177HB05160', 'S-11402SPF', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('71', 'C329R900039', 'S-11MP4055R+', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('72', 'C337R400681', 'S-11MP5055R+', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('73', '3140R100716', 'S-11IMC6000', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('74', 'C509PA01055', 'S-11MPC307R+', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('75', 'Y178H500108', 'S-11402SPRF+', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('76', 'C507PC04901', 'S-11307C', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('77', 'Y178HA01264', 'S-11402SPF', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('78', 'Y178HA01296', 'S-11402SPF', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('79', 'Y177HC01784', 'S-11402SPF', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('80', 'Y178H401959', 'S-11402SPFR+', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('81', 'Y177H901479', 'S-11402SPF+', '14');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `id_cliente`) VALUES ('82', 'Y177HC01647', 'S-1102SPF', '14');

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
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('24', '2026-07-17', '', 'atendido', '2026-07-17', '', '12', '18', '27131-0020-003');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('25', '2026-07-20', '', 'atendido', '2026-07-20', '', '13', '22', '230001-0046-0001');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('26', '2026-07-20', '', 'atendido', '2026-07-20', '', '13', '32', '230001-0046-0014');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('27', '2026-07-20', '', 'atendido', '2026-07-20', '', '13', '41', '230001-0046-0024');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('28', '2026-07-20', '', 'atendido', '2026-07-20', '', '13', '39', '230001-0046-0021');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('29', '2026-07-20', '', 'atendido', '2026-07-20', '', '13', '42', '230001-0046-0025');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('30', '2026-07-20', '', 'atendido', '2026-07-20', '', '13', '40', '230001-0046-0023');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('31', '2026-07-20', '', 'atendido', '2026-07-20', '', '13', '38', '230001-0046-0021');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('32', '2026-07-20', '', 'atendido', '2026-07-20', '', '13', '37', '230001-0046-0020');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('33', '2026-07-20', '', 'atendido', '2026-07-20', '', '13', '33', '230001-0046-0015');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('34', '2026-07-20', '', 'atendido', '2026-07-20', '', '13', '21', '230001-0046-0016');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('35', '2026-07-20', '', 'atendido', '2026-07-20', '', '13', '20', '230001-0046-0017');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('36', '2026-07-20', '', 'atendido', '2026-07-20', '', '13', '34', '230001-0046-0006');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('37', '2026-07-20', '', 'atendido', '2026-07-20', '', '13', '35', '230001-0046-0018');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('38', '2026-07-20', '', 'atendido', '2026-07-20', '', '13', '31', '230001-0046-0013');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('39', '2026-07-20', '', 'atendido', '2026-07-20', '', '13', '29', '230001-0046-0011');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('40', '2026-07-20', '', 'atendido', '2026-07-20', '', '13', '30', '230001-0046-0012');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('41', '2026-07-20', '', 'atendido', '2026-07-20', '', '13', '36', '230001-0046-0019');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('42', '2026-07-20', '', 'atendido', '2026-07-20', '', '13', '43', '230001-0046-0026');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('43', '2026-07-20', '', 'atendido', '2026-07-20', '', '13', '27', '230001-0046-0008');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('44', '2026-07-20', '', 'atendido', '2026-07-20', '', '13', '28', '230001-0046-0010');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('45', '2026-07-20', '', 'atendido', '2026-07-20', '', '13', '23', '230001-0046-0002');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('46', '2026-07-20', '', 'atendido', '2026-07-20', '', '13', '24', '23001-0046-0003');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('47', '2026-07-20', '', 'atendido', '2026-07-20', '', '13', '25', '230001-0046-0005');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('48', '2026-07-20', '', 'atendido', '2026-07-20', '', '13', '26', '230001-0046-0007');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('49', '2026-07-16', '', 'atendido', '2026-07-16', '', '13', '20', '230001-0046-0017');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('50', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '59', '246401-0213-0034');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('51', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '71', '246401-0213-0053');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('52', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '60', '246401-0213-0036');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('53', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '61', '246401-0213-0037');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('54', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '62', '246401-0213-0038');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('55', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '63', '246401-0213-0039');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('56', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '66', '246401-0213-0048');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('57', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '58', '246401-0213-0033');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('58', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '67', '246401-0213-0049');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('59', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '54', '246401-0213-0027');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('60', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '55', '246401-0213-0028');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('61', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '65', '246401-0213-0047');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('62', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '45', '246401-0213-0010');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('63', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '56', '246401-0213-0029');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('64', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '46', '246401-0213-0011');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('65', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '47', '246401-0213-0012');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('66', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '57', '246401-0213-0030');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('67', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '64', '246401-0213-0045');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('68', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '48', '246401-0213-0016');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('69', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '49', '246401-0213-0019');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('70', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '50', '246401-0213-0020');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('71', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '51', '246401-0213-0021');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('72', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '68', '246401-0213-0050');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('73', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '70', '246401-0213-0052');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('74', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '52', '246401-0213-0025');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('75', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '53', '246401-0213-0026');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('76', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '69', '246401-0213-0051');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('77', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '72', '246401-0213-0056');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('78', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '73', '246401-0213-0057');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('79', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '74', '246401-0213-0058');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('80', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '75', '246401-0213-0059');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('81', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '76', '246401-0213-0060');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('82', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '77', '246401-0213-0061');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('83', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '78', '246401-0213-0063');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('84', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '79', '246401-0213-0064');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('85', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '80', '246401-0213-0065');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('86', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '81', '246401-0213-0066');
INSERT INTO `reportes` (`id_reporte`, `fecha`, `tecnico`, `estado`, `fecha_atencion`, `observaciones_atencion`, `id_cliente`, `id_equipo`, `referencia`) VALUES ('87', '2026-07-21', '', 'atendido', '2026-07-21', '', '14', '82', '246401-0213-0067');

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
) ENGINE=InnoDB AUTO_INCREMENT=284 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--Datos de tabla: reportes_componentes
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('84', '13', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('90', '4', 'Servicio Correctivo', 'SER-02', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('91', '4', '130948', 'componente', '1', 'Unidad de Imagen 501/ IM430');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('93', '5', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('99', '12', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('126', '2', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('128', '11', 'Servicio Correctivo', 'SER-02', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('132', '21', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('133', '7', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('145', '1', 'Entrega Refacción/Consumible', 'SER-03', '1', 'Elemento de Equipo');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('146', '1', 'Servicio Correctivo', 'SER-02', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('147', '1', '120978G+', 'componente', '1', 'Toner MPC6003 Cyan Ikon/CET+');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('148', '24', 'Servicio Correctivo', 'SER-02', '18', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('149', '24', '131224', 'componente', '1', 'Fotoconductora MP402 SP4500/4510');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('175', '25', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('176', '48', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('177', '47', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('178', '46', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('179', '45', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('180', '44', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('181', '43', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('182', '42', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('183', '41', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('184', '40', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('185', '39', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('186', '38', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('187', '37', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('188', '36', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('189', '35', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('190', '34', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('191', '33', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('192', '32', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('193', '31', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('194', '30', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('195', '29', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('196', '28', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('197', '27', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('198', '26', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('199', '49', 'Servicio Correctivo', 'SER-02', '18', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('200', '49', '133049', 'componente', '1', 'Switch Thermistor MPC3503');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('240', '50', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('241', '87', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('242', '86', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('243', '85', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('244', '84', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('246', '82', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('247', '81', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('248', '80', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('249', '79', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('250', '78', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('251', '77', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('254', '74', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('255', '75', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('256', '76', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('259', '71', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('260', '70', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('261', '69', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('262', '68', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('263', '67', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('264', '66', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('265', '65', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('266', '64', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('267', '63', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('268', '62', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('269', '61', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('270', '60', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('271', '59', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('272', '58', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('273', '57', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('274', '56', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('275', '55', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('276', '54', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('277', '53', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('278', '52', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('279', '51', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('280', '83', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('281', '73', 'Servicio Preventivo', 'SER-01', '1', '');
INSERT INTO `reportes_componentes` (`id_reporte_componente`, `id_reporte`, `componente`, `tipo`, `cantidad`, `descripcion`) VALUES ('282', '72', 'Servicio Preventivo', 'SER-01', '1', '');

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
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--Datos de tabla: telefonos
INSERT INTO `telefonos` (`id`, `telefono`, `id_cliente`, `contacto`) VALUES ('18', '4422133388', '1', '');
INSERT INTO `telefonos` (`id`, `telefono`, `id_cliente`, `contacto`) VALUES ('22', '4421532410', '2', '');
INSERT INTO `telefonos` (`id`, `telefono`, `id_cliente`, `contacto`) VALUES ('24', '4422389600', '3', '');
INSERT INTO `telefonos` (`id`, `telefono`, `id_cliente`, `contacto`) VALUES ('25', '4421338069', '6', '');
INSERT INTO `telefonos` (`id`, `telefono`, `id_cliente`, `contacto`) VALUES ('27', '4423948804', '4', '');
INSERT INTO `telefonos` (`id`, `telefono`, `id_cliente`, `contacto`) VALUES ('32', '4422531069', '7', '');
INSERT INTO `telefonos` (`id`, `telefono`, `id_cliente`, `contacto`) VALUES ('34', '4422199244', '5', '');
INSERT INTO `telefonos` (`id`, `telefono`, `id_cliente`, `contacto`) VALUES ('35', '4421787636', '12', '');
INSERT INTO `telefonos` (`id`, `telefono`, `id_cliente`, `contacto`) VALUES ('36', '4422052215', '13', '');
INSERT INTO `telefonos` (`id`, `telefono`, `id_cliente`, `contacto`) VALUES ('37', '4422744952', '14', '');

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
