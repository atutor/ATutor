<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }

/*******************
 * constants
 ******/
/* used for the collapse/expand as well as open/close */
define('MENU_CLOSE',        0);	/* also: DISABLE, COLLAPSE */
define('MENU_OPEN',         1); /* also: ENABLE,  EXPAND  */
define('OPEN',				1); /* also: ENABLE,  EXPAND  */
define('CLOSE',				0); /* also: ENABLE,  EXPAND  */

define('NONE',				0);
define('TOP',				1);
define('BOTTOM',			2);
define('BOTH',				3);

define('MENU_RIGHT',		0); /* the location of the menu */
define('MENU_LEFT',			1);

/* how many related topics can be listed? */
define('NUM_RELATED_TOPICS', 5);

/* how long cache objects can persist		*/
/* in seconds. should be low initially, but doesn't really matter. */
/* in practice should be 0 (ie. INF)				*/
define('CACHE_TIME_OUT',	60);

// colours[0] = array('NAME' => 'fancy blue', 'FILE' => 'blue');
// not translated, to be recreated in theme builder
$_colours[0]['NAME'] = 'ATutor Original';
$_colours[0]['FILE'] = 'stylesheet';
$_colours[1]['NAME'] = 'Chrome';
$_colours[1]['FILE'] = 'chrome';
$_colours[2]['NAME'] = 'Dusty Rose';
$_colours[2]['FILE'] = 'pink';
$_colours[3]['NAME'] = 'Faded Green';
$_colours[3]['FILE'] = 'green';
$_colours[4]['NAME'] = 'Fancy Blue';
$_colours[4]['FILE'] = 'blue';
$_colours[5]['NAME'] = 'Coloured';
$_colours[5]['FILE'] = 'multi';
$_colours[6]['NAME'] = 'Colour High Contrast';
$_colours[6]['FILE'] = 'high';
$_colours[7]['NAME'] = 'Mono High Contrast';
$_colours[7]['FILE'] = 'high2';


$_fonts[0]['NAME'] = 'Verdana';
$_fonts[0]['FILE'] = 'verdana';
$_fonts[1]['NAME'] = 'Helvetica';
$_fonts[1]['FILE'] = 'helvetica';
$_fonts[2]['NAME'] = 'Times New Roman';
$_fonts[2]['FILE'] = 'times';
$_fonts[3]['NAME'] = 'Courier New';
$_fonts[3]['FILE'] = 'courier';
$_fonts[4]['NAME'] = 'Garamond';
$_fonts[4]['FILE'] = 'garamond';
$_fonts[5]['NAME'] = 'Comic';
$_fonts[5]['FILE'] = 'comic';
$_fonts[6]['NAME'] = 'Arial';
$_fonts[6]['FILE'] = 'arial';


if (strpos(@ini_get('arg_separator.input'), ';') !== false) {
	define('SEP', ';');
} else {
	define('SEP', '&');
}

$PHP_SELF		= $_SERVER['PHP_SELF'];
if (!isset($_SERVER['REQUEST_URI'])) {
	$REQUEST_URI	= $_SERVER['SCRIPT_NAME'];
	if ($_SERVER['QUERY_STRING'] != '') {
		$REQUEST_URI .= '?'.$_SERVER['QUERY_STRING'];
	}
	$_SERVER['REQUEST_URI'] = $REQUEST_URI;
}

/* get the base url									*/
$dir_deep		= substr_count(AT_INCLUDE_PATH, '..');
$current_url	= $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
$url_parts		= explode('/', $current_url);
$_base_href		= array_slice($url_parts, 0, count($url_parts) - $dir_deep-1);
$_base_href		= 'http://'.implode('/', $_base_href).'/';

$_base_path = substr($_base_href, strlen('http://'.$_SERVER['HTTP_HOST']));
/******************/


@ini_set('session.gc_maxlifetime', '36000'); /* 10 hours */

define('HELP',			0);
define('VERSION',		'1.3.2');
define('ONLINE_UPDATE', 3); /* update the user expiry every 3 min */

/* valid date format_types:						*/
/* @see ./include/lib/date_functions.inc.php	*/
define('AT_DATE_MYSQL_DATETIME',		1); /* YYYY-MM-DD HH:MM:SS	*/
define('AT_DATE_MYSQL_TIMESTAMP_14',	2); /* YYYYMMDDHHMMSS		*/
define('AT_DATE_UNIX_TIMESTAMP',		3); /* seconds since epoch	*/
define('AT_DATE_INDEX_VALUE',			4); /* index to the date arrays */

define('AT_KBYTE_SIZE',		1024);

define('AT_DEFAULT_THEME',		4); /* must match the theme_id in the theme_settings table */

define('AT_COURSESIZE_UNLIMITED',	-1); 
define('AT_COURSESIZE_DEFAULT',		-2);  /* can be changed in config.inc.php */
define('AT_FILESIZE_DEFAULT',		-3);  /* this too */
define('AT_FILESIZE_SYSTEM_MAX',	-4);

/* names of the include files, the index IS important, so DO NOT change the order! */
$_stacks = array('local_menu', 'menu_menu', 'related_topics', 'users_online', 'glossary', 'search');

$_rtl_languages = array('ar', 'fa', 'he');

	if (isset($_GET['cid'])) {
		$cid = intval($_GET['cid']);
	} else if (isset($_POST['cid'])) {
		$cid = intval($_POST['cid']);
	}

?>