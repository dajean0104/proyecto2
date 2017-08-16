/*
Navicat MariaDB Data Transfer

Source Server         : MariaDB
Source Server Version : 100207
Source Host           : localhost:3306
Source Database       : proyecto2

Target Server Type    : MariaDB
Target Server Version : 100207
File Encoding         : 65001

Date: 2017-08-16 14:23:18
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for abc
-- ----------------------------
DROP TABLE IF EXISTS `abc`;
CREATE TABLE `abc` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NombreProducto` varchar(255) NOT NULL,
  `FechaIngreso` date NOT NULL,
  `Existencia` int(255) NOT NULL,
  `Proveedor` int(255) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `ProvProd` (`Proveedor`),
  CONSTRAINT `ProvProd` FOREIGN KEY (`Proveedor`) REFERENCES `proveedores` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of abc
-- ----------------------------
INSERT INTO `abc` VALUES ('1', 'Doritos', '2017-08-08', '150', '6');
INSERT INTO `abc` VALUES ('2', 'Tortrix Taco', '2017-08-14', '500', '5');
INSERT INTO `abc` VALUES ('3', 'Coca Cola Litro', '2017-08-11', '600', '3');

-- ----------------------------
-- Table structure for proveedores
-- ----------------------------
DROP TABLE IF EXISTS `proveedores`;
CREATE TABLE `proveedores` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NombreProveedor` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of proveedores
-- ----------------------------
INSERT INTO `proveedores` VALUES ('1', 'Señorial');
INSERT INTO `proveedores` VALUES ('2', 'Cerveceria C.A');
INSERT INTO `proveedores` VALUES ('3', 'Coca Cola');
INSERT INTO `proveedores` VALUES ('4', 'Pepsi');
INSERT INTO `proveedores` VALUES ('5', 'Tortrix');
INSERT INTO `proveedores` VALUES ('6', 'Tortilla Chip');

-- ----------------------------
-- Table structure for usuarios
-- ----------------------------
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `IDUsuario` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) NOT NULL,
  `Contraseña` varchar(255) NOT NULL,
  PRIMARY KEY (`IDUsuario`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of usuarios
-- ----------------------------
INSERT INTO `usuarios` VALUES ('1', 'admin', 'Admin123#');
SET FOREIGN_KEY_CHECKS=1;
