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

if (!$_REQUEST['f']) {
	$_REQUEST['f']	= 'en';
}

$page = 'translate';
$_user_location = 'public';
$page_title = 'ATutor: LCMS: Translation';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
//require(AT_INCLUDE_PATH.'language/languages.inc.php');


//define variables to be used
global $db;
$_INCLUDE_PATH = AT_INCLUDE_PATH;
$_TABLE_PREFIX = TABLE_PREFIX;
$_TABLE_SUFFIX = '';

if ($_REQUEST['lang_code']) {
	$_SESSION['language'] = $_REQUEST['lang_code'];
}

require ($_INCLUDE_PATH.'header.inc.php');
echo '<h3>ATutor Translator Site</h3>';

$variables = array('_template','_msgs');

$atutor_test = '<a href="'.$_base_href.'" title="Open ATutor in a new window" target="new">';

require_once('translator.php');


?>