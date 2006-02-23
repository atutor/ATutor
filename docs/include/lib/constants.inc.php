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
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }

/***************
 * constants
 ******/

/* config variables. if they're not in the db then it uses the installation default values: */
$_config_defaults = array();
$_config_defaults['contact_email']             = '';
$_config_defaults['email_notification']        = 1;
$_config_defaults['allow_instructor_requests'] = 1;
$_config_defaults['auto_approve_instructors']  = 0;
$_config_defaults['max_file_size']             = 1048576;  // 1MB
$_config_defaults['max_course_size']           = 10485760; // 10 MB
$_config_defaults['max_course_float']          = 2097152;  // 2MB
$_config_defaults['illegal_extentions']        = 'exe|asp|php|php3|bat|cgi|pl|com|vbs|reg|pcd|pif|scr|bas|inf|vb|vbe|wsc|wsf|wsh';
$_config_defaults['site_name']                 = '';
$_config_defaults['home_url']                  = '';
$_config_defaults['default_language']          = 'en';
$_config_defaults['cache_dir']                 = '';
$_config_defaults['enable_category_themes']    = 0;
$_config_defaults['course_backups']            = 5;
$_config_defaults['email_confirmation']        = 0;
$_config_defaults['master_list']               = 0;
$_config_defaults['enable_handbook_notes']     = 0;
$_config_defaults['theme_categories']          = 0;
$_config_defaults['main_defaults']	           = 'forum/list.php|glossary/index.php';
$_config_defaults['home_defaults']             = 'forum/list.php|glossary/index.php|chat/index.php|tile.php|faq/index.php|links/index.php|tools/my_tests.php|sitemap.php|export.php|my_stats.php|polls/index.php|directory.php';
$_config_defaults['side_defaults']             = 'menu_menu|related_topics|users_online|glossary|search|poll|posts';
$_config_defaults['pref_defaults']			   = 'a:4:{s:10:"PREF_THEME";s:7:"default";s:14:"PREF_NUMBERING";i:1;s:18:"PREF_JUMP_REDIRECT";i:1;s:15:"PREF_FORM_FOCUS";i:1;}';
$_config_defaults['pref_inbox_notify']		   = 0;
$_config_defaults['check_version']	           = 0;

$_config = $_config_defaults;

/* how many related topics can be listed */
define('NUM_RELATED_TOPICS', 5);

/* taking a test an unlimited # of times */
define('AT_TESTS_TAKE_UNLIMITED', 0);

/* how many announcements listed */
define('NUM_ANNOUNCEMENTS', 10);

/* how long cache objects can persist	*/
/* in seconds. should be low initially, but doesn't really matter. */
/* in practice should be 0 (ie. INF)    */
define('CACHE_TIME_OUT',	60);

/* member status field options */
define('AT_STATUS_DISABLED',    0);
define('AT_STATUS_UNCONFIRMED', 1);
define('AT_STATUS_STUDENT',     2);
define('AT_STATUS_INSTRUCTOR',  3);

/* $_pages sections */
define('AT_NAV_PUBLIC', 'AT_NAV_PUBLIC');
define('AT_NAV_START',  'AT_NAV_START');
define('AT_NAV_COURSE', 'AT_NAV_COURSE');
define('AT_NAV_HOME',   'AT_NAV_HOME');
define('AT_NAV_ADMIN',  'AT_NAV_ADMIN');

/* user permissions */

/* $_privs[priv number] = array(String name, Boolean pen, Boolean tools) */
define('AT_PRIV_RETURN',		true);
define('AT_PRIV_NONE',			0);

define('AT_PRIV_ADMIN',			1);

/* admin privs: */
define('AT_ADMIN_PRIV_NONE',        0);
define('AT_ADMIN_PRIV_ADMIN',       1);

/* admin log (type of operations) */
define('AT_ADMIN_LOG_UPDATE',  1);
define('AT_ADMIN_LOG_DELETE',  2);
define('AT_ADMIN_LOG_INSERT',  3);
define('AT_ADMIN_LOG_REPLACE', 4);
define('AT_ADMIN_LOG_OTHER',   5); //for non-db operations

if (strpos(@ini_get('arg_separator.input'), ';') !== false) {
	define('SEP', ';');
} else {
	define('SEP', '&');
}

/* the URL to the AChecker server of choice. must include trailing slash. */
define('AT_ACHECKER_URL', 'http://checker.atrc.utoronto.ca/servlet/');

if (!isset($_SERVER['REQUEST_URI'])) {
	$REQUEST_URI = $_SERVER['SCRIPT_NAME'];
	if ($_SERVER['QUERY_STRING'] != '') {
		$REQUEST_URI .= '?'.$_SERVER['QUERY_STRING'];
	}
	$_SERVER['REQUEST_URI'] = $REQUEST_URI;
}

/* get the base url	*/
if (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on')) {
	$server_protocol = 'https://';
} else {
	$server_protocol = 'http://';
}

