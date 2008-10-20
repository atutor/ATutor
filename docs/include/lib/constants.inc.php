<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
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
$_config_defaults['email_notification']        = 1; // enabled
$_config_defaults['allow_instructor_requests'] = 1; // enabled
$_config_defaults['auto_approve_instructors']  = 0; // disabled
$_config_defaults['max_file_size']             = 1048576;  // 1MB
$_config_defaults['max_course_size']           = 10485760; // 10 MB
$_config_defaults['max_course_float']          = 2097152;  // 2MB
$_config_defaults['illegal_extentions']        = 'exe|asp|php|php3|bat|cgi|pl|com|vbs|reg|pcd|pif|scr|bas|inf|vb|vbe|wsc|wsf|wsh';
$_config_defaults['site_name']                 = '';
$_config_defaults['home_url']                  = ''; // empty means disabled
$_config_defaults['default_language']          = 'en';
$_config_defaults['allow_registration']        = 1;
$_config_defaults['allow_unenroll']            = 1;
$_config_defaults['cache_dir']                 = ''; // empty means disabled
$_config_defaults['enable_category_themes']    = 0; // disabled
$_config_defaults['course_backups']            = 5; // number of backups
$_config_defaults['email_confirmation']        = 0; // disabled
$_config_defaults['master_list']               = 0; // disabled
$_config_defaults['user_notes']                = 0; // disabled - whether to enable the user contributed handbook notes
$_config_defaults['theme_categories']          = 0; // disabled
$_config_defaults['main_defaults']	           = 'forum/list.php|glossary/index.php|file_storage/index.php';
$_config_defaults['home_defaults']             = 'forum/list.php|file_storage/index.php|glossary/index.php|chat/index.php|tile.php|faq/index.php|links/index.php|tools/my_tests.php|sitemap.php|export.php|my_stats.php|polls/index.php|directory.php|groups.php|reading_list/index.php|blogs/index.php';
$_config_defaults['side_defaults']             = 'menu_menu|related_topics|users_online|glossary|search|poll|posts';
$_config_defaults['pref_defaults']			   = 'a:34:{s:14:"PREF_NUMBERING";i:1;s:10:"PREF_THEME";s:7:"default";s:13:"PREF_TIMEZONE";s:0:"";s:18:"PREF_JUMP_REDIRECT";i:1;s:15:"PREF_FORM_FOCUS";i:1;s:19:"PREF_CONTENT_EDITOR";i:0;s:15:"PREF_SHOW_GUIDE";i:1;s:14:"PREF_FONT_FACE";s:0:"";s:15:"PREF_FONT_TIMES";s:1:"1";s:14:"PREF_FG_COLOUR";s:0:"";s:14:"PREF_BG_COLOUR";s:0:"";s:14:"PREF_HL_COLOUR";s:0:"";s:28:"PREF_USE_ALTERNATIVE_TO_TEXT";i:0;s:16:"PREF_ALT_TO_TEXT";s:5:"audio";s:34:"PREF_ALT_TO_TEXT_APPEND_OR_REPLACE";s:6:"append";s:25:"PREF_ALT_TEXT_PREFER_LANG";s:2:"en";s:29:"PREF_USE_ALTERNATIVE_TO_AUDIO";i:0;s:17:"PREF_ALT_TO_AUDIO";s:4:"text";s:35:"PREF_ALT_TO_AUDIO_APPEND_OR_REPLACE";s:6:"append";s:26:"PREF_ALT_AUDIO_PREFER_LANG";s:2:"en";s:30:"PREF_USE_ALTERNATIVE_TO_VISUAL";i:0;s:18:"PREF_ALT_TO_VISUAL";s:4:"text";s:36:"PREF_ALT_TO_VISUAL_APPEND_OR_REPLACE";s:6:"append";s:27:"PREF_ALT_VISUAL_PREFER_LANG";s:2:"en";s:15:"PREF_DICTIONARY";i:1;s:14:"PREF_THESAURUS";i:1;s:16:"PREF_NOTE_TAKING";i:1;s:15:"PREF_CALCULATOR";i:1;s:11:"PREF_ABACUS";i:1;s:10:"PREF_ATLAS";i:1;s:17:"PREF_ENCYCLOPEDIA";i:1;s:18:"PREF_SHOW_CONTENTS";i:1;s:31:"PREF_SHOW_NEXT_PREVIOUS_BUTTONS";s:1:"1";s:22:"PREF_SHOW_BREAD_CRUMBS";s:1:"1";}';
$_config_defaults['pref_inbox_notify']		   = 0; // disabled
$_config_defaults['pref_is_auto_login']		   = "disable"; // disabled
$_config_defaults['check_version']	           = 0; // disabled
$_config_defaults['fs_versioning']             = 1; // enabled - file storage version control
$_config_defaults['last_cron']                 = 0; // cron has to be enabled manually
$_config_defaults['enable_mail_queue']         = 0; // mail queue can only be enabled if cron is running
$_config_defaults['auto_install_languages']    = 0; // auto install languages can only be enabled if cron is running
$_config_defaults['display_name_format']       = 1; // 0-5, see (array) display_name_formats
$_config_defaults['time_zone']                 = ''; // empty means disabled or not supported
$_config_defaults['prof_pic_max_file_size']	   = 819200; // max size of an uploaded profile pic, in bytes. default 800 KB
$_config_defaults['sent_msgs_ttl']             = 120; // number of days till saved sent inbox msgs are deleted
$_config_defaults['mysql_group_concat_max_len'] = null; // null = check, 0 = disabled/unsupported, (non-zero is the actual mysql value)
$_config_defaults['latex_server']              = 'http://www.forkosh.dreamhost.com/mimetex.cgi?'; // the full URL to an external LaTeX parser
$_config_defaults['gtype']					   = 0;	//Defaulted to be original google search, @author Harris
$_config_defaults['pretty_url']				   = 0;	//pretty url, disabled
$_config_defaults['course_dir_name']		   = 0;	//course dir name (course slug), disabled
$_config_defaults['apache_mod_rewrite']		   = 0;	//apache mod_rewrite extension, disabled by default.
$_config = $_config_defaults;


