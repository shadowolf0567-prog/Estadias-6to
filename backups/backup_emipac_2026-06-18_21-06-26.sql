-- Backup de Base de Datos Emipac
-- Fecha: 2026-06-18 21:06:26
DROP DATABASE IF EXISTS emipac;
CREATE DATABASE emipac;
use emipac; 
SET FOREIGN_KEY_CHECKS=0;

-- Estructura de tabla: clientes
DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `no_cuenta` varchar(100) NOT NULL,
  `telefono` int(11) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--Datos de tabla: clientes
INSERT INTO `clientes` (`id_cliente`, `nombre`, `no_cuenta`, `telefono`, `correo`, `direccion`) VALUES ('8', 'pedro', '11111', '2147483647', 'p@mail.com', 'lojkhijuiyv');
INSERT INTO `clientes` (`id_cliente`, `nombre`, `no_cuenta`, `telefono`, `correo`, `direccion`) VALUES ('10', 'ju', '666', '88', 'gg', 'uytyy');
INSERT INTO `clientes` (`id_cliente`, `nombre`, `no_cuenta`, `telefono`, `correo`, `direccion`) VALUES ('14', 'ch', '2', '987', 'a', 'si');
INSERT INTO `clientes` (`id_cliente`, `nombre`, `no_cuenta`, `telefono`, `correo`, `direccion`) VALUES ('15', 'piojo', '2', '23323', 'piojo@mail.com', 'su casa');
INSERT INTO `clientes` (`id_cliente`, `nombre`, `no_cuenta`, `telefono`, `correo`, `direccion`) VALUES ('27', 'antonio', '0987', '124324', 'a', 'uvgu');
INSERT INTO `clientes` (`id_cliente`, `nombre`, `no_cuenta`, `telefono`, `correo`, `direccion`) VALUES ('40', 'Hospital General de Quer?taro', '12324', '88888', 'fcharbelnava12@gmail.com', 'Marqu?s de Miraflores 462, Fraccionamiento Real del Marqu?s, 76118 Santiago de Quer?taro, Qro.');

