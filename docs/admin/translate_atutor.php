<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca						*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.		*/
/****************************************************************/

if (!defined('AT_DEVEL_TRANSLATE') && !AT_DEVEL_TRANSLATE) { exit; }

if (!$_REQUEST['f']) {
	$_REQUEST['f']	= 'en';
}

$page = 'translate';
$_user_location = 'public';
$page_title = 'ATutor: LCMS: Translation';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

//define variables to be used
global $db;
$_INCLUDE_PATH = AT_INCLUDE_PATH;
$_TABLE_PREFIX = TABLE_PREFIX;
$_TABLE_SUFFIX = '';

if ($_REQUEST['lang_code']) {
	$_SESSION['language'] = $_REQUEST['lang_code'];
}

require ($_INCLUDE_PATH.'header.inc.php');
?>

<STYLE type="text/css">
<!--
li {
	font-family: Verdana, Helvetica, sans-serif;
	font-size: small;
    /*line-height: 12pt; */
	padding-bottom: 5px; 
	padding: 2px;
}


ul {
	list-style-position: outside;
	list-style-image: url('/images/bullet.gif');
	margin-top: 0px;
}

.submit {
	background: #006699;
	color: white;
	border-right: white solid 1px;
	border-left: white solid 1px;
	border-top: white solid 1px;
	border-bottom: white solid 1px;
	padding: 1px;
	font-size: smaller;
}
h5.heading2 {
	letter-spacing: 3px;
	/* text-transform: uppercase;  */
	background-color: #eeeeee; 
	color: #006699; 
	font-weight: bold; 
	font-size: small; 
	padding-right: 3px; 
	padding-left: 3px; 
	text-align: center;
}
.submit {
	background: #006699;
	color: white;
	border-right: white solid 1px;
	border-left: white solid 1px;
	border-top: white solid 1px;
	border-bottom: white solid 1px;
	padding: 1px;
	font-size: smaller;
}
.selected {
 font-family : Arial, Helvetica, Arial Cyr, Arial Ua, sans-serif;
  font-size : 14px;
  color : black;
  background: #F2FF85;
  font-weight : bold;
  padding: 2px;
  width: 45%;
}
table.box {
	background-color: white;
	border-right: #006699 solid 1px;
	border-left: #006699 solid 1px;
	border-top: #006699 solid 1px;
}

table.box th, table.box td {
	border-bottom: #006699 solid 1px;
	font-size: smaller;
}

table.box .submit {
	background: #006699;
	color: white;
	font-size: smaller;
}

table.box .submit:hover {
	background: #0077AA;
	color: white;
	font-size: smaller;
}

-->
</STYLE>

<?php


echo '<h3>ATutor Translator Site</h3>';

$variables = array('_template','_msgs');

$atutor_test = '<a href="'.$_base_href.'" title="Open ATutor in a new window" target="new">';

$_SESSION['status'] = 2;
$_USER_ADMIN = $_SESSION['status'];

require_once('translator.php');


?>