/* display name formats
 * L = login name
 * F = first name
 * S = second name
 * T = last name */
$display_name_formats = array();
$display_name_formats[0] = 'display_name_format_l';
$display_name_formats[1] = 'display_name_format_fst';
$display_name_formats[2] = 'display_name_format_fstl';
$display_name_formats[3] = 'display_name_format_fl';
$display_name_formats[4] = 'display_name_format_lf';
$display_name_formats[5] = 'display_name_format_lfst';


/* the atutor.ca language translation server: */
define('AT_LANG_DB_HOST', 'atutorsvn.atrc.utoronto.ca');
define('AT_LANG_DB_USER', 'read_dev_lang');
define('AT_LANG_DB_PASS', 'read_dev_lang');

/* links */
define('LINK_CAT_COURSE',	1);
define('LINK_CAT_GROUP',	2);
define('LINK_CAT_SELF',		3);

define('LINK_CAT_AUTH_NONE',	0);
define('LINK_CAT_AUTH_ALL',		1);
define('LINK_CAT_AUTH_COURSE',  2);
define('LINK_CAT_AUTH_GROUP',	3);

/* drafting room constants */
define('WORKSPACE_COURSE',     1); // aka Course Files
define('WORKSPACE_PERSONAL',   2); // aka My Files
define('WORKSPACE_ASSIGNMENT', 3);
define('WORKSPACE_GROUP',      4);
define('WORKSPACE_SYSTEM',     5);
define('WORKSPACE_PATH_DEPTH', 1); // how deep the directories should be
define('WORKSPACE_FILE_PATH',  AT_CONTENT_DIR . 'file_storage/');

/* how many related topics can be listed */
define('NUM_RELATED_TOPICS', 5);

/* how many days until the password reminder link expires */
define('AT_PASSWORD_REMINDER_EXPIRY', 2);

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

/* Handles pretty url - @author Harris */
define('AT_PRETTY_URL_HANDLER',		'go.php');	
define('AT_PRETTY_URL_NOT_HEADER',	false);
define('AT_PRETTY_URL_IS_HEADER',	true);

$dir_deep	 = substr_count(AT_INCLUDE_PATH, '..');
$url_parts	 = explode('/', $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
$_base_href	 = array_slice($url_parts, 0, count($url_parts) - $dir_deep-1);
$_base_href	 = $server_protocol . implode('/', $_base_href).'/';

if (($temp = strpos($_base_href, AT_PRETTY_URL_HANDLER)) > 0){
	$endpos = $temp;
} else {
	$endpos = strlen($_base_href); 

}
$_base_href	 = substr($_base_href, 0, $endpos);
$_base_path  = substr($_base_href, strlen($server_protocol . $_SERVER['HTTP_HOST']));

define('AT_BASE_HREF', $_base_href);

/* relative uri */
$_rel_url = '/'.implode('/', array_slice($url_parts, count($url_parts) - $dir_deep-1));

/* where the gudes are (could be a full URL if needed): */
define('AT_GUIDES_PATH', $_base_path . 'documentation/');

define('AT_BACKUP_DIR', AT_CONTENT_DIR . 'backups/'); // where the backups get stored

define('VERSION',		'1.6.2');
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
$editable_file_types = array('txt', 'html', 'htm', 'xml', 'css', 'asc', 'csv', 'sql');

/* how many poll choices are available: */
define('AT_NUM_POLL_CHOICES',   7);

/* ways of releasing a test */
define('AT_RELEASE_NEVER',		   0); // do not release 
define('AT_RELEASE_IMMEDIATE',	   1); // release after submitted
define('AT_RELEASE_MARKED',		   2); // release after all q's marked

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

$_field_formatting['messages.subject']			= AT_FORMAT_EMOTICONS + AT_FORMAT_IMAGES;
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

$_field_formatting['blog_posts.body']	     = AT_FORMAT_ALL & ~AT_FORMAT_HTML;
$_field_formatting['blog_posts.title']	     = AT_FORMAT_NONE;

$_field_formatting['blog_posts_comments.comment'] = AT_FORMAT_ALL & ~AT_FORMAT_HTML;

$_field_formatting['courses.banner'] = AT_FORMAT_ALL;

if (isset($_GET['cid'])) {
	$cid = intval($_GET['cid']);
} else if (isset($_POST['cid'])) {
	$cid = intval($_POST['cid']);
}


/* google type constants - @author Harris */
define('GOOGLE_TYPE_SOAP',		0);		//The original soap search with key generated before Dec 2005.
define('GOOGLE_TYPE_AJAX',		1);		//The new AJAX search by google

/* flags for validate_length in vitals. - @author Harris*/
define('VALIDATE_LENGTH_FOR_DISPLAY',	1);	
?>