-- Estructura de tabla: equipos
DROP TABLE IF EXISTS `equipos`;
CREATE TABLE `equipos` (
  `id_equipo` int(11) NOT NULL AUTO_INCREMENT,
  `no_serie` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `accesorios` varchar(255) DEFAULT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `inicio_contrato` date DEFAULT NULL,
  `fin_contrato` date DEFAULT NULL,
  PRIMARY KEY (`id_equipo`),
  KEY `id_cliente` (`id_cliente`),
  CONSTRAINT `equipos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--Datos de tabla: equipos
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `accesorios`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('22', '987', 'j', '8888', '10', NULL, NULL);
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `accesorios`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('24', '987878', 'mo', 'jjj', '8', NULL, NULL);
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `accesorios`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('25', '987899', 'mo', 'jjj', '8', NULL, NULL);
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `accesorios`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('26', '123456', 'n ,j', 'kjnb', '10', NULL, NULL);
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `accesorios`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('28', '5555', '9', 'muchos', '15', NULL, NULL);
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `accesorios`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('29', 'ml k', 'm0ikn', 'on', '14', NULL, NULL);
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `accesorios`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('38', '123456', 'n ,j', 'm', '14', NULL, NULL);
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `accesorios`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('39', '123456', 'n ,j', 'm', '10', NULL, NULL);
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `accesorios`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('43', '987', 'dcvbn', 'kjhgfv', '27', NULL, NULL);
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `accesorios`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('45', '987', 'dcvbn', 'kjhgfv', '8', NULL, NULL);
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `accesorios`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('46', 'b?n', 'ty', '', '27', NULL, NULL);
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `accesorios`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('47', '987', '234t', 'kjnb', '8', '2026-06-23', '2026-07-02');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `accesorios`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('48', '987', 'dcvbn', 'kjhgfv', '40', '2026-06-15', '2026-06-29');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `accesorios`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('49', '123456', 'n ,j', '', '27', '2026-06-13', '2026-06-20');
INSERT INTO `equipos` (`id_equipo`, `no_serie`, `modelo`, `accesorios`, `id_cliente`, `inicio_contrato`, `fin_contrato`) VALUES ('50', '6789', 'mole', '', '8', '2026-06-18', '2026-07-18');

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
  `contacto_cliente` varchar(100) DEFAULT NULL,
  `telefono_cliente` varchar(20) DEFAULT NULL,
  `acciones` text DEFAULT NULL,
  PRIMARY KEY (`id_reporte`),
  KEY `idx_cliente` (`id_cliente`),
  KEY `idx_equipo` (`id_equipo`),
  CONSTRAINT `reportes_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE SET NULL,
  CONSTRAINT `reportes_ibfk_2` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id_equipo`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--Datos de tabla: reportes
INSERT INTO `reportes` (`id_reporte`, `reporte`, `fecha`, `tecnico`, `refaccion`, `estado`, `fecha_atencion`, `observaciones_atencion`, `descripcion`, `id_cliente`, `id_equipo`, `contacto_cliente`, `telefono_cliente`, `acciones`) VALUES ('2', 'si', '2026-05-20', 'k', 'p', 'atendido', '2026-05-26', 'pknpk', 'no', '8', NULL, '', '', NULL);
INSERT INTO `reportes` (`id_reporte`, `reporte`, `fecha`, `tecnico`, `refaccion`, `estado`, `fecha_atencion`, `observaciones_atencion`, `descripcion`, `id_cliente`, `id_equipo`, `contacto_cliente`, `telefono_cliente`, `acciones`) VALUES ('3', '', '0000-00-00', '', '', 'pendiente', NULL, NULL, '', '10', NULL, 'juan', '', NULL);
INSERT INTO `reportes` (`id_reporte`, `reporte`, `fecha`, `tecnico`, `refaccion`, `estado`, `fecha_atencion`, `observaciones_atencion`, `descripcion`, `id_cliente`, `id_equipo`, `contacto_cliente`, `telefono_cliente`, `acciones`) VALUES ('4', 'lknpubnj', '2026-05-25', '', '', 'atendido', '2026-05-28', 'lj lj', '0kkl', '10', NULL, '', '', NULL);
INSERT INTO `reportes` (`id_reporte`, `reporte`, `fecha`, `tecnico`, `refaccion`, `estado`, `fecha_atencion`, `observaciones_atencion`, `descripcion`, `id_cliente`, `id_equipo`, `contacto_cliente`, `telefono_cliente`, `acciones`) VALUES ('5', 'okhvylou', '2026-05-26', '', '', 'atendido', '2026-05-27', '', '0,m', NULL, NULL, 'lj l', '', NULL);
INSERT INTO `reportes` (`id_reporte`, `reporte`, `fecha`, `tecnico`, `refaccion`, `estado`, `fecha_atencion`, `observaciones_atencion`, `descripcion`, `id_cliente`, `id_equipo`, `contacto_cliente`, `telefono_cliente`, `acciones`) VALUES ('6', '', '2026-05-28', 'nklbjhk', '', 'atendido', '2026-05-28', '.m .m', 'k,n.bjlkhvgjvh', '10', NULL, '', '', NULL);
INSERT INTO `reportes` (`id_reporte`, `reporte`, `fecha`, `tecnico`, `refaccion`, `estado`, `fecha_atencion`, `observaciones_atencion`, `descripcion`, `id_cliente`, `id_equipo`, `contacto_cliente`, `telefono_cliente`, `acciones`) VALUES ('7', 'jn lojn', '2026-05-29', '', '', 'pendiente', NULL, NULL, '0', '10', NULL, 'iyclllkk', '', NULL);
INSERT INTO `reportes` (`id_reporte`, `reporte`, `fecha`, `tecnico`, `refaccion`, `estado`, `fecha_atencion`, `observaciones_atencion`, `descripcion`, `id_cliente`, `id_equipo`, `contacto_cliente`, `telefono_cliente`, `acciones`) VALUES ('8', 'si', '2026-06-05', '', '', 'pendiente', NULL, NULL, 'iigy', NULL, NULL, '', '', NULL);
INSERT INTO `reportes` (`id_reporte`, `reporte`, `fecha`, `tecnico`, `refaccion`, `estado`, `fecha_atencion`, `observaciones_atencion`, `descripcion`, `id_cliente`, `id_equipo`, `contacto_cliente`, `telefono_cliente`, `acciones`) VALUES ('9', 'pu', '2026-06-04', 'kjh', '7ujn', 'atendido', '2026-06-15', 'jgb bik', '0km', '8', NULL, '', '2147483647', NULL);
INSERT INTO `reportes` (`id_reporte`, `reporte`, `fecha`, `tecnico`, `refaccion`, `estado`, `fecha_atencion`, `observaciones_atencion`, `descripcion`, `id_cliente`, `id_equipo`, `contacto_cliente`, `telefono_cliente`, `acciones`) VALUES ('10', 'si', '2026-06-05', '', '', 'atendido', '2026-06-10', '', '0', '27', NULL, '', '124324', NULL);
INSERT INTO `reportes` (`id_reporte`, `reporte`, `fecha`, `tecnico`, `refaccion`, `estado`, `fecha_atencion`, `observaciones_atencion`, `descripcion`, `id_cliente`, `id_equipo`, `contacto_cliente`, `telefono_cliente`, `acciones`) VALUES ('11', 'si', '2026-06-05', '', '', 'atendido', '2026-06-10', '', 'iigy', '8', NULL, '', '', NULL);
INSERT INTO `reportes` (`id_reporte`, `reporte`, `fecha`, `tecnico`, `refaccion`, `estado`, `fecha_atencion`, `observaciones_atencion`, `descripcion`, `id_cliente`, `id_equipo`, `contacto_cliente`, `telefono_cliente`, `acciones`) VALUES ('15', 'kjnb', '2026-06-09', '', '', 'atendido', '2026-06-10', 'mucho', 'ojbulj,dosk', '8', NULL, '', '', NULL);
INSERT INTO `reportes` (`id_reporte`, `reporte`, `fecha`, `tecnico`, `refaccion`, `estado`, `fecha_atencion`, `observaciones_atencion`, `descripcion`, `id_cliente`, `id_equipo`, `contacto_cliente`, `telefono_cliente`, `acciones`) VALUES ('16', 'kjnb', '2026-06-09', 'e', '2', 'atendido', '2026-06-10', '', 'lib', NULL, NULL, '', '', NULL);
INSERT INTO `reportes` (`id_reporte`, `reporte`, `fecha`, `tecnico`, `refaccion`, `estado`, `fecha_atencion`, `observaciones_atencion`, `descripcion`, `id_cliente`, `id_equipo`, `contacto_cliente`, `telefono_cliente`, `acciones`) VALUES ('17', 'n bpo', '2026-06-10', 'mp?', 'yfhc', 'atendido', '2026-06-15', 'kihvyi', 'jvbohk', '8', NULL, '', '', NULL);
INSERT INTO `reportes` (`id_reporte`, `reporte`, `fecha`, `tecnico`, `refaccion`, `estado`, `fecha_atencion`, `observaciones_atencion`, `descripcion`, `id_cliente`, `id_equipo`, `contacto_cliente`, `telefono_cliente`, `acciones`) VALUES ('18', 'lj', '2026-06-16', '', '', 'pendiente', NULL, '', '', '8', NULL, '', '', NULL);
INSERT INTO `reportes` (`id_reporte`, `reporte`, `fecha`, `tecnico`, `refaccion`, `estado`, `fecha_atencion`, `observaciones_atencion`, `descripcion`, `id_cliente`, `id_equipo`, `contacto_cliente`, `telefono_cliente`, `acciones`) VALUES ('19', '?', '2026-06-17', '', '', 'pendiente', NULL, '', '?p', '27', '49', '', '124324', NULL);

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
