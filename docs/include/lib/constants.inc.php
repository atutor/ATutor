<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: constants.inc.php,v 1.26 2004/03/05 19:02:36 heidi Exp $

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

/* how many announcements listed */
define('NUM_ANNOUNCEMENTS', 10);

/* how long cache objects can persist		*/
/* in seconds. should be low initially, but doesn't really matter. */
/* in practice should be 0 (ie. INF)				*/
define('CACHE_TIME_OUT',	60);

/* user permissions */

/* $_privs[priv number] = array(String name, Boolean pen, Boolean tools) */

define('AT_PRIV_ADMIN',			true);
define('AT_PRIV_CHECK',			true);
define('AT_PRIV_NONE',			0);

$_privs[1]		= array('name' => 'AT_PRIV_CONTENT',		'pen' => true,	'tools' => true);
$_privs[2]		= array('name' => 'AT_PRIV_GLOSSARY',		'pen' => true,	'tools' => false);
$_privs[4]		= array('name' => 'AT_PRIV_TEST_CREATE',	'pen' => false, 'tools' => true);
$_privs[8]		= array('name' => 'AT_PRIV_TEST_MARK',		'pen' => false, 'tools' => true);
$_privs[16]		= array('name' => 'AT_PRIV_FILES',			'pen' => false, 'tools' => true);
$_privs[32]		= array('name' => 'AT_PRIV_LINKS',			'pen' => true,	'tools' => false);
$_privs[64]		= array('name' => 'AT_PRIV_FORUMS',			'pen' => true,	'tools' => false);
$_privs[128]	= array('name' => 'AT_PRIV_STYLES',			'pen' => false, 'tools' => true);
$_privs[256]	= array('name' => 'AT_PRIV_ENROLLMENT',		'pen' => false,	'tools' => false);
$_privs[512]	= array('name' => 'AT_PRIV_COURSE_EMAIL',	'pen' => false,	'tools' => false);
$_privs[1024]	= array('name' => 'AT_PRIV_ANNOUNCEMENTS',	'pen' => true,	'tools' => false);


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

