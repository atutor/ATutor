-- MySQL dump 10.10
--
-- Host: localhost    Database: atutor
-- ------------------------------------------------------
-- Server version	5.0.26

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
-- Table structure for table `AT_admin_log`
--

--
-- Table structure for table `AT_auth_cookie`
--

DROP TABLE IF EXISTS `AT_auth_cookie`;
CREATE TABLE `AT_auth_cookie` (
  `aid` int(11) NOT NULL auto_increment,
  `hash` varchar(255) NOT NULL,
  `ttl` int(11) NOT NULL,
  PRIMARY KEY  (`aid`)
) ENGINE=MyISAM AUTO_INCREMENT=199 DEFAULT CHARSET=latin1;

--
-- Table structure for table `AT_config_ldap`
--

DROP TABLE IF EXISTS `AT_config_ldap`;
CREATE TABLE `AT_config_ldap` (
  `name` varchar(30) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dump data for table `AT_config_ldap`
--

LOCK TABLES `AT_config_ldap` WRITE;
INSERT INTO `AT_config_ldap` VALUES ('ldap_name',''),('ldap_port','389'),('ldap_base_tree',''),('ldap_attr_login',''),('ldap_attr_password','__unused_ldap_field__'),('ldap_attr_mail',''),('ldap_attr_last_name',''),('ldap_attr_first_name',''),('ldap_attr_second_name',''),('ldap_attr_dob',''),('ldap_attr_gender',''),('ldap_attr_address',''),('ldap_attr_postal',''),('ldap_attr_city',''),('ldap_attr_province',''),('ldap_attr_country',''),('ldap_attr_phone',''),('ldap_attr_website','');
UNLOCK TABLES;



--
-- Table structure for table `AT_ldap_log`
--

DROP TABLE IF EXISTS `AT_ldap_log`;
CREATE TABLE `AT_ldap_log` (
  `member_id` mediumint(9) NOT NULL,
  `create_date` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


--
-- Dump data for table `AT_language_text`
--

LOCK TABLES `AT_language_text` WRITE;
/*!40000 ALTER TABLE `AT_language_text` DISABLE KEYS */;
INSERT INTO `AT_language_text` VALUES 
('en','_template','ldap_name','LDAP Server name','2007-04-27 00:00:00','atutor_ldap_mod'),
('en','_template','ldap_port','LDAP Server port','2007-04-27 00:00:00','atutor_ldap_mod'),
('en','_template','ldap_base_tree','LDAP Server tree <br/><small> Base of LDAP tree where stored users entries</small>','2007-04-27 00:00:00','atutor_ldap_mod'),
('en','_template','ldap_attr','LDAP Server fields <br/> <small> You must specify name of LDAP attributes which associated to ATutor members DB fields </small>','2007-04-27 00:00:00','atutor_ldap_mod'),
('en','_template','ldap_attr_address','Address','2007-04-27 00:00:00','atutor_ldap_mod'),
('en','_template','ldap_attr_city','City','2007-04-27 00:00:00','atutor_ldap_mod'),
('en','_template','ldap_attr_country','Country','2007-04-27 00:00:00','atutor_ldap_mod'),
('en','_template','ldap_attr_dob','DOB (Date Of Birth)','2007-04-27 00:00:00','atutor_ldap_mod'),
('en','_template','ldap_attr_first_name','Fisrt name','2007-04-27 00:00:00','atutor_ldap_mod'),
('en','_template','ldap_attr_gender','Gender','2007-04-27 00:00:00','atutor_ldap_mod'),
('en','_template','ldap_attr_last_name','Last name','2007-04-27 00:00:00','atutor_ldap_mod'),
('en','_template','ldap_attr_login','Login ','2007-04-27 00:00:00','atutor_ldap_mod'),
('en','_template','ldap_attr_mail','E-mail','2007-04-27 00:00:00','atutor_ldap_mod'),
('en','_template','ldap_attr_phone','Phone','2007-04-27 00:00:00','atutor_ldap_mod'),
('en','_template','ldap_attr_postal','Postal','2007-04-27 00:00:00','atutor_ldap_mod'),
('en','_template','ldap_attr_province','Province','2007-04-27 00:00:00','atutor_ldap_mod'),
('en','_template','ldap_attr_second_name','Second name','2007-04-27 00:00:00','atutor_ldap_mod'),
('en','_template','ldap_attr_website','Website','2007-04-27 00:00:00','atutor_ldap_mod'),
('en','_template','config_ldap','LDAP Authentication','2007-04-27 00:00:00','atutor_ldap_mod'),
('en','_template','ldap_auth_log','LDAP Auth Log','2007-04-27 00:00:00','atutor_ldap_mod');
('uk','_template','ldap_name','Імя LDAP-сервера','2007-04-27 00:00:00','atutor_ldap_mod'),
('uk','_template','ldap_port','Порт LDAP-сервера','2007-04-27 00:00:00','atutor_ldap_mod'),
('uk','_template','ldap_base_tree','Дерево LDAP-каталога<br/><small> Ви повинні вказати дерево LDAP-каталога, де зберігаються записи </small>','2007-04-27 00:00:00','atutor_ldap_mod'),
('uk','_template','ldap_attr','Атрибути LDAP-запису <br/> <small> Ви повинні вказати атрибути запису в LDAP-каталозі, що відповідають полям в таблиці members БД ATutor</small>','2007-04-27 00:00:00','atutor_ldap_mod'),
('uk','_template','ldap_attr_address','Адреса','2007-04-27 00:00:00','atutor_ldap_mod'),
('uk','_template','ldap_attr_city','Місто','2007-04-27 00:00:00','atutor_ldap_mod'),
('uk','_template','ldap_attr_country','Країна','2007-04-27 00:00:00','atutor_ldap_mod'),
('uk','_template','ldap_attr_dob','Дата народження','2007-04-27 00:00:00','atutor_ldap_mod'),
('uk','_template','ldap_attr_first_name','Імя','2007-04-27 00:00:00','atutor_ldap_mod'),
('uk','_template','ldap_attr_gender','Стать','2007-04-27 00:00:00','atutor_ldap_mod'),
('uk','_template','ldap_attr_last_name','Прізвище','2007-04-27 00:00:00','atutor_ldap_mod'),
('uk','_template','ldap_attr_login','Імя для входу','2007-04-27 00:00:00','atutor_ldap_mod'),
('uk','_template','ldap_attr_mail','Адреса електронної пошти','2007-04-27 00:00:00','atutor_ldap_mod'),
('uk','_template','ldap_attr_phone','Номер телефону','2007-04-27 00:00:00','atutor_ldap_mod'),
('uk','_template','ldap_attr_postal','Поштовий індекс','2007-04-27 00:00:00','atutor_ldap_mod'),
('uk','_template','ldap_attr_province','Область','2007-04-27 00:00:00','atutor_ldap_mod'),
('uk','_template','ldap_attr_second_name','По батькові','2007-04-27 00:00:00','atutor_ldap_mod'),
('uk','_template','ldap_attr_website','Сайт','2007-04-27 00:00:00','atutor_ldap_mod'),
('uk','_template','config_ldap','LDAP-Автентифікація','2007-04-27 00:00:00','atutor_ldap_mod'),
('uk','_template','ldap_auth_log','Лог LDAP-Автентифікації','2007-04-27 00:00:00','atutor_ldap_mod');

/*!40000 ALTER TABLE `AT_language_text` ENABLE KEYS */;
UNLOCK TABLES;