$dir_deep	 = substr_count(AT_INCLUDE_PATH, '..');
$url_parts	 = explode('/', $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
$_base_href	 = array_slice($url_parts, 0, count($url_parts) - $dir_deep-1);
$_base_href	 = $server_protocol . implode('/', $_base_href).'/';
$_base_path  = substr($_base_href, strlen($server_protocol . $_SERVER['HTTP_HOST']));

/* relative uri */
$_rel_url = '/'.implode('/', array_slice($url_parts, count($url_parts) - $dir_deep-1));

/* where the gudes are (could be a full URL if needed): */
define('AT_GUIDES_PATH', $_base_path . 'documentation/');

define('AT_BACKUP_DIR', AT_CONTENT_DIR . 'backups/'); // where the backups get stored

define('VERSION',		'1.5.3');
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

define('AT_COURSESIZE_UNLIMITED',	   -1); 
define('AT_COURSESIZE_DEFAULT',		   -2);  /* can be changed in config.inc.php */
define('AT_FILESIZE_DEFAULT',		   -3);  /* this too */
define('AT_FILESIZE_SYSTEM_MAX',	   -4);
$editable_file_types = array('txt', 'html', 'htm', 'xml', 'css', 'asc', 'csv');

/* how many poll choices are available: */
define('AT_NUM_POLL_CHOICES',   7);

/* ways of releasing a test */
define('AT_RELEASE_NEVER',		   0); // do not release 
define('AT_RELEASE_IMMEDIATE',	   1); // release after submitted
define('AT_RELEASE_MARKED',		   2); // release after all q's marked

/* types of test questions */
define('AT_TESTS_MC',				1); // multiple choice
define('AT_TESTS_TF',				2); // true/false
define('AT_TESTS_LONG',				3); // long answer
define('AT_TESTS_LIKERT',			4); // likert

/* QPROP = question property: */
define('AT_TESTS_QPROP_WORD',       1);
define('AT_TESTS_QPROP_SENTENCE',   2);
define('AT_TESTS_QPROP_PARAGRAPH',  3);
define('AT_TESTS_QPROP_PAGE',       4);
define('AT_TESTS_QPROP_ALIGN_VERT',	5); // align question options vertically
define('AT_TESTS_QPROP_ALIGN_HORT',	6); // align question options horizontally

/* enrollment types for $_SESSION['enroll'] */
define('AT_ENROLL_NO',			0);
define('AT_ENROLL_YES',			1);
define('AT_ENROLL_ALUMNUS',		2);

//$_stacks = array('menu_menu', 'related_topics', 'users_online', 'glossary', 'search', 'poll', 'posts');

/* control how user inputs get formatted on output: */
/* note: v131 not all formatting options are available on each section. */

define('AT_FORMAT_NONE',	      0); /* LEQ to ~AT_FORMAT_ALL */
define('AT_FORMAT_EMOTICONS',     1);
define('AT_FORMAT_LINKS',         2);
define('AT_FORMAT_IMAGES',        4);
define('AT_FORMAT_HTML',          8);
define('AT_FORMAT_GLOSSARY',     16);
define('AT_FORMAT_ATCODES',      32);
define('AT_FORMAT_CONTENT_DIR', 64); /* remove CONTENT_DIR */
define('AT_FORMAT_QUOTES',      128); /* remove double quotes (does this get used?) */
define('AT_FORMAT_ALL',       AT_FORMAT_EMOTICONS 
							   + AT_FORMAT_LINKS 
						       + AT_FORMAT_IMAGES 
						       + AT_FORMAT_HTML 
						       + AT_FORMAT_GLOSSARY 
							   + AT_FORMAT_ATCODES
							   + AT_FORMAT_CONTENT_DIR);

$_field_formatting = array();

$_field_formatting['content.keywords']			= AT_FORMAT_NONE;
$_field_formatting['content.title']				= AT_FORMAT_ALL & ~AT_FORMAT_HTML | AT_FORMAT_QUOTES;
$_field_formatting['content.text']				= AT_FORMAT_ALL;

$_field_formatting['course_cats.cat_name']		= AT_FORMAT_NONE;

$_field_formatting['courses.*']				    = AT_FORMAT_ALL & ~AT_FORMAT_EMOTICONS & ~AT_FORMAT_ATCODES & ~AT_FORMAT_LINKS & ~AT_FORMAT_IMAGES;

$_field_formatting['forums.title']				= AT_FORMAT_NONE;
$_field_formatting['forums.description']		= AT_FORMAT_ALL;

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

$_field_formatting['resource_categories.CatName']= AT_FORMAT_NONE;
$_field_formatting['resource_categories.Url']	= AT_FORMAT_NONE;
$_field_formatting['resource_links.LinkName']	= AT_FORMAT_NONE;
$_field_formatting['resource_links.Description']= AT_FORMAT_NONE;
$_field_formatting['resource_links.SubmitName']= AT_FORMAT_NONE;

$_field_formatting['tests.title']				= AT_FORMAT_ALL;
$_field_formatting['tests.instructions']		= AT_FORMAT_ALL;

$_field_formatting['themes.title']				= AT_FORMAT_NONE;

$_field_formatting['tests_answers.answer']		= AT_FORMAT_NONE;
$_field_formatting['tests_answers.notes']		= AT_FORMAT_ALL;
$_field_formatting['tests_questions.*']			= AT_FORMAT_ALL;

$_field_formatting['tests_questions_categories.title']	= AT_FORMAT_NONE;

$_field_formatting['polls.*']            = AT_FORMAT_ALL;

if (isset($_GET['cid'])) {
	$cid = intval($_GET['cid']);
} else if (isset($_POST['cid'])) {
	$cid = intval($_POST['cid']);
}

// constants for reading list module
define ('RL_TYPE_BOOK', 1);
define ('RL_TYPE_URL',  2);
define ('RL_TYPE_HANDOUT', 3);
define ('RL_TYPE_AV', 4);
define ('RL_TYPE_FILE', 5);

$_rl_types = array ();
$_rl_types[RL_TYPE_BOOK]	= 'rl_book';
$_rl_types[RL_TYPE_URL]		= 'rl_url';
$_rl_types[RL_TYPE_HANDOUT]	= 'rl_handout';
$_rl_types[RL_TYPE_AV]		= 'rl_av';
$_rl_types[RL_TYPE_FILE]	= 'rl_file';

?>