if (!isset($_SERVER['REQUEST_URI'])) {
	$REQUEST_URI = $_SERVER['SCRIPT_NAME'];
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

/* relative uri */
$_rel_url = '/'.implode('/', array_slice($url_parts, count($url_parts) - $dir_deep-1));

/******************/

define('HELP',			0);
define('VERSION',		'1.3.4');
define('ONLINE_UPDATE', 3); /* update the user expiry every 3 min */

/* valid date format_types:						*/
/* @see ./include/lib/date_functions.inc.php	*/
define('AT_DATE_MYSQL_DATETIME',		1); /* YYYY-MM-DD HH:MM:SS	*/
define('AT_DATE_MYSQL_TIMESTAMP_14',	2); /* YYYYMMDDHHMMSS		*/
define('AT_DATE_UNIX_TIMESTAMP',		3); /* seconds since epoch	*/
define('AT_DATE_INDEX_VALUE',			4); /* index to the date arrays */

define('AT_ROLE_STUDENT',				0);
define('AT_ROLE_INSTRUCTOR',			1);

define('AT_KBYTE_SIZE',		         1024);

define('AT_DEFAULT_THEME',		        4); /* must match the theme_id in the theme_settings table */

define('AT_COURSESIZE_UNLIMITED',	   -1); 
define('AT_COURSESIZE_DEFAULT',		   -2);  /* can be changed in config.inc.php */
define('AT_FILESIZE_DEFAULT',		   -3);  /* this too */
define('AT_FILESIZE_SYSTEM_MAX',	   -4);

/* names of the include files, the index IS important, so DO NOT change the order! */
$_stacks = array('local_menu', 'menu_menu', 'related_topics', 'users_online', 'glossary', 'search');

/* the languages that are right to left: */
/* arabic, farsi, hebrew, urdo */
$_rtl_languages = array('ar', 'fa', 'he', 'ur');

/* control how user inputs get formatted on output: */
/* note: v131 not all formatting options are available on each section. */

define('AT_FORMAT_NONE',	      0); /* LEQ to ~AT_FORMAT_ALL */
define('AT_FORMAT_EMOTICONS',     1);
define('AT_FORMAT_LINKS',         2);
define('AT_FORMAT_IMAGES',        4);
define('AT_FORMAT_HTML',          8);
define('AT_FORMAT_GLOSSARY',     16);
define('AT_FORMAT_LEARNING',     32);
define('AT_FORMAT_ATCODES',      64);
define('AT_FORMAT_CONTENT_DIR', 128); /* remove CONTENT_DIR */
define('AT_FORMAT_QUOTES',      256); /* remove double quotes */
define('AT_FORMAT_ALL',       AT_FORMAT_EMOTICONS 
							   + AT_FORMAT_LINKS 
						       + AT_FORMAT_IMAGES 
						       + AT_FORMAT_HTML 
						       + AT_FORMAT_GLOSSARY 
						       + AT_FORMAT_LEARNING
							   + AT_FORMAT_ATCODES
							   + AT_FORMAT_CONTENT_DIR);

$_field_formatting = array();

$_field_formatting['content.keywords']			= AT_FORMAT_NONE;
$_field_formatting['content.title']				= AT_FORMAT_ALL & ~AT_FORMAT_HTML | AT_FORMAT_QUOTES;
$_field_formatting['content.text']				= AT_FORMAT_ALL;

$_field_formatting['courses.*']				    = AT_FORMAT_ALL & ~AT_FORMAT_EMOTICONS & ~AT_FORMAT_ATCODES & ~AT_FORMAT_LINKS & ~AT_FORMAT_IMAGES;

$_field_formatting['forums.title']				= AT_FORMAT_NONE;
$_field_formatting['forums.description']		= AT_FORMAT_ALL & ~AT_FORMAT_LEARNING;

$_field_formatting['forums_threads.subject']	= AT_FORMAT_ALL & ~AT_FORMAT_HTML;
$_field_formatting['forums_threads.body']		= AT_FORMAT_ALL & ~AT_FORMAT_HTML;

$_field_formatting['glossary.word']				= AT_FORMAT_NONE;
$_field_formatting['glossary.definition']		= AT_FORMAT_ALL & ~AT_FORMAT_HTML;

$_field_formatting['instructor_approvals.notes']= AT_FORMAT_NONE;

$_field_formatting['members.*']                 = AT_FORMAT_NONE; /* wildcards are okay */

$_field_formatting['messages.subject']			= AT_FORMAT_EMOTICONS + AT_FORMAT_LINKS + AT_FORMAT_IMAGES;
$_field_formatting['messages.body']				= AT_FORMAT_EMOTICONS + AT_FORMAT_LINKS + AT_FORMAT_IMAGES + AT_FORMAT_ATCODES;

$_field_formatting['news.title']				= AT_FORMAT_EMOTICONS | AT_FORMAT_LINKS & ~AT_FORMAT_HTML;
$_field_formatting['news.body']					= AT_FORMAT_ALL;

$_field_formatting['resource_categories.CatName']	= AT_FORMAT_NONE;
$_field_formatting['resource_categories.Url']	= AT_FORMAT_NONE;
$_field_formatting['resource_links.LinkName']	= AT_FORMAT_NONE;
$_field_formatting['resource_links.Description']= AT_FORMAT_NONE;
$_field_formatting['resource_links.SubmitName']= AT_FORMAT_NONE;

$_field_formatting['tests.title']				= AT_FORMAT_ALL;
$_field_formatting['tests.instructions']		= AT_FORMAT_ALL;

$_field_formatting['tests_answers.answer']		= AT_FORMAT_ALL;
$_field_formatting['tests_answers.notes']		= AT_FORMAT_ALL;

$_field_formatting['tests_answers.notes']		= AT_FORMAT_ALL;


	if (isset($_GET['cid'])) {
		$cid = intval($_GET['cid']);
	} else if (isset($_POST['cid'])) {
		$cid = intval($_POST['cid']);
	}

?>