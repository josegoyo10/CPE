-- MySQL dump 10.10
--
-- Host: localhost    Database: cpprod
-- ------------------------------------------------------
-- Server version	5.0.22

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `OrigenUsr`
--

DROP TABLE IF EXISTS `OrigenUsr`;
CREATE TABLE `OrigenUsr` (
  `id_origen` int(10) unsigned NOT NULL auto_increment,
  `nom_origen` varchar(45) NOT NULL default '',
  PRIMARY KEY  (`id_origen`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `archivos`
--

DROP TABLE IF EXISTS `archivos`;
CREATE TABLE `archivos` (
  `prefijo` varchar(20) collate latin1_spanish_ci NOT NULL default '',
  `tipo` char(3) collate latin1_spanish_ci default NULL,
  `id_cadena` tinyint(3) unsigned default NULL,
  PRIMARY KEY  (`prefijo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `arreglos_florales`
--

DROP TABLE IF EXISTS `arreglos_florales`;
CREATE TABLE `arreglos_florales` (
  `id_arr_flo` int(11) NOT NULL auto_increment,
  `id_os` bigint(20) NOT NULL,
  `arr_flo_nombre` varchar(255) default NULL,
  `arr_flo_direccion` varchar(255) NOT NULL,
  `arr_flo_localizacion` bigint(20) NOT NULL,
  `arr_flo_mensajededicatoria` varchar(5000) default NULL,
  `arr_flo_observaciones` varchar(2000) default NULL,
  `arr_flo_fechaentrega` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id_arr_flo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `calidad_tributaria`
--

DROP TABLE IF EXISTS `calidad_tributaria`;
CREATE TABLE `calidad_tributaria` (
  `id_cal_tri` int(11) NOT NULL auto_increment,
  `nombre_cal_tri` varchar(30) default NULL,
  `abreviatura_cal_tri` varchar(5) default NULL,
  PRIMARY KEY  (`id_cal_tri`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `cambiosestado`
--

DROP TABLE IF EXISTS `cambiosestado`;
CREATE TABLE `cambiosestado` (
  `id_estado_destino` char(2) collate latin1_spanish_ci NOT NULL default '',
  `id_estado_origen` char(2) collate latin1_spanish_ci NOT NULL default '',
  `esta_tipo` char(2) collate latin1_spanish_ci NOT NULL default '',
  `orden` tinyint(3) unsigned default NULL,
  `desc_transicion` varchar(45) collate latin1_spanish_ci default NULL,
  `flujo` smallint(5) unsigned default NULL,
  PRIMARY KEY  (`id_estado_destino`,`id_estado_origen`,`esta_tipo`),
  KEY `cambiosestado_FKIndex1` (`id_estado_origen`),
  KEY `cambiosestado_FKIndex2` (`id_estado_destino`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `car_catprod`
--

DROP TABLE IF EXISTS `car_catprod`;
CREATE TABLE `car_catprod` (
  `cod_cat` varchar(10) collate latin1_spanish_ci default NULL,
  `cod_cat_padre` varchar(10) collate latin1_spanish_ci default NULL,
  `descat` varchar(100) collate latin1_spanish_ci default NULL,
  `cat_nivel` int(1) unsigned default NULL,
  `cat_tipo` char(1) collate latin1_spanish_ci default NULL,
  `idcarga` decimal(20,0) default NULL,
  `id_cadena` tinyint(3) unsigned default NULL,
  KEY `car_catprod_index8477` (`idcarga`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `car_codbarra`
--

DROP TABLE IF EXISTS `car_codbarra`;
CREATE TABLE `car_codbarra` (
  `cod_prod1` varchar(20) collate latin1_spanish_ci default NULL,
  `cod_barra` varchar(14) collate latin1_spanish_ci default NULL,
  `tip_codbar` char(3) collate latin1_spanish_ci default NULL,
  `cod_ppal` char(1) collate latin1_spanish_ci default NULL,
  `unid_med` char(3) collate latin1_spanish_ci default NULL,
  `idcarga` decimal(20,0) default NULL,
  `id_cadena` tinyint(3) unsigned default NULL,
  `factor_conv` double default NULL,
  `fecultact` datetime default NULL,
  `estadoactivo` char(1) collate latin1_spanish_ci default NULL,
  KEY `car_codbarra_index8481` (`idcarga`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `car_cost`
--

DROP TABLE IF EXISTS `car_cost`;
CREATE TABLE `car_cost` (
  `cod_local` varchar(20) character set latin1 collate latin1_spanish_ci default NULL,
  `cod_prod1` varchar(20) character set latin1 collate latin1_spanish_ci default NULL,
  `prec_costo` decimal(14,2) default NULL,
  `id_carga` decimal(20,0) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

--
-- Table structure for table `car_estadocarga`
--

DROP TABLE IF EXISTS `car_estadocarga`;
CREATE TABLE `car_estadocarga` (
  `idcarga` int(10) unsigned NOT NULL auto_increment,
  `fechacarga` datetime default NULL,
  `estado` char(1) collate latin1_spanish_ci default NULL,
  `fechafincarga` datetime default NULL,
  PRIMARY KEY  (`idcarga`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `car_inventario`
--

DROP TABLE IF EXISTS `car_inventario`;
CREATE TABLE `car_inventario` (
  `cod_prod1` varchar(20) collate latin1_spanish_ci NOT NULL default '',
  `cod_local` varchar(20) collate latin1_spanish_ci default NULL,
  `prec_valor` varchar(20) collate latin1_spanish_ci default NULL,
  `prec_costo` varchar(20) collate latin1_spanish_ci default NULL,
  `idcarga` decimal(20,0) default NULL,
  `id_cadena` int(10) unsigned default NULL,
  `stock` varchar(20) collate latin1_spanish_ci default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `car_precios`
--

DROP TABLE IF EXISTS `car_precios`;
CREATE TABLE `car_precios` (
  `cod_local` varchar(20) collate latin1_spanish_ci NOT NULL default '',
  `cod_prod1` varchar(20) collate latin1_spanish_ci default NULL,
  `prec_valor` decimal(10,0) default NULL,
  `id_carga` decimal(20,0) default NULL,
  `id_cadena` int(10) unsigned default NULL,
  `id_orden` int(10) unsigned default NULL,
  `prec_costo` decimal(14,2) default NULL,
  `stock` decimal(10,0) default NULL,
  `iva` float(10,4) default NULL,
  `reteica` float(10,4) default NULL,
  `retefuente` float(10,4) default NULL,
  `reteiva` float(10,4) default NULL,
  `estadoactivo` char(1) collate latin1_spanish_ci default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `car_productos`
--

DROP TABLE IF EXISTS `car_productos`;
CREATE TABLE `car_productos` (
  `cod_categ` varchar(10) collate latin1_spanish_ci NOT NULL default '',
  `cod_prod1` varchar(20) collate latin1_spanish_ci default NULL,
  `cod_prod2` varchar(20) collate latin1_spanish_ci default NULL,
  `des_corta` varchar(40) collate latin1_spanish_ci default NULL,
  `des_larga` varchar(200) collate latin1_spanish_ci default NULL,
  `estado` int(1) unsigned default NULL,
  `cod_propal` varchar(20) collate latin1_spanish_ci default NULL,
  `prod_tipo` char(2) collate latin1_spanish_ci default NULL,
  `prod_subtipo` char(2) collate latin1_spanish_ci default NULL,
  `stock_proveedor` decimal(10,0) default NULL,
  `idcarga` decimal(20,0) default NULL,
  `id_cadena` tinyint(3) unsigned default NULL,
  `peso` float(13,3) default NULL,
  `volumen` float(13,3) default NULL,
  KEY `car_productos_index8475` (`idcarga`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `car_prodxprov`
--

DROP TABLE IF EXISTS `car_prodxprov`;
CREATE TABLE `car_prodxprov` (
  `cod_prod1` varchar(20) collate latin1_spanish_ci default NULL,
  `cod_prov` varchar(20) collate latin1_spanish_ci default NULL,
  `idcarga` decimal(20,0) default NULL,
  `id_cadena` tinyint(3) unsigned default NULL,
  KEY `car_prodxprov_index8483` (`idcarga`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `car_proveedores`
--

DROP TABLE IF EXISTS `car_proveedores`;
CREATE TABLE `car_proveedores` (
  `codprov` varchar(20) collate latin1_spanish_ci NOT NULL default '',
  `rutprov` varchar(20) collate latin1_spanish_ci NOT NULL,
  `nomprov` varchar(60) collate latin1_spanish_ci default NULL,
  `razsoc` varchar(60) collate latin1_spanish_ci default NULL,
  `fonocto` varchar(20) collate latin1_spanish_ci default NULL,
  `nombcto` varchar(50) collate latin1_spanish_ci default NULL,
  `emailcto` varchar(30) collate latin1_spanish_ci default NULL,
  `idcarga` decimal(20,0) default NULL,
  KEY `car_proveedores_index8479` (`idcarga`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `catprod`
--

DROP TABLE IF EXISTS `catprod`;
CREATE TABLE `catprod` (
  `id_catprod` varchar(20) collate latin1_spanish_ci NOT NULL default '',
  `id_cadena` int(10) unsigned NOT NULL default '0',
  `id_cadpadre` int(10) unsigned default NULL,
  `id_catpadre` varchar(20) collate latin1_spanish_ci default NULL,
  `descat` varchar(255) collate latin1_spanish_ci default NULL,
  `cat_nivel` tinyint(3) unsigned default NULL,
  `cat_tipo` char(1) collate latin1_spanish_ci default NULL,
  `estadoactivo` char(1) collate latin1_spanish_ci default NULL,
  PRIMARY KEY  (`id_catprod`,`id_cadena`),
  KEY `catprod_FKIndex1` (`id_catpadre`,`id_cadpadre`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `cfg_cp_dd`
--

DROP TABLE IF EXISTS `cfg_cp_dd`;
CREATE TABLE `cfg_cp_dd` (
  `variable` varchar(50) NOT NULL default '',
  `valor` varchar(45) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `ciudad`
--

DROP TABLE IF EXISTS `ciudad`;
CREATE TABLE `ciudad` (
  `id_departamento` int(11) default NULL,
  `id_ciudad` int(11) NOT NULL,
  `nombre_ciudad` varchar(60) NOT NULL,
  PRIMARY KEY  (`id_ciudad`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
  `clie_rut` bigint(18) unsigned NOT NULL,
  `clie_tipo` char(1) character set latin1 collate latin1_spanish_ci NOT NULL default '',
  `clie_nombre` varchar(45) character set latin1 collate latin1_spanish_ci default NULL,
  `clie_paterno` varchar(45) character set latin1 collate latin1_spanish_ci default NULL,
  `clie_materno` varchar(45) character set latin1 collate latin1_spanish_ci default NULL,
  `clie_telefonocasa` varchar(20) character set latin1 collate latin1_spanish_ci default NULL,
  `clie_telcontacto1` varchar(20) character set latin1 collate latin1_spanish_ci default NULL,
  `clie_telcontacto2` varchar(20) character set latin1 collate latin1_spanish_ci default NULL,
  `clie_observacion` varchar(255) character set latin1 collate latin1_spanish_ci default NULL,
  `clie_activo` tinyint(1) default NULL,
  `clie_nombrecomercial` varchar(20) character set latin1 collate latin1_spanish_ci default NULL,
  `clie_rutcontacto` varchar(20) character set latin1 collate latin1_spanish_ci default NULL,
  `clie_razonsocial` varchar(20) character set latin1 collate latin1_spanish_ci default NULL,
  `clie_giro` varchar(50) character set latin1 collate latin1_spanish_ci default NULL,
  `clie_email` varchar(50) default NULL,
  `clie_localizacion` bigint(18) default NULL,
  `clie_departamento` int(11) default NULL,
  `clie_provincia` int(11) default NULL,
  `clie_ciudad` int(11) default NULL,
  `clie_localidad` int(11) default NULL,
  `clie_barrio` int(10) default NULL,
  `clie_sexo` int(11) default NULL,
  `clie_tipo_cliente` int(11) default NULL,
  `clie_categoria_cliente` int(11) default NULL,
  `clie_tipo_contribuyente` varchar(5) default NULL,
  `clie_reteica` int(2) default NULL,
  `clie_fuente` int(2) default NULL,
  `clie_reteiva` int(2) default NULL,
  `id_tipo_doc` int(11) default NULL,
  PRIMARY KEY  (`clie_rut`),
  KEY `clie_index1` (`clie_nombre`),
  KEY `clie_index2` (`clie_paterno`),
  KEY `clie_index3` (`clie_materno`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `codbarra`
--

DROP TABLE IF EXISTS `codbarra`;
CREATE TABLE `codbarra` (
  `cod_barra` varchar(13) collate latin1_spanish_ci NOT NULL default '',
  `tip_codbar` char(3) collate latin1_spanish_ci NOT NULL default '',
  `id_producto` int(10) unsigned NOT NULL default '0',
  `cod_prod1` varchar(20) collate latin1_spanish_ci NOT NULL,
  `factor_conv` double default NULL,
  `cod_ppal` char(1) collate latin1_spanish_ci default NULL,
  `unid_med` char(3) collate latin1_spanish_ci default NULL,
  `estadoactivo` char(1) collate latin1_spanish_ci default NULL,
  `fecultact` datetime default NULL,
  PRIMARY KEY  USING BTREE (`cod_barra`,`tip_codbar`,`cod_prod1`),
  KEY `codigos_barra_FKIndex1` (`id_producto`),
  KEY `codbarra_index10268` (`cod_prod1`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `comuna`
--

DROP TABLE IF EXISTS `comuna`;
CREATE TABLE `comuna` (
  `id_comuna` int(11) NOT NULL,
  `id_region` int(11) NOT NULL,
  `comu_nombre` varchar(255) NOT NULL,
  `id_localidad` int(11) default NULL,
  PRIMARY KEY  (`id_comuna`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `ctacte_instalador`
--

DROP TABLE IF EXISTS `ctacte_instalador`;
CREATE TABLE `ctacte_instalador` (
  `id_linea` int(10) unsigned NOT NULL auto_increment,
  `id_instalador` int(10) unsigned default NULL,
  `fechatrx` datetime NOT NULL default '0000-00-00 00:00:00',
  `monto` int(10) unsigned NOT NULL default '0',
  `descripcion` varchar(255) default NULL,
  `id_lote` int(10) unsigned default NULL,
  `usuario` varchar(255) NOT NULL default '',
  `USR_ID` int(8) NOT NULL default '0',
  PRIMARY KEY  (`id_linea`),
  KEY `ctacte_instalador_FKIndex1` (`id_instalador`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `cu_city`
--

DROP TABLE IF EXISTS `cu_city`;
CREATE TABLE `cu_city` (
  `ID` decimal(10,0) NOT NULL default '0',
  `ID_PROVINCE` decimal(10,0) NOT NULL default '0',
  `ID_DEPARTMENT` decimal(10,0) NOT NULL default '0',
  `DESCRIPTION` varchar(100) NOT NULL,
  PRIMARY KEY  (`ID`,`ID_PROVINCE`,`ID_DEPARTMENT`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `cu_department`
--

DROP TABLE IF EXISTS `cu_department`;
CREATE TABLE `cu_department` (
  `ID` decimal(10,0) NOT NULL,
  `DESCRIPTION` varchar(100) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `cu_locality`
--

DROP TABLE IF EXISTS `cu_locality`;
CREATE TABLE `cu_locality` (
  `ID` decimal(10,0) NOT NULL,
  `ID_DEPARTMENT` decimal(10,0) NOT NULL,
  `ID_PROVINCE` decimal(10,0) NOT NULL,
  `ID_CITY` decimal(10,0) NOT NULL,
  `DESCRIPTION` varchar(100) NOT NULL,
  PRIMARY KEY  (`ID`,`ID_DEPARTMENT`,`ID_PROVINCE`,`ID_CITY`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `cu_neighborhood`
--

DROP TABLE IF EXISTS `cu_neighborhood`;
CREATE TABLE `cu_neighborhood` (
  `ID` decimal(10,0) NOT NULL,
  `ID_DEPARTMENT` decimal(10,0) NOT NULL,
  `ID_PROVINCE` decimal(10,0) NOT NULL,
  `ID_CITY` decimal(10,0) NOT NULL,
  `ID_LOCALITY` decimal(10,0) NOT NULL,
  `DESCRIPTION` varchar(100) NOT NULL,
  `SOCIAL_LEVEL` varchar(1) NOT NULL,
  `LOCATION` decimal(20,0) NOT NULL,
  PRIMARY KEY  (`ID`,`ID_DEPARTMENT`,`ID_PROVINCE`,`ID_CITY`,`ID_LOCALITY`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `cu_province`
--

DROP TABLE IF EXISTS `cu_province`;
CREATE TABLE `cu_province` (
  `ID` decimal(10,0) NOT NULL,
  `ID_DEPARTMENT` decimal(10,0) NOT NULL,
  `DESCRIPTION` varchar(50) NOT NULL,
  PRIMARY KEY  (`ID`,`ID_DEPARTMENT`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `dbdesigner4`
--

DROP TABLE IF EXISTS `dbdesigner4`;
CREATE TABLE `dbdesigner4` (
  `idmodel` int(10) unsigned NOT NULL default '0',
  `idversion` int(10) unsigned NOT NULL default '0',
  `name` varchar(45) collate latin1_spanish_ci default NULL,
  `version` varchar(20) collate latin1_spanish_ci default NULL,
  `username` varchar(45) collate latin1_spanish_ci default NULL,
  `createdate` datetime default NULL,
  `iscurrent` int(1) unsigned default NULL,
  `ischeckedout` int(1) unsigned default NULL,
  `info` varchar(255) collate latin1_spanish_ci default NULL,
  `model` mediumtext collate latin1_spanish_ci,
  PRIMARY KEY  (`idmodel`,`idversion`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `departamento`
--

DROP TABLE IF EXISTS `departamento`;
CREATE TABLE `departamento` (
  `id_departamento` bigint(20) NOT NULL,
  `nombre_departamento` varchar(60) NOT NULL,
  PRIMARY KEY  (`id_departamento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `direcciones`
--

DROP TABLE IF EXISTS `direcciones`;
CREATE TABLE `direcciones` (
  `id_direccion` int(10) unsigned NOT NULL auto_increment,
  `dire_localizacion` bigint(18) default NULL,
  `id_departamento` int(11) default NULL,
  `id_provincia` int(11) default NULL,
  `id_ciudad` int(11) default NULL,
  `id_localidad` int(11) default NULL,
  `id_comuna` int(10) unsigned NOT NULL default '0',
  `clie_rut` bigint(18) unsigned NOT NULL default '0',
  `dire_nombre` varchar(20) character set latin1 collate latin1_spanish_ci default NULL,
  `dire_direccion` varchar(255) character set latin1 collate latin1_spanish_ci NOT NULL default '',
  `dire_telefono` int(11) default NULL,
  `dire_observacion` varchar(255) character set latin1 collate latin1_spanish_ci default NULL,
  `dire_activo` tinyint(1) default NULL,
  `dire_defecto` char(1) character set latin1 collate latin1_spanish_ci default NULL,
  PRIMARY KEY  (`id_direccion`),
  KEY `direccion_FKIndex3` (`clie_rut`),
  KEY `direcciones_FKIndex2` (`id_comuna`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `dummy`
--

DROP TABLE IF EXISTS `dummy`;
CREATE TABLE `dummy` (
  `a` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`a`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `empresa_instaladora`
--

DROP TABLE IF EXISTS `empresa_instaladora`;
CREATE TABLE `empresa_instaladora` (
  `id_empresa_instaladora` int(11) NOT NULL auto_increment,
  `descripcion` varchar(150) default NULL,
  `nit` varchar(30) default NULL,
  `direccion` varchar(80) default NULL,
  `telefono` varchar(20) default NULL,
  PRIMARY KEY  (`id_empresa_instaladora`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `estados`
--

DROP TABLE IF EXISTS `estados`;
CREATE TABLE `estados` (
  `id_estado` char(2) collate latin1_spanish_ci NOT NULL default '',
  `esta_nombre` varchar(45) collate latin1_spanish_ci default NULL,
  `esta_tipo` char(2) collate latin1_spanish_ci default NULL,
  `orden` tinyint(3) unsigned default NULL,
  `estadoterminal` tinyint(3) unsigned default NULL,
  PRIMARY KEY  (`id_estado`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `fecha_entrega`
--

DROP TABLE IF EXISTS `fecha_entrega`;
CREATE TABLE `fecha_entrega` (
  `id_fecha_entrega` int(11) NOT NULL auto_increment,
  `cod_producto` int(11) default NULL,
  `cod_sap` int(11) default NULL,
  `cod_proveedor` int(11) default NULL,
  `numero_dias` int(11) default NULL,
  `nombre_producto` varchar(255) default NULL,
  `nombre_proveedor` varchar(255) default NULL,
  PRIMARY KEY  (`id_fecha_entrega`),
  UNIQUE KEY `codigo_unico` (`cod_proveedor`,`cod_sap`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `fecha_entrega_despachos`
--

DROP TABLE IF EXISTS `fecha_entrega_despachos`;
CREATE TABLE `fecha_entrega_despachos` (
  `id_tipoentrega` int(11) NOT NULL default '0',
  `descripcion` varchar(30) default NULL,
  `num_dias_fecha_entrega` int(11) default NULL,
  PRIMARY KEY  (`id_tipoentrega`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `glo_grupos`
--

DROP TABLE IF EXISTS `glo_grupos`;
CREATE TABLE `glo_grupos` (
  `GLO_ID` int(8) NOT NULL auto_increment,
  `GLO_TITULO` varchar(255) collate latin1_spanish_ci default NULL,
  `GLO_DESCRIPCION` text collate latin1_spanish_ci,
  `GLO_ORDEN` int(3) default NULL,
  `GLO_USR_CREA` varchar(255) collate latin1_spanish_ci default NULL,
  `GLO_FEC_CREA` datetime default NULL,
  `GLO_USR_MOD` varchar(255) collate latin1_spanish_ci default NULL,
  `GLO_FEC_MOD` datetime default NULL,
  PRIMARY KEY  (`GLO_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `glo_variables`
--

DROP TABLE IF EXISTS `glo_variables`;
CREATE TABLE `glo_variables` (
  `VAR_ID` int(8) NOT NULL auto_increment,
  `VAR_GLO_ID` int(8) NOT NULL default '0',
  `VAR_TITULO` varchar(255) character set latin1 collate latin1_spanish_ci default NULL,
  `VAR_LLAVE` varchar(255) character set latin1 collate latin1_spanish_ci default NULL,
  `VAR_VALOR` varchar(550) default NULL,
  `VAR_DESCRIPCION` text character set latin1 collate latin1_spanish_ci,
  `VAR_ORDEN` int(3) default NULL,
  `VAR_USR_CREA` varchar(255) character set latin1 collate latin1_spanish_ci default NULL,
  `VAR_FEC_CREA` datetime default NULL,
  `VAR_USR_MOD` varchar(255) default NULL,
  `VAR_FEC_MOD` datetime default NULL,
  PRIMARY KEY  (`VAR_ID`),
  KEY `GLO_VARIABLES_FKIndex1` (`VAR_GLO_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `historial`
--

DROP TABLE IF EXISTS `historial`;
CREATE TABLE `historial` (
  `id_historial` int(10) unsigned NOT NULL auto_increment,
  `ot_id` int(10) unsigned default '0',
  `id_os` int(11) default '0',
  `hist_fecha` datetime default '0000-00-00 00:00:00',
  `hist_usuario` varchar(50) default NULL,
  `hist_descripcion` varchar(255) character set latin1 collate latin1_spanish_ci default NULL,
  `his_tipo` char(3) character set latin1 collate latin1_spanish_ci default 'SYS' COMMENT 'SYS: Sistema, USR:Usuario',
  PRIMARY KEY  (`id_historial`),
  KEY `historial_FKIndex1` (`id_os`),
  KEY `historial_FKIndex2` (`ot_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `historyInterfaz`
--

DROP TABLE IF EXISTS `historyInterfaz`;
CREATE TABLE `historyInterfaz` (
  `fechaActualiza` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `nomArchivo` varchar(50) NOT NULL,
  `numReg` int(10) unsigned NOT NULL,
  `sentence` varchar(50) NOT NULL,
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `hoja1`
--

DROP TABLE IF EXISTS `hoja1`;
CREATE TABLE `hoja1` (
  `101000` int(11) default NULL,
  `CHOLGUAN LISO 2.4X1520X2440MM` varchar(255) default NULL,
  `96765270` int(11) default NULL,
  `ARAUCO DISTRIBUCION` varchar(255) default NULL,
  `10` int(11) default NULL,
  `200` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `impuesto_por_cal_tri`
--

DROP TABLE IF EXISTS `impuesto_por_cal_tri`;
CREATE TABLE `impuesto_por_cal_tri` (
  `id_impuesto_cal_tri` int(11) NOT NULL auto_increment,
  `id_impuesto` int(11) default NULL,
  `id_cal_tri` int(11) default NULL,
  `valor` int(2) default NULL,
  PRIMARY KEY  (`id_impuesto_cal_tri`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 4096 kB';

--
-- Table structure for table `impuestos`
--

DROP TABLE IF EXISTS `impuestos`;
CREATE TABLE `impuestos` (
  `id_impuesto` int(11) NOT NULL auto_increment,
  `nombre_impuesto` varchar(25) default NULL,
  PRIMARY KEY  (`id_impuesto`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `instalaciones`
--

DROP TABLE IF EXISTS `instalaciones`;
CREATE TABLE `instalaciones` (
  `id_catprod` varchar(20) collate latin1_spanish_ci NOT NULL default '',
  `descat` varchar(45) collate latin1_spanish_ci default NULL,
  `estadoactivo` char(1) collate latin1_spanish_ci default NULL,
  PRIMARY KEY  (`id_catprod`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `instalador_comuna`
--

DROP TABLE IF EXISTS `instalador_comuna`;
CREATE TABLE `instalador_comuna` (
  `id_instalador` int(10) unsigned NOT NULL default '0',
  `id_comuna` int(10) unsigned NOT NULL default '0',
  KEY `instalador_comuna_FKIndex1` (`id_comuna`),
  KEY `instalador_comuna_FKIndex2` (`id_instalador`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `instaladores`
--

DROP TABLE IF EXISTS `instaladores`;
CREATE TABLE `instaladores` (
  `id_instalador` int(10) unsigned NOT NULL auto_increment,
  `inst_nombre` varchar(20) character set latin1 collate latin1_spanish_ci NOT NULL default '',
  `inst_paterno` varchar(20) character set latin1 collate latin1_spanish_ci NOT NULL default '',
  `inst_materno` varchar(20) character set latin1 collate latin1_spanish_ci default NULL,
  `inst_telefono` varchar(20) character set latin1 collate latin1_spanish_ci default NULL,
  `inst_rut` varchar(20) character set latin1 collate latin1_spanish_ci NOT NULL default '',
  `direccion` varchar(45) character set latin1 collate latin1_spanish_ci default NULL,
  `telefono2` varchar(20) character set latin1 collate latin1_spanish_ci default NULL,
  `email` varchar(128) character set latin1 collate latin1_spanish_ci default NULL,
  `id_empresa_instaladora` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id_instalador`),
  KEY `FK_instaladores_1_comuna` (`id_empresa_instaladora`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `list_bono`
--

DROP TABLE IF EXISTS `list_bono`;
CREATE TABLE `list_bono` (
  `id_Bono` int(10) unsigned NOT NULL auto_increment,
  `fec_Crea` datetime default NULL,
  `valor` bigint(20) unsigned default NULL,
  `liq_porcent` float default NULL,
  `num_impre` int(10) unsigned default '0',
  `usu_creacion` varchar(45) default NULL,
  `id_Lista` int(10) unsigned default NULL,
  `fec_impresion` datetime default NULL,
  PRIMARY KEY  (`id_Bono`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `list_eventos`
--

DROP TABLE IF EXISTS `list_eventos`;
CREATE TABLE `list_eventos` (
  `idEvento` int(10) unsigned NOT NULL auto_increment,
  `nombre` varchar(255) NOT NULL,
  PRIMARY KEY  (`idEvento`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

--
-- Table structure for table `list_historial`
--

DROP TABLE IF EXISTS `list_historial`;
CREATE TABLE `list_historial` (
  `id_historial` int(10) unsigned NOT NULL auto_increment,
  `ot_idList` int(10) unsigned default '0',
  `idLista` int(11) default '0',
  `hist_fecha` datetime default '0000-00-00 00:00:00',
  `hist_usuario` varchar(50) default NULL,
  `hist_descripcion` varchar(255) character set latin1 collate latin1_spanish_ci default NULL,
  `his_tipo` char(3) character set latin1 collate latin1_spanish_ci default 'SYS' COMMENT 'SYS: Sistema, USR:Usuario',
  `os_idList` int(10) unsigned default NULL,
  PRIMARY KEY  (`id_historial`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `list_os_det`
--

DROP TABLE IF EXISTS `list_os_det`;
CREATE TABLE `list_os_det` (
  `idLista_OS_det` int(10) unsigned NOT NULL auto_increment,
  `idLista_OS_enc` int(10) unsigned default NULL,
  `idLista_det` int(10) unsigned default NULL,
  `OS_cantidad` int(10) unsigned default NULL,
  `OS_idOT` int(10) unsigned default NULL,
  `OS_cantPick` int(10) unsigned default '0',
  PRIMARY KEY  (`idLista_OS_det`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `list_os_enc`
--

DROP TABLE IF EXISTS `list_os_enc`;
CREATE TABLE `list_os_enc` (
  `idLista_OS_enc` int(10) unsigned NOT NULL auto_increment,
  `idLista_enc` int(10) unsigned NOT NULL,
  `OS_estado` char(2) character set latin1 collate latin1_spanish_ci default NULL,
  `OS_local` int(10) unsigned default NULL,
  `OS_idUsuario` int(8) unsigned default NULL,
  `OS_clieRut` varchar(45) default NULL,
  `OS_fecCrea` datetime default NULL,
  `invitado` varchar(255) default NULL,
  `num_boleta` varchar(45) default NULL,
  `OS_fecPago` datetime default NULL,
  PRIMARY KEY  USING BTREE (`idLista_OS_enc`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC COMMENT='Cotizacion Lista de Regalos';

--
-- Table structure for table `list_ot`
--

DROP TABLE IF EXISTS `list_ot`;
CREATE TABLE `list_ot` (
  `ot_idList` int(10) unsigned NOT NULL auto_increment,
  `ot_idEstado` char(2) collate latin1_spanish_ci NOT NULL,
  `ot_listTipo` varchar(45) character set latin1 NOT NULL,
  `ot_listFeccrea` datetime default NULL,
  `ot_listNumImp` int(11) default '0',
  `ot_usuAutoriza` varchar(255) collate latin1_spanish_ci default NULL,
  `ot_listTipopago` varchar(45) collate latin1_spanish_ci default NULL,
  `ot_listTiendaPago` varchar(45) collate latin1_spanish_ci default NULL,
  `no_OC_SAP` varchar(45) collate latin1_spanish_ci default '0',
  `no_TR_SAP` varchar(45) collate latin1_spanish_ci default '0',
  `id_proveedor` int(11) default NULL,
  `fec_compra` datetime default NULL,
  `compra_obs` varchar(255) collate latin1_spanish_ci default NULL,
  PRIMARY KEY  (`ot_idList`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Table structure for table `list_regalos_det`
--

DROP TABLE IF EXISTS `list_regalos_det`;
CREATE TABLE `list_regalos_det` (
  `idLista_det` int(10) unsigned NOT NULL auto_increment,
  `idLista_enc` int(10) unsigned NOT NULL,
  `cod_Ean` varchar(13) collate latin1_spanish_ci default NULL,
  `cod_Easy` varchar(20) collate latin1_spanish_ci default NULL,
  `descripcion` varchar(255) character set latin1 default NULL,
  `list_tipoprod` char(2) character set latin1 default NULL,
  `list_cantprod` decimal(10,0) default '0',
  `list_precio` int(10) unsigned default NULL,
  `list_idTipodespacho` int(10) unsigned default NULL,
  `list_instalacion` tinyint(1) unsigned default NULL,
  `list_Cantcomp` decimal(10,0) default '0',
  `invitado` varchar(255) collate latin1_spanish_ci default NULL,
  `peso` bigint(20) unsigned default '0',
  PRIMARY KEY  (`idLista_det`),
  FULLTEXT KEY `descripcion` (`descripcion`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Table structure for table `list_regalos_enc`
--

DROP TABLE IF EXISTS `list_regalos_enc`;
CREATE TABLE `list_regalos_enc` (
  `idLista` int(10) unsigned NOT NULL auto_increment,
  `clie_Rut` bigint(18) unsigned NOT NULL,
  `id_Evento` int(10) unsigned NOT NULL,
  `fec_creacion` datetime default NULL,
  `id_Estado` char(2) collate latin1_spanish_ci default NULL,
  `festejado` varchar(255) collate latin1_spanish_ci default NULL,
  `fec_Evento` datetime default NULL,
  `fec_entrega` datetime default NULL,
  `id_Direccion` int(10) unsigned default NULL,
  `id_Local` int(10) unsigned default NULL,
  `id_Usuario` int(8) unsigned default NULL,
  `descripcion` varchar(255) collate latin1_spanish_ci default NULL,
  `GD_fecImpresion` datetime default NULL,
  `GD_numImpresion` decimal(10,0) default '0',
  `GD_id` int(10) unsigned default NULL,
  `GD_usReimpre` varchar(45) collate latin1_spanish_ci default NULL,
  `fec_cierre` datetime default NULL,
  PRIMARY KEY  (`idLista`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `local_usr`
--

DROP TABLE IF EXISTS `local_usr`;
CREATE TABLE `local_usr` (
  `USR_ID` int(10) unsigned NOT NULL default '0',
  `id_local` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`USR_ID`),
  KEY `local_usr_FKIndex1` (`id_local`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `locales`
--

DROP TABLE IF EXISTS `locales`;
CREATE TABLE `locales` (
  `id_local` int(10) unsigned NOT NULL default '0',
  `cod_local` varchar(5) character set latin1 collate latin1_spanish_ci default NULL,
  `nom_local` varchar(30) character set latin1 collate latin1_spanish_ci default NULL,
  `dir_local` varchar(45) character set latin1 collate latin1_spanish_ci default NULL,
  `fono_local` varchar(10) character set latin1 collate latin1_spanish_ci default NULL,
  `tipo_local` char(1) character set latin1 collate latin1_spanish_ci default NULL,
  `reg_local` varchar(20) character set latin1 collate latin1_spanish_ci default NULL,
  `ciu_local` varchar(20) character set latin1 collate latin1_spanish_ci default NULL,
  `com_local` varchar(20) character set latin1 collate latin1_spanish_ci default NULL,
  `cat_local` varchar(20) character set latin1 collate latin1_spanish_ci default NULL,
  `orden` int(3) unsigned default NULL,
  `tip_local` varchar(20) character set latin1 collate latin1_spanish_ci default NULL,
  `fmt_local` varchar(20) character set latin1 collate latin1_spanish_ci default NULL,
  `ip_local` varchar(20) character set latin1 collate latin1_spanish_ci default NULL,
  `path_destino` varchar(255) character set latin1 collate latin1_spanish_ci default NULL,
  `cod_local_pos` varchar(4) character set latin1 collate latin1_spanish_ci default NULL,
  `id_cadena` int(10) unsigned default NULL,
  `fecha_carga_precios` datetime default NULL,
  `id_localizacion` bigint(18) default NULL,
  PRIMARY KEY  (`id_local`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `localidad`
--

DROP TABLE IF EXISTS `localidad`;
CREATE TABLE `localidad` (
  `id_localidad` int(11) NOT NULL,
  `nombre_localidad` varchar(60) NOT NULL,
  `id_ciudad` int(11) default NULL,
  PRIMARY KEY  (`id_localidad`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `log_table`
--

DROP TABLE IF EXISTS `log_table`;
CREATE TABLE `log_table` (
  `id_log` int(10) unsigned NOT NULL auto_increment,
  `programa` varchar(255) collate latin1_spanish_ci default NULL,
  `tipo_log` varchar(64) collate latin1_spanish_ci default NULL,
  `fecha` datetime default NULL,
  `subrutina` varchar(255) collate latin1_spanish_ci default NULL,
  `texto` text collate latin1_spanish_ci,
  `cod_err` varchar(64) collate latin1_spanish_ci default NULL,
  PRIMARY KEY  (`id_log`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `lote_instalador`
--

DROP TABLE IF EXISTS `lote_instalador`;
CREATE TABLE `lote_instalador` (
  `id_lote` int(10) unsigned NOT NULL auto_increment,
  `id_instalador` int(10) unsigned NOT NULL default '0',
  `estado` char(1) default NULL,
  `fechageneracion` datetime default NULL,
  `fechafacturacion` datetime default NULL,
  `usuario` varchar(255) default NULL,
  `usuarioingreso` varchar(255) default NULL,
  `num_factura` int(10) unsigned default NULL,
  `monto_factura` int(10) unsigned default NULL,
  `numero1` int(10) unsigned default NULL,
  `numero2` int(10) unsigned default NULL,
  `retencion_lote` int(10) unsigned default NULL,
  PRIMARY KEY  (`id_lote`),
  KEY `lote_instalador_FKIndex1` (`id_instalador`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `modulos`
--

DROP TABLE IF EXISTS `modulos`;
CREATE TABLE `modulos` (
  `MOD_ID` int(8) NOT NULL auto_increment,
  `MOD_PADRE_ID` int(8) default NULL,
  `MOD_ESTADO` int(2) default NULL,
  `MOD_NOMBRE` varchar(255) collate latin1_spanish_ci default NULL,
  `MOD_DESCRIPCION` text collate latin1_spanish_ci,
  `MOD_URL` text collate latin1_spanish_ci,
  `MOD_ORDEN` int(5) default NULL,
  `MOD_USR_CREA` varchar(255) collate latin1_spanish_ci default NULL,
  `MOD_FEC_CREA` datetime default NULL,
  `MOD_USR_MOD` varchar(255) collate latin1_spanish_ci default NULL,
  `MOD_FEC_MOD` datetime default NULL,
  PRIMARY KEY  (`MOD_ID`),
  KEY `modulos_FKIndex1` (`MOD_PADRE_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `motivo_dev_os`
--

DROP TABLE IF EXISTS `motivo_dev_os`;
CREATE TABLE `motivo_dev_os` (
  `id` int(11) NOT NULL,
  `nombre` varchar(45) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `msg_pri`
--

DROP TABLE IF EXISTS `msg_pri`;
CREATE TABLE `msg_pri` (
  `MSG_PRI_ID` int(8) NOT NULL auto_increment,
  `MSG_PRI_TEXT` varchar(255) collate latin1_spanish_ci default NULL,
  `MSG_PRI_USR_TO` int(8) default NULL,
  `MSG_PRI_USR_FROM` varchar(255) collate latin1_spanish_ci default NULL,
  `MSG_PRI_USR_FROM_ID` varchar(255) collate latin1_spanish_ci default NULL,
  `MSG_PRI_TIPO` int(2) default NULL,
  `MSG_PRI_VISTO` int(2) default '1',
  `MSG_PRI_FECHA` datetime default NULL,
  PRIMARY KEY  (`MSG_PRI_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `origen_descuentos`
--

DROP TABLE IF EXISTS `origen_descuentos`;
CREATE TABLE `origen_descuentos` (
  `id_origen` int(10) unsigned NOT NULL auto_increment,
  `nombre` varchar(20) collate latin1_spanish_ci default NULL,
  PRIMARY KEY  (`id_origen`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `origenusr`
--

DROP TABLE IF EXISTS `origenusr`;
CREATE TABLE `origenusr` (
  `id_origen` int(10) unsigned NOT NULL auto_increment,
  `nom_origen` varchar(45) NOT NULL default '',
  PRIMARY KEY  (`id_origen`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `os`
--

DROP TABLE IF EXISTS `os`;
CREATE TABLE `os` (
  `id_os` bigint(18) NOT NULL auto_increment,
  `id_estado` char(2) character set latin1 collate latin1_spanish_ci NOT NULL default '',
  `id_proyecto` int(10) unsigned NOT NULL default '0',
  `id_local` int(10) unsigned NOT NULL default '0',
  `id_direccion` int(10) unsigned NOT NULL default '0',
  `clie_rut` bigint(18) unsigned NOT NULL default '0',
  `os_fechacreacion` datetime default NULL,
  `os_fechacotizacion` datetime NOT NULL default '0000-00-00 00:00:00',
  `os_fechaestimada` datetime default NULL,
  `os_comentarios` varchar(255) character set latin1 collate latin1_spanish_ci default NULL,
  `os_descripcion` varchar(350) default NULL,
  `usuario` varchar(255) character set latin1 collate latin1_spanish_ci default NULL,
  `USR_ID` int(8) unsigned default NULL,
  `os_fechaestimacion` datetime default NULL,
  `origen` char(1) character set latin1 collate latin1_spanish_ci default NULL,
  `USR_ORIGEN` int(10) unsigned default NULL,
  `os_cotizaciones_cruzadas` varchar(255) default NULL,
  `os_fechaboleta` datetime default NULL,
  `os_numboleta` varchar(20) default NULL,
  `os_codbarras` varchar(15) default NULL,
  `os_terminal_pos` int(11) default NULL,
  `zona` varchar(30) default NULL,
  PRIMARY KEY  (`id_os`),
  KEY `cotizacion_FKIndex2` (`clie_rut`),
  KEY `os_FKIndex2` (`id_proyecto`),
  KEY `os_FKIndex3` (`id_direccion`),
  KEY `os_FKIndex4` (`id_local`),
  KEY `os_FKIndex5` (`id_estado`),
  KEY `os_FKIndex6` (`USR_ORIGEN`),
  KEY `os_FKIndex7` (`origen`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `os_detalle`
--

DROP TABLE IF EXISTS `os_detalle`;
CREATE TABLE `os_detalle` (
  `id_os_detalle` int(10) unsigned NOT NULL auto_increment,
  `ot_id` int(10) unsigned default NULL,
  `id_origen` int(10) unsigned NOT NULL default '0',
  `id_tipodespacho` int(10) unsigned NOT NULL default '0',
  `id_os` bigint(18) NOT NULL default '0',
  `osde_tipoprod` char(2) character set latin1 collate latin1_spanish_ci default NULL,
  `osde_subtipoprod` char(2) character set latin1 collate latin1_spanish_ci default NULL,
  `osde_instalacion` tinyint(1) unsigned default NULL,
  `osde_precio` int(10) unsigned default NULL,
  `osde_cantidad` decimal(10,0) unsigned default NULL,
  `osde_descuento` int(10) unsigned default NULL,
  `osde_preciocosto` int(10) unsigned default NULL,
  `cod_sap` varchar(20) character set latin1 collate latin1_spanish_ci default NULL,
  `osde_especificacion` varchar(255) character set latin1 collate latin1_spanish_ci default NULL,
  `osde_descripcion` varchar(60) character set latin1 collate latin1_spanish_ci default NULL,
  `id_producto` int(10) unsigned default NULL,
  `cod_barra` varchar(13) character set latin1 collate latin1_spanish_ci default NULL,
  `usrnomaut` varchar(40) character set latin1 collate latin1_spanish_ci default NULL,
  `usrpassaut` varchar(40) character set latin1 collate latin1_spanish_ci default NULL,
  `observacion` varchar(255) character set latin1 collate latin1_spanish_ci default NULL,
  `ind_dec` tinyint(3) unsigned default '0',
  `cant_pickeada` decimal(10,0) default NULL,
  `osde_fecha_entrega` varchar(15) default NULL,
  PRIMARY KEY  (`id_os_detalle`),
  KEY `os_detalle_FKIndex1` (`id_os`),
  KEY `os_detalle_FKIndex2` (`id_tipodespacho`),
  KEY `os_detalle_FKIndex3` (`id_origen`),
  KEY `os_detalle_FKIndex4` (`ot_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `ot`
--

DROP TABLE IF EXISTS `ot`;
CREATE TABLE `ot` (
  `ot_id` int(10) unsigned NOT NULL auto_increment,
  `id_tipodespacho` int(10) unsigned NOT NULL default '0',
  `id_estado` char(2) character set latin1 collate latin1_spanish_ci NOT NULL default '',
  `id_os` int(11) NOT NULL default '0',
  `ot_tipo` char(2) character set latin1 collate latin1_spanish_ci default NULL,
  `ot_fechacreacion` datetime default NULL,
  `ot_cantidad` int(10) unsigned default NULL,
  `ot_especificacion` int(10) unsigned default NULL,
  `ot_freactivacion` date default NULL,
  `ot_comentario` varchar(255) character set latin1 collate latin1_spanish_ci default NULL,
  `margenapagar` tinyint(3) unsigned default NULL,
  `subtotalapagar` int(10) unsigned default NULL,
  `id_lote` int(10) unsigned default NULL,
  `id_proveedor` int(10) unsigned default NULL,
  `desp_ddp` tinyint(3) unsigned default '0',
  `noc_sap` mediumtext character set latin1 collate latin1_spanish_ci,
  `id_instalador` int(10) unsigned default NULL,
  `ot_ftermino` datetime default NULL COMMENT 'Fecha de cambio a estado terminal',
  `statusprint` int(10) unsigned default NULL,
  `numero_impresiones` int(10) default '0',
  `usu_autorizacion_reimpresion` varchar(255) default NULL,
  `numero_impresionesGuia` int(10) default '0',
  `fecha_visita_inst` varchar(22) default NULL,
  `doc_instalacion` varchar(10) default NULL,
  `os_asoInstala` bigint(18) unsigned NOT NULL default '0',
  `os_asoMaterial` bigint(18) unsigned NOT NULL default '0',
  `os_motDevolucion` bigint(18) unsigned NOT NULL default '0',
  `os_valDevolucion` varchar(45) NOT NULL,
  `os_asoVisita` bigint(18) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ot_id`),
  KEY `ot_FKIndex1` (`id_os`),
  KEY `ot_FKIndex2` (`id_estado`),
  KEY `ot_FKIndex3` (`id_tipodespacho`),
  KEY `FK_ot_3_Lote` (`id_lote`),
  KEY `FK_ot_3_Prov` (`id_proveedor`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `pedido_especial`
--

DROP TABLE IF EXISTS `pedido_especial`;
CREATE TABLE `pedido_especial` (
  `id_catprod` varchar(20) collate latin1_spanish_ci NOT NULL default '',
  `descat` varchar(45) collate latin1_spanish_ci default NULL,
  `estadoactivo` char(1) collate latin1_spanish_ci default NULL,
  PRIMARY KEY  (`id_catprod`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `perfiles`
--

DROP TABLE IF EXISTS `perfiles`;
CREATE TABLE `perfiles` (
  `PER_ID` int(8) NOT NULL auto_increment,
  `PER_NOMBRE` varchar(255) collate latin1_spanish_ci default NULL,
  `PER_DESCRIPCION` text collate latin1_spanish_ci,
  `PER_PADRE` int(8) default NULL,
  `PER_USR_CREA` varchar(255) collate latin1_spanish_ci default NULL,
  `PER_FEC_CREA` datetime default NULL,
  `PER_USR_MOD` varchar(255) collate latin1_spanish_ci default NULL,
  `PER_FEC_MOD` datetime default NULL,
  PRIMARY KEY  (`PER_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `perfilesxusuario`
--

DROP TABLE IF EXISTS `perfilesxusuario`;
CREATE TABLE `perfilesxusuario` (
  `PEUS_PER_ID` int(8) NOT NULL default '0',
  `PEUS_USR_ID` int(8) NOT NULL default '0',
  `PEUS_USR_CREA` varchar(255) collate latin1_spanish_ci default NULL,
  `PEUS_FEC_CREA` datetime default NULL,
  `PEUS_USR_MOD` varchar(255) collate latin1_spanish_ci default NULL,
  `PEUS_FEC_MOD` datetime default NULL,
  KEY `perfilesxusuario_FKIndex1` (`PEUS_USR_ID`),
  KEY `perfilesxusuario_FKIndex2` (`PEUS_PER_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `permisosxmodulo`
--

DROP TABLE IF EXISTS `permisosxmodulo`;
CREATE TABLE `permisosxmodulo` (
  `PEMO_MOD_ID` int(8) NOT NULL default '0',
  `PEMO_TIPO` int(2) NOT NULL default '0',
  `PEMO_PER_ID` int(8) NOT NULL default '0',
  `PEMO_INSERT` int(1) default NULL,
  `PEMO_DELETE` int(1) default NULL,
  `PEMO_UPDATE` int(1) default NULL,
  `PEMO_SELECT` int(1) default NULL,
  `PEMO_USR_CREA` varchar(255) collate latin1_spanish_ci default NULL,
  `PEMO_FEC_CREA` datetime default NULL,
  `PEMO_USR_MOD` varchar(255) collate latin1_spanish_ci default NULL,
  `PEMO_FEC_MOD` datetime default NULL,
  KEY `permisosxmodulo_FKIndex1` (`PEMO_MOD_ID`),
  KEY `permisosxmodulo_FKIndex2` (`PEMO_PER_ID`),
  KEY `permisosxmodulo_FKIndex3` (`PEMO_PER_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `precios`
--

DROP TABLE IF EXISTS `precios`;
CREATE TABLE `precios` (
  `id_local` int(10) unsigned NOT NULL default '0',
  `id_producto` int(10) unsigned NOT NULL default '0',
  `cod_prod1` varchar(20) character set latin1 collate latin1_spanish_ci default NULL,
  `cod_local` varchar(20) character set latin1 collate latin1_spanish_ci default NULL,
  `prec_costo` decimal(10,0) default NULL,
  `prec_valor` decimal(14,2) NOT NULL default '0.00',
  `stock` decimal(10,0) default '0',
  `estadoactivo` char(1) character set latin1 collate latin1_spanish_ci default NULL,
  `retefuente` float(10,4) default NULL,
  `reteiva` float(10,4) default NULL,
  `reteica` float(10,4) default NULL,
  `iva` float(10,4) default NULL,
  KEY `precios_FKIndex1` (`id_producto`),
  KEY `precios_FKIndex2` (`id_local`),
  KEY `precios_index10364` (`cod_prod1`),
  KEY `indice_compuesto` (`id_local`,`cod_prod1`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `precios_temp`
--

DROP TABLE IF EXISTS `precios_temp`;
CREATE TABLE `precios_temp` (
  `cod_prod1` varchar(20) character set latin1 collate latin1_spanish_ci default NULL,
  `pre_cost` decimal(10,0) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `priceCero`
--

DROP TABLE IF EXISTS `priceCero`;
CREATE TABLE `priceCero` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `fecha` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `nombreArchivo` varchar(50) NOT NULL,
  `cod_prod` varchar(20) NOT NULL,
  `cod_barra` varchar(20) default NULL,
  PRIMARY KEY  USING BTREE (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
CREATE TABLE `productos` (
  `id_producto` int(10) unsigned NOT NULL auto_increment,
  `id_catprod` varchar(20) character set latin1 collate latin1_spanish_ci NOT NULL default '',
  `cod_prod1` varchar(20) character set latin1 collate latin1_spanish_ci NOT NULL default '',
  `cod_prod2` varchar(20) character set latin1 collate latin1_spanish_ci default NULL,
  `des_corta` varchar(45) character set latin1 collate latin1_spanish_ci default NULL,
  `des_larga` varchar(255) character set latin1 collate latin1_spanish_ci default NULL,
  `estadoactivo` char(1) character set latin1 collate latin1_spanish_ci default NULL,
  `cod_propal` varchar(20) character set latin1 collate latin1_spanish_ci default NULL,
  `prod_tipo` char(2) character set latin1 collate latin1_spanish_ci default NULL,
  `prod_subtipo` char(2) character set latin1 collate latin1_spanish_ci default NULL,
  `stock_proveedor` decimal(10,0) default NULL,
  `id_cadena` int(10) unsigned default NULL,
  `peso` float(13,3) default NULL,
  `volumen` float(13,3) default NULL,
  PRIMARY KEY  (`id_producto`),
  KEY `productos_FKIndex1` (`id_catprod`,`id_cadena`),
  KEY `productos_index10247` (`cod_prod1`),
  FULLTEXT KEY `des_larga` (`des_larga`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `productos_ext`
--

DROP TABLE IF EXISTS `productos_ext`;
CREATE TABLE `productos_ext` (
  `id_producto` int(10) unsigned NOT NULL default '0',
  `stock_proveedor` decimal(10,0) default NULL,
  `img` varchar(255) collate latin1_spanish_ci default NULL,
  `espec_tecnicas` text collate latin1_spanish_ci,
  KEY `productos_ext_FKIndex1` (`id_producto`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `prodxprov`
--

DROP TABLE IF EXISTS `prodxprov`;
CREATE TABLE `prodxprov` (
  `id_producto` int(10) unsigned NOT NULL default '0',
  `id_proveedor` int(10) unsigned NOT NULL default '0',
  `cod_prov` varchar(20) collate latin1_spanish_ci default NULL,
  `cod_prod1` varchar(20) collate latin1_spanish_ci default NULL,
  `estadoactivo` char(1) collate latin1_spanish_ci default NULL,
  UNIQUE KEY `prodxprov_index10257` (`id_producto`,`id_proveedor`),
  KEY `prod_prov_FKIndex2` (`id_proveedor`),
  KEY `prodxprov_FKIndex2` (`id_producto`),
  KEY `prodxprov_index10254` (`estadoactivo`),
  KEY `cod_prod1_index` (`cod_prod1`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `proveedores`
--

DROP TABLE IF EXISTS `proveedores`;
CREATE TABLE `proveedores` (
  `id_proveedor` int(10) unsigned NOT NULL auto_increment,
  `cod_prov` varchar(20) collate latin1_spanish_ci default NULL,
  `rut_prov` bigint(18) unsigned default NULL,
  `nom_prov` varchar(60) collate latin1_spanish_ci default NULL,
  `razsoc_prov` varchar(45) collate latin1_spanish_ci default NULL,
  `fonocto_prov` varchar(20) collate latin1_spanish_ci default NULL,
  `nombcto_prov` varchar(20) collate latin1_spanish_ci default NULL,
  `emailcto_prov` varchar(45) collate latin1_spanish_ci default NULL,
  `estadoactivo` char(1) collate latin1_spanish_ci default NULL,
  PRIMARY KEY  (`id_proveedor`),
  KEY `proveedores_index10271` (`cod_prov`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `proveedores_ext`
--

DROP TABLE IF EXISTS `proveedores_ext`;
CREATE TABLE `proveedores_ext` (
  `id_proveedor` int(10) unsigned NOT NULL default '0',
  `diasentrega` int(10) unsigned default NULL,
  `instalacion` char(1) collate latin1_spanish_ci default NULL,
  `nomcontacto` int(10) unsigned default NULL,
  `mailcontacto` int(10) unsigned default NULL,
  PRIMARY KEY  (`id_proveedor`),
  KEY `Table_21_FKIndex1` (`id_proveedor`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `proyectos`
--

DROP TABLE IF EXISTS `proyectos`;
CREATE TABLE `proyectos` (
  `id_proyecto` int(10) unsigned NOT NULL auto_increment,
  `clie_rut` bigint(18) unsigned NOT NULL default '0',
  `proy_nombre` varchar(45) collate latin1_spanish_ci default NULL,
  PRIMARY KEY  (`id_proyecto`),
  KEY `proyecto_FKIndex1` (`clie_rut`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `regiones`
--

DROP TABLE IF EXISTS `regiones`;
CREATE TABLE `regiones` (
  `id_region` int(11) NOT NULL,
  `regi_numero` int(11) default NULL,
  `regi_nombre` varchar(45) NOT NULL,
  PRIMARY KEY  (`id_region`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `sexo_cliente`
--

DROP TABLE IF EXISTS `sexo_cliente`;
CREATE TABLE `sexo_cliente` (
  `id_sexo_cliente` int(11) NOT NULL auto_increment,
  `sexo_cliente` varchar(20) default NULL,
  PRIMARY KEY  (`id_sexo_cliente`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `tem_precios`
--

DROP TABLE IF EXISTS `tem_precios`;
CREATE TABLE `tem_precios` (
  `SAP` varchar(20) character set latin1 collate latin1_spanish_ci default NULL,
  `LOCAL` varchar(20) character set latin1 collate latin1_spanish_ci default NULL,
  `PRECIOS` decimal(10,0) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `teradata`
--

DROP TABLE IF EXISTS `teradata`;
CREATE TABLE `teradata` (
  `idteradata` int(11) NOT NULL auto_increment,
  `id_cotizacion` int(20) NOT NULL,
  `detalle` int(1) NOT NULL,
  `encabezado` int(1) NOT NULL,
  `fecha_proceso` date NOT NULL,
  `fecha_paso_interfase` datetime NOT NULL,
  `secuencia` int(2) NOT NULL,
  `archivo` varchar(45) NOT NULL,
  `codprod` int(10) default NULL,
  PRIMARY KEY  (`idteradata`),
  KEY `Sec` (`fecha_proceso`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Log de la tabla Teradata';

--
-- Table structure for table `tipo_categoria_cliente`
--

DROP TABLE IF EXISTS `tipo_categoria_cliente`;
CREATE TABLE `tipo_categoria_cliente` (
  `id_categoria` int(11) NOT NULL auto_increment,
  `nombre_categoria` varchar(30) default NULL,
  PRIMARY KEY  (`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `tipo_cliente`
--

DROP TABLE IF EXISTS `tipo_cliente`;
CREATE TABLE `tipo_cliente` (
  `id_tipo_cliente` int(11) NOT NULL auto_increment,
  `nombre_tipo_cliente` varchar(40) default NULL,
  PRIMARY KEY  (`id_tipo_cliente`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `tipo_nif`
--

DROP TABLE IF EXISTS `tipo_nif`;
CREATE TABLE `tipo_nif` (
  `id_nif` int(11) NOT NULL default '0',
  `descripcion` varchar(100) default NULL,
  PRIMARY KEY  (`id_nif`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `tipo_subtipo`
--

DROP TABLE IF EXISTS `tipo_subtipo`;
CREATE TABLE `tipo_subtipo` (
  `prod_tipo` char(2) collate latin1_spanish_ci NOT NULL default '',
  `prod_subtipo` char(2) collate latin1_spanish_ci NOT NULL default '',
  `precio_req` tinyint(3) unsigned default NULL,
  `valid_stock` tinyint(3) unsigned default NULL,
  `precio_mod` tinyint(3) unsigned default NULL,
  `glosa` varchar(20) collate latin1_spanish_ci default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `tipo_subtipo_adm`
--

DROP TABLE IF EXISTS `tipo_subtipo_adm`;
CREATE TABLE `tipo_subtipo_adm` (
  `tipo` char(2) NOT NULL default '',
  `subtipo` char(2) NOT NULL default '',
  `codigos` longblob,
  `tipocatprod` char(1) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `tipos_despacho`
--

DROP TABLE IF EXISTS `tipos_despacho`;
CREATE TABLE `tipos_despacho` (
  `id_tipodespacho` int(10) unsigned NOT NULL auto_increment,
  `nombre` varchar(20) collate latin1_spanish_ci default NULL,
  PRIMARY KEY  (`id_tipodespacho`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `tmp_listdet`
--

DROP TABLE IF EXISTS `tmp_listdet`;
CREATE TABLE `tmp_listdet` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `codProd` bigint(20) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `tracking`
--

DROP TABLE IF EXISTS `tracking`;
CREATE TABLE `tracking` (
  `tra_usr_id` varchar(255) collate latin1_spanish_ci NOT NULL default '',
  `tra_uid` varchar(255) collate latin1_spanish_ci NOT NULL default 'Sin sesion',
  `tra_tracktime` datetime NOT NULL default '0000-00-00 00:00:00',
  `tra_ip` varchar(20) collate latin1_spanish_ci NOT NULL default '',
  `tra_server` varchar(90) collate latin1_spanish_ci NOT NULL default '',
  `tra_referer` varchar(255) collate latin1_spanish_ci NOT NULL default '',
  `tra_requrl` varchar(255) collate latin1_spanish_ci NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `trp_catprod`
--

DROP TABLE IF EXISTS `trp_catprod`;
CREATE TABLE `trp_catprod` (
  `cod_cat` varchar(10) collate latin1_spanish_ci default NULL,
  `cod_cat_padre` varchar(10) collate latin1_spanish_ci default NULL,
  `descat` varchar(30) collate latin1_spanish_ci default NULL,
  `cat_nivel` int(1) unsigned default NULL,
  `cat_tipo` char(1) collate latin1_spanish_ci default NULL,
  `car_estadocarga` tinyint(3) unsigned default NULL,
  `id_cod_error` int(10) unsigned default NULL,
  `id_cadena` tinyint(3) unsigned default NULL,
  KEY `trp_catprod_index8493` (`car_estadocarga`),
  KEY `trp_catprod_index8501` (`cat_nivel`),
  KEY `trp_catprod_index8502` (`cod_cat`,`id_cadena`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `trp_cod_error`
--

DROP TABLE IF EXISTS `trp_cod_error`;
CREATE TABLE `trp_cod_error` (
  `id_cod` int(10) unsigned NOT NULL default '0',
  `descripcion` varchar(128) collate latin1_spanish_ci default NULL,
  PRIMARY KEY  (`id_cod`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `trp_codbarra`
--

DROP TABLE IF EXISTS `trp_codbarra`;
CREATE TABLE `trp_codbarra` (
  `cod_prod1` varchar(20) collate latin1_spanish_ci default NULL,
  `cod_barra` varchar(14) collate latin1_spanish_ci default NULL,
  `tip_codbar` char(3) collate latin1_spanish_ci default NULL,
  `cod_ppal` char(1) collate latin1_spanish_ci default NULL,
  `unid_med` char(3) collate latin1_spanish_ci default NULL,
  `car_estadocarga` char(1) collate latin1_spanish_ci default NULL,
  `id_cod_error` int(10) unsigned default NULL,
  `id_cadena` tinyint(3) unsigned default NULL,
  `factor_conv` double default NULL,
  KEY `trp_codbarra_index8499` (`car_estadocarga`),
  KEY `trp_codbarra_index8513` (`id_cadena`,`cod_prod1`),
  KEY `trp_codbarra_index8514` (`cod_prod1`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `trp_inventario`
--

DROP TABLE IF EXISTS `trp_inventario`;
CREATE TABLE `trp_inventario` (
  `cod_prod1` varchar(20) collate latin1_spanish_ci NOT NULL default '',
  `cod_local` varchar(20) collate latin1_spanish_ci default NULL,
  `prec_valor` varchar(20) collate latin1_spanish_ci default NULL,
  `prec_costo` varchar(20) collate latin1_spanish_ci default NULL,
  `stock` varchar(20) collate latin1_spanish_ci default NULL,
  `car_estadocarga` tinyint(3) unsigned default NULL,
  `id_cod_error` int(10) unsigned default NULL,
  `id_cadena` tinyint(3) unsigned default NULL,
  KEY `trp_inventario_index10250` (`cod_prod1`),
  KEY `trp_inventario_index10360` (`cod_local`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `trp_notificacion`
--

DROP TABLE IF EXISTS `trp_notificacion`;
CREATE TABLE `trp_notificacion` (
  `id_notificacion` int(10) unsigned NOT NULL auto_increment,
  `idcarga` int(10) unsigned default NULL,
  `cod_prod1` varchar(20) collate latin1_spanish_ci default NULL,
  `cod_cat` varchar(10) collate latin1_spanish_ci default NULL,
  `estado` char(1) collate latin1_spanish_ci default NULL,
  `id_cadena` tinyint(3) unsigned default NULL,
  PRIMARY KEY  (`id_notificacion`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `trp_osdetprod`
--

DROP TABLE IF EXISTS `trp_osdetprod`;
CREATE TABLE `trp_osdetprod` (
  `id_cadena` tinyint(3) unsigned NOT NULL default '0',
  `id_os` int(10) unsigned NOT NULL default '0',
  `cod_prod1` varchar(20) collate latin1_spanish_ci NOT NULL default '',
  `id_osdetalle` int(10) unsigned NOT NULL default '0',
  `estado_disp` char(1) collate latin1_spanish_ci default NULL,
  PRIMARY KEY  (`id_cadena`,`id_os`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `trp_precios`
--

DROP TABLE IF EXISTS `trp_precios`;
CREATE TABLE `trp_precios` (
  `cod_prod1` varchar(20) collate latin1_spanish_ci NOT NULL default '',
  `cod_local` varchar(20) collate latin1_spanish_ci default NULL,
  `prec_valor` decimal(10,0) default NULL,
  `car_estadocarga` tinyint(3) unsigned default NULL,
  `id_cadena` tinyint(3) unsigned default NULL,
  `id_cod_error` int(10) unsigned default NULL,
  `id_orden` int(10) unsigned default NULL,
  `prec_costo` decimal(14,2) default NULL,
  `stock` decimal(10,0) default NULL,
  KEY `trp_precios_index10273` (`cod_prod1`),
  KEY `trp_precios_index10362` (`cod_local`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `trp_productos`
--

DROP TABLE IF EXISTS `trp_productos`;
CREATE TABLE `trp_productos` (
  `cod_prod1` varchar(20) collate latin1_spanish_ci default NULL,
  `cod_prod2` varchar(20) collate latin1_spanish_ci default NULL,
  `des_corta` varchar(40) collate latin1_spanish_ci default NULL,
  `des_larga` varchar(200) collate latin1_spanish_ci default NULL,
  `cod_categ` varchar(10) collate latin1_spanish_ci default NULL,
  `cod_propal` varchar(20) collate latin1_spanish_ci default NULL,
  `prod_tipo` char(2) collate latin1_spanish_ci default NULL,
  `prod_subtipo` char(2) collate latin1_spanish_ci default NULL,
  `stock_proveedor` decimal(10,0) default NULL,
  `car_estadocarga` char(1) collate latin1_spanish_ci default NULL,
  `id_cod_error` int(10) unsigned default NULL,
  `cod_categ_ant` varchar(10) collate latin1_spanish_ci default NULL,
  `id_cadena` tinyint(3) unsigned default NULL,
  KEY `trp_productos_index8495` (`car_estadocarga`),
  KEY `trp_productos_index8504` (`cod_prod1`,`id_cadena`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `trp_prodxprov`
--

DROP TABLE IF EXISTS `trp_prodxprov`;
CREATE TABLE `trp_prodxprov` (
  `cod_prod1` varchar(20) collate latin1_spanish_ci default NULL,
  `cod_prov` varchar(20) collate latin1_spanish_ci default NULL,
  `car_estadocarga` char(1) collate latin1_spanish_ci default NULL,
  `id_cod_error` int(10) unsigned default NULL,
  `id_cadena` tinyint(3) unsigned default NULL,
  KEY `trp_prodxprov_ind_cod_prod1` (`cod_prod1`),
  KEY `trp_prodxprov_ind_cod_prov` (`cod_prov`),
  KEY `trp_prodxprov_index8508` (`id_cadena`,`cod_prod1`),
  KEY `trp_prodxprov_index8509` (`car_estadocarga`),
  KEY `trp_prodxprov_index8510` (`cod_prod1`,`cod_prov`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `trp_promoscat`
--

DROP TABLE IF EXISTS `trp_promoscat`;
CREATE TABLE `trp_promoscat` (
  `id_catprod` varchar(20) collate latin1_spanish_ci NOT NULL default '',
  `id_cadena` tinyint(3) unsigned NOT NULL default '0',
  `id_promocion` int(10) unsigned NOT NULL default '0',
  `estado_disp` char(1) collate latin1_spanish_ci default NULL,
  PRIMARY KEY  (`id_catprod`,`id_cadena`,`id_promocion`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `trp_proveedores`
--

DROP TABLE IF EXISTS `trp_proveedores`;
CREATE TABLE `trp_proveedores` (
  `codprov` varchar(20) collate latin1_spanish_ci NOT NULL default '',
  `rutprov` varchar(10) collate latin1_spanish_ci NOT NULL default '',
  `nomprov` varchar(30) collate latin1_spanish_ci default NULL,
  `razsoc` varchar(60) collate latin1_spanish_ci default NULL,
  `fonocto` varchar(20) collate latin1_spanish_ci default NULL,
  `nombcto` varchar(50) collate latin1_spanish_ci default NULL,
  `emailcto` varchar(30) collate latin1_spanish_ci default NULL,
  `car_estadocarga` char(1) collate latin1_spanish_ci default NULL,
  `id_cod_error` int(10) unsigned default NULL,
  KEY `trp_proveedores_index8497` (`car_estadocarga`),
  KEY `trp_proveedores_index8506` (`codprov`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `unidmed`
--

DROP TABLE IF EXISTS `unidmed`;
CREATE TABLE `unidmed` (
  `unid_med` char(3) character set latin1 collate latin1_spanish_ci NOT NULL default '',
  `ind_decimal` tinyint(3) unsigned default '0',
  PRIMARY KEY  (`unid_med`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `USR_ID` int(8) NOT NULL auto_increment,
  `USR_NOMBRES` varchar(255) collate latin1_spanish_ci default NULL,
  `USR_APELLIDOS` varchar(255) collate latin1_spanish_ci default NULL,
  `USR_EMAIL` varchar(255) collate latin1_spanish_ci default NULL,
  `USR_LOGIN` varchar(255) collate latin1_spanish_ci default NULL,
  `USR_CLAVE` varchar(255) collate latin1_spanish_ci default NULL,
  `USR_ESTADO` int(2) default NULL,
  `USR_TIPO` int(1) default NULL,
  `USR_ULT_LOGIN` datetime default NULL,
  `USR_EST_LOGIN` int(2) default NULL,
  `USR_PUESTO` varchar(255) collate latin1_spanish_ci default NULL,
  `USR_DEPTO` varchar(255) collate latin1_spanish_ci default NULL,
  `USR_ORGANIZACION` varchar(255) collate latin1_spanish_ci default NULL,
  `USR_FONO` varchar(255) collate latin1_spanish_ci default NULL,
  `USR_FAX` varchar(255) collate latin1_spanish_ci default NULL,
  `USR_WEB` varchar(255) collate latin1_spanish_ci default NULL,
  `USR_CALLE` varchar(255) collate latin1_spanish_ci default NULL,
  `USR_CIUDAD` varchar(255) collate latin1_spanish_ci default NULL,
  `USR_PROVINCIA` varchar(255) collate latin1_spanish_ci default NULL,
  `USR_COD_POS` varchar(255) collate latin1_spanish_ci default NULL,
  `USR_PAIS` varchar(255) collate latin1_spanish_ci default NULL,
  `USR_DAT_EXTRAS` text collate latin1_spanish_ci,
  `USR_USR_CREA` varchar(255) collate latin1_spanish_ci default NULL,
  `USR_FEC_CREA` datetime default NULL,
  `USR_USR_MOD` varchar(255) collate latin1_spanish_ci default NULL,
  `USR_FEC_MOD` datetime default NULL,
  `impresora` varchar(45) collate latin1_spanish_ci default NULL,
  `USR_ORIGEN` int(10) unsigned default NULL,
  PRIMARY KEY  (`USR_ID`),
  KEY `usr_index1` (`USR_ORIGEN`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Table structure for table `ventaxvolumenps`
--

DROP TABLE IF EXISTS `ventaxvolumenps`;
CREATE TABLE `ventaxvolumenps` (
  `id_ventaXvolumen` int(11) NOT NULL auto_increment,
  `cantidadventaXvolumen` int(11) default NULL,
  `cod_sap` int(11) default NULL,
  `cod_proveedor` int(11) default NULL,
  `numero_dias` int(11) default NULL,
  `nombre_producto` varchar(255) default NULL,
  `nombre_proveedor` varchar(255) default NULL,
  PRIMARY KEY  (`id_ventaXvolumen`),
  UNIQUE KEY `codigo_unico` (`cod_proveedor`,`cod_sap`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping routines for database 'cpprod'
--
DELIMITER ;;
/*!50003 DROP PROCEDURE IF EXISTS `sp_elimina_clientes_incompletos` */;;
/*!50003 SET SESSION SQL_MODE=""*/;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_elimina_clientes_incompletos`()
BEGIN
  DELETE FROM cpprod.clientes
  WHERE ( ((TRIM(clie_nombre))='') OR ((TRIM(clie_nombre)) is NULL) )
 AND ( ((TRIM(clie_paterno)='') OR (TRIM(clie_paterno) is NULL)) AND ((TRIM(clie_materno)='') OR (TRIM(clie_materno) is NULL)) );
END */;;
/*!50003 SET SESSION SQL_MODE=@OLD_SQL_MODE*/;;
DELIMITER ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

