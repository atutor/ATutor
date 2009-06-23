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

define('AT_DEVEL', 0);
define('AT_ERROR_REPORTING', E_ALL ^ E_NOTICE); // default is E_ALL ^ E_NOTICE, use E_ALL or E_ALL + E_STRICT for developing
define('AT_DEVEL_TRANSLATE', 0);

// Emulate register_globals off. src: http://php.net/manual/en/faq.misc.php#faq.misc.registerglobals
function unregister_GLOBALS() {
   if (!ini_get('register_globals')) { return; }

   // Might want to change this perhaps to a nicer error
   if (isset($_REQUEST['GLOBALS'])) { die('GLOBALS overwrite attempt detected'); }

   // Variables that shouldn't be unset
   $noUnset = array('GLOBALS','_GET','_POST','_COOKIE','_REQUEST','_SERVER','_ENV', '_FILES');
   $input = array_merge($_GET,$_POST,$_COOKIE,$_SERVER,$_ENV,$_FILES,isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array());
  
   foreach ($input as $k => $v) {
       if (!in_array($k, $noUnset) && isset($GLOBALS[$k])) { unset($GLOBALS[$k]); }
   }
}

/*
 * structure of this document (in order):
 *
 * 0. load config.inc.php
 * 1. load constants
 * 2. initilize session
 * 3. load language constants
 * 4. enable output compression
 * 5. initilize db connection
 * 6. load cache library
 * 7. initilize session localization
 * 8. load ContentManagement/output/Savant/Message libraries
 ***/

/**** 0. start system configuration options block ****/
	error_reporting(0);
	if (!defined('AT_REDIRECT_LOADED')){
		include_once(AT_INCLUDE_PATH.'config.inc.php');
	}
	error_reporting(AT_ERROR_REPORTING);

	if (!defined('AT_INSTALL') || !AT_INSTALL) {
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Pragma: no-cache');

		$relative_path = substr(AT_INCLUDE_PATH, 0, -strlen('include/'));
		header('Location: ' . $relative_path . 'install/not_installed.php');
		exit;
	}
/*** end system config block ****/

/*** 1. constants ***/
	if (!defined('AT_REDIRECT_LOADED')){
		require_once(AT_INCLUDE_PATH.'lib/constants.inc.php');
	}

/***** 2. start session initilization block ****/
	if (headers_sent()) {
		require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
		$err =& new ErrorHandler();
		trigger_error('VITAL#<br /><br /><code><strong>An error occurred. Output sent before it should have. Please correct the above error(s).' . '</strong></code><br /><hr /><br />', E_USER_ERROR);
	}

	@set_time_limit(0);
	@ini_set('session.gc_maxlifetime', '36000'); /* 10 hours */
	@session_cache_limiter('private, must-revalidate');
	session_name('ATutorID');
	error_reporting(AT_ERROR_REPORTING);

	if (headers_sent()) {
		require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
		$err =& new ErrorHandler();
		trigger_error('VITAL#<br /><code><strong>Headers already sent. ' .
						'Cannot initialise session.</strong></code><br /><hr /><br />', E_USER_ERROR);
		exit;
	}

	ob_start();
	session_set_cookie_params(0, $_base_path);
	session_start();
	$str = ob_get_contents();
	ob_end_clean();
	unregister_GLOBALS();

	if ($str) {
		require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
		$err =& new ErrorHandler();
		trigger_error('VITAL#<br /><code><strong>Error initializing session. ' .
						'Please varify that session.save_path is correctly set in your php.ini file ' .
						'and the directory exists.</strong></code><br /><hr /><br />', E_USER_ERROR);
		exit;
	}

	if (!isset($_SESSION['course_id']) && !isset($_SESSION['valid_user']) && (!isset($_user_location) || $_user_location != 'public') && !isset($_pretty_url_course_id)) {
		if (isset($in_get) && $in_get && (($pos = strpos($_SERVER['PHP_SELF'], 'get.php/')) !== FALSE)) {
			$redirect = substr($_SERVER['PHP_SELF'], 0, $pos) . 'login.php';
			header('Location: '.$redirect);
			exit;
		}

		header('Location: '.AT_BASE_HREF.'login.php');
		exit;
	}


/***** end session initilization block ****/

// 4. enable output compression, if it isn't already enabled:
if ((@ini_get('output_handler') == '') && (@ini_get('zlib.output_handler') == '')) {
	@ini_set('zlib.output_compression', 1);
}

/* 5. database connection */
if (!defined('AT_REDIRECT_LOADED')){
	require_once(AT_INCLUDE_PATH.'lib/mysql_connect.inc.php');
}

/* get config variables. if they're not in the db then it uses the installation default value in constants.inc.php */
$sql    = "SELECT * FROM ".TABLE_PREFIX."config";
$result = mysql_query($sql, $db);
while ($row = mysql_fetch_assoc($result)) { 
	$_config[$row['name']] = $row['value'];
}

/* following is added as a transition period and backwards compatability: */
define('EMAIL',                     $_config['contact_email']);
define('EMAIL_NOTIFY',              $_config['email_notification']);
define('ALLOW_INSTRUCTOR_REQUESTS', $_config['allow_instructor_requests']);
define('AUTO_APPROVE_INSTRUCTORS',  $_config['auto_approve_instructors']);
define('SITE_NAME',                 $_config['site_name']);
define('HOME_URL',                  $_config['home_url']);
define('DEFAULT_LANGUAGE',          $_config['default_language']);
define('CACHE_DIR',                 $_config['cache_dir']);
define('AT_ENABLE_CATEGORY_THEMES', $_config['theme_categories']);
define('AT_COURSE_BACKUPS',         $_config['course_backups']);
define('AT_EMAIL_CONFIRMATION',     $_config['email_confirmation']);
define('AT_MASTER_LIST',            $_config['master_list']);
$MaxFileSize       = $_config['max_file_size']; 
$MaxCourseSize     = $_config['max_course_size'];
$MaxCourseFloat    = $_config['max_course_float'];
$IllegalExtentions = explode('|',$_config['illegal_extentions']);
define('AT_DEFAULT_PREFS',  isset($_config['prefs_default']) ? $_config['prefs_default'] : '');
$_config['home_defaults'] .= (isset($_config['home_defaults_2']) ? $_config['home_defaults_2'] : '');
$_config['main_defaults'] .= (isset($_config['main_defaults_2']) ? $_config['main_defaults_2'] : '');

require(AT_INCLUDE_PATH.'phpCache/phpCache.inc.php'); // cache library
require(AT_INCLUDE_PATH.'lib/utf8.php');			//UTF-8 multibyte library

if ($_config['time_zone']) {
	//$sql = "SET time_zone='{$_config['time_zone']}'";
	//mysql_query($sql, $db);

	if (function_exists('date_default_timezone_set')) {
		date_default_timezone_set($_config['time_zone']);
	} else {
		@putenv("TZ={$_config['time_zone']}");
	}
}

/***** 7. start language block *****/
	// set current language
	require(AT_INCLUDE_PATH . 'classes/Language/LanguageManager.class.php');
	$languageManager =& new LanguageManager();

	$myLang =& $languageManager->getMyLanguage();

	if ($myLang === FALSE) {
		echo 'There are no languages installed!';
		exit;
	}
	$myLang->saveToSession();
	if (isset($_GET['lang']) && $_SESSION['valid_user']) {
		if ($_SESSION['course_id'] == -1) {
			$myLang->saveToPreferences($_SESSION['login'], 1);	//1 for admin			
		} else {
			$myLang->saveToPreferences($_SESSION['member_id'], 0);	//0 for non-admin
		}
	}
	$myLang->sendContentTypeHeader();

	/* set right-to-left language */
	$rtl = '';
	if ($myLang->isRTL()) {
		$rtl = 'rtl_'; /* basically the prefix to a rtl variant directory/filename. eg. rtl_tree */
	}
/***** end language block ****/

/* 8. load common libraries */
	require(AT_INCLUDE_PATH.'classes/ContentManager.class.php');  /* content management class */
	require_once(AT_INCLUDE_PATH.'lib/output.inc.php');           /* output functions */
	if (!(defined('AT_REDIRECT_LOADED'))){
		require_once(AT_INCLUDE_PATH . 'classes/UrlRewrite/UrlParser.class.php');	/* pretty url tool */
	}
	require(AT_INCLUDE_PATH.'classes/Savant2/Savant2.php');       /* for the theme and template management */

	// set default template paths:
	$savant =& new Savant2();
	$savant->addPath('template', AT_INCLUDE_PATH . '../themes/default/');

	if (isset($_SESSION['prefs']['PREF_THEME']) && file_exists(AT_INCLUDE_PATH . '../themes/' . $_SESSION['prefs']['PREF_THEME']) && isset($_SESSION['valid_user']) && $_SESSION['valid_user']) {

		if ($_SESSION['course_id'] == -1) {
			if (!is_dir(AT_INCLUDE_PATH . '../themes/' . $_SESSION['prefs']['PREF_THEME'])) {
				$_SESSION['prefs']['PREF_THEME'] = 'default';
			}
			$savant->addPath('template', AT_INCLUDE_PATH . '../themes/' . $_SESSION['prefs']['PREF_THEME'] . '/');
		} else {
			//check if enabled
			$sql    = "SELECT status FROM ".TABLE_PREFIX."themes WHERE dir_name = '".$_SESSION['prefs']['PREF_THEME']."'";
			$result = mysql_query($sql, $db);
			$row = mysql_fetch_assoc($result);
			if ($row['status'] > 0) {
				$savant->addPath('template', AT_INCLUDE_PATH . '../themes/' . $_SESSION['prefs']['PREF_THEME'] . '/');
			} else {
				// get default
				$default_theme = get_default_theme();
				if (!is_dir(AT_INCLUDE_PATH . '../themes/' . $default_theme['dir_name'])) {
					$default_theme = array('dir_name' => 'default');
				}
				$savant->addPath('template', AT_INCLUDE_PATH . '../themes/' . $default_theme['dir_name'] . '/');
				$_SESSION['prefs']['PREF_THEME'] = $default_theme['dir_name'];
			}
		}
	} else {
		// get default
		$default_theme = get_default_theme();
		if (!is_dir(AT_INCLUDE_PATH . '../themes/' . $default_theme['dir_name'])) {
			$default_theme = array('dir_name' => 'default');
		}
		$savant->addPath('template', AT_INCLUDE_PATH . '../themes/' . $default_theme['dir_name'] . '/');
		$_SESSION['prefs']['PREF_THEME'] = $default_theme['dir_name'];
	}

	require(AT_INCLUDE_PATH . '../themes/' . $_SESSION['prefs']['PREF_THEME'] . '/theme.cfg.php');

	require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');
	$msg = new Message($savant);

	$contentManager = new ContentManager($db, isset($_SESSION['course_id']) ? $_SESSION['course_id'] : 0);
	$contentManager->initContent();
/**************************************************/

if (isset($_user_location) && ($_user_location == 'users') && $_SESSION['valid_user'] && ($_SESSION['course_id'] > 0)) {
	$_SESSION['course_id'] = 0;
}

if ((!isset($_SESSION['course_id']) || $_SESSION['course_id'] == 0) && ($_user_location != 'users') && ($_user_location != 'prog') && !isset($_GET['h']) && ($_user_location != 'public') && (!isset($_pretty_url_course_id) || $_pretty_url_course_id == 0)) {
	header('Location:'.AT_BASE_HREF.'users/index.php');
	exit;
}
/* check if we are in the requested course, if not, bounce to it.
 * @author harris, for pretty url, read AT_PRETTY_URL_HANDLER
 */ 
if ((isset($_SESSION['course_id']) && isset($_pretty_url_course_id) && $_SESSION['course_id'] != $_pretty_url_course_id) ||
	(isset($_pretty_url_course_id) && !isset($_SESSION['course_id']) && !isset($_REQUEST['ib']))) {

	if($_config['pretty_url'] == 0){
		header('Location: '.AT_BASE_HREF.'bounce.php?course='.$_pretty_url_course_id.SEP.'pu='.$_SERVER['PATH_INFO'].urlencode('?'.$_SERVER['QUERY_STRING']));
	} else {
		header('Location: '.AT_BASE_HREF.'bounce.php?course='.$_pretty_url_course_id.SEP.'pu='.$_SERVER['PATH_INFO']);
	}
	exit;
}

   /**
   * This function is used for printing variables for debugging.
   * @access  public
   * @param   mixed $var	The variable to output
   * @param   string $title	The name of the variable, or some mark-up identifier.
   * @author  Joel Kronenberg
   */
function debug($var, $title='') {
	if (!defined('AT_DEVEL') || !AT_DEVEL) {
		return;
	}
	
	echo '<pre style="border: 1px black solid; padding: 0px; margin: 10px;" title="debugging box">';
	if ($title) {
		echo '<h4>'.$title.'</h4>';
	}
	
	ob_start();
	print_r($var);
	$str = ob_get_contents();
	ob_end_clean();

	$str = str_replace('<', '&lt;', $str);

	$str = str_replace('[', '<span style="color: red; font-weight: bold;">[', $str);
	$str = str_replace(']', ']</span>', $str);
	$str = str_replace('=>', '<span style="color: blue; font-weight: bold;">=></span>', $str);
	$str = str_replace('Array', '<span style="color: purple; font-weight: bold;">Array</span>', $str);
	echo $str;
	echo '</pre>';
}


/********************************************************************/
/* the system course information									*/
/* system_courses[course_id] = array(title, description, subject)	*/
$system_courses = array();

// temporary set to a low number
$sql = 'SELECT * FROM '.TABLE_PREFIX.'courses ORDER BY title';
$result = mysql_query($sql, $db);
while ($row = mysql_fetch_assoc($result)) {
	$course = $row['course_id'];
	unset($row['course_id']);
	$system_courses[$course] = $row;
}
/*																	*/
/********************************************************************/

if (isset($_SESSION['course_id']) && $_SESSION['course_id'] > 0) {
	$sql = 'SELECT * FROM '.TABLE_PREFIX.'glossary WHERE course_id='.$_SESSION['course_id'].' ORDER BY word';
	$result = mysql_query($sql, $db);
	$glossary = array();
	$glossary_ids = array();
	while ($row_g = mysql_fetch_assoc($result)) {
		
		$row_g['word'] = urlencode($row_g['word']);

		$glossary[$row_g['word']] = str_replace("'", "\'",$row_g['definition']);
		$glossary_ids[$row_g['word_id']] = $row_g['word'];

		/* a kludge to get the related id's for when editing content */
		/* it's ugly, but beats putting this query AGAIN on the edit_content.php page */
		if (isset($get_related_glossary)) {
			$glossary_ids_related[$row_g['word']] = $row_g['related_word_id'];
		}
	}
}

function get_html_body($text) {
	/* strip everything before <body> */
	$start_pos	= strpos(strtolower($text), '<body');
	if ($start_pos !== false) {
		$start_pos	+= strlen('<body');
		$end_pos	= strpos(strtolower($text), '>', $start_pos);
		$end_pos	+= strlen('>');

		$text = substr($text, $end_pos);
	}

	/* strip everything after </body> */
	$end_pos	= strpos(strtolower($text), '</body>');
	if ($end_pos !== false) {
		$text = trim(substr($text, 0, $end_pos));
	}

	return $text;
}

function get_html_head ($text) {
	/* make all text lower case */
	$text = strtolower($text);

	/* strip everything before <head> */
	$start_pos	= strpos($text, '<head');
	if ($start_pos !== false) {
		$start_pos	+= strlen('<head');
		$end_pos	= strpos($text, '>', $start_pos);
		$end_pos	+= strlen('>');

		$text = substr($text, $end_pos);
	}

	/* strip everything after </head> */
	$end_pos	= strpos($text, '</head');
	if ($end_pos !== false) {
		$text = trim(substr($text, 0, $end_pos));
	}
	return $text;
}

/**
* This function cuts out requested tag information from html head
* @access  public
* @param   $text  html text
* @param   $tags  a string or an array of requested tags
* @author  Cindy Qi Li
*/
function get_html_head_by_tag($text, $tags)
{
	$head = get_html_head($text);
	$rtn_text = "";
	
	if (!is_array($tags) && strlen(trim($tags)) > 0)
	{
		$tags = array(trim($tags));
	}
	
	foreach ($tags as $tag)
	{
		$tag = strtolower($tag);

		/* strip everything before <{tag}> */
		$start_pos	= strpos($head, '<'.$tag);
		$temp_head = $head;
		
		while ($start_pos !== false) 
		{
			$temp_text = substr($temp_head, $start_pos);
	
			/* strip everything after </{tag}> or />*/
			$end_pos	= strpos($temp_text, '</' . $tag . '>');
	
			if ($end_pos !== false) 
			{
				$end_pos += strlen('</' . $tag . '>');
				
				// add an empty line after each tag information
				$rtn_text .= trim(substr($temp_text, 0, $end_pos)) . '
	
';
			}
			else  // match /> as ending tag if </tag> is not found
			{
				$end_pos	= strpos($temp_text, '/>');
				$end_pos += strlen('/>');
				
				// add an empty line after each tag information
				$rtn_text .= trim(substr($temp_text, 0, $end_pos)) . '
	
';
			}
			
			// initialize vars for next round of matching
			$temp_head = substr($temp_text, $end_pos);
			$start_pos = strpos($temp_head, '<'.$tag);
		}
	}
	
	return $rtn_text;
}

if (version_compare(phpversion(), '4.3.0') < 0) {
	function file_get_contents($filename) {
		$fd = @fopen($filename, 'rb');
		if ($fd === false) {
			$content = false;
		} else {
			$content = @fread($fd, filesize($filename));
			@fclose($fd);
		}

		return $content;
	}

	function mysql_real_escape_string($input) {
		return mysql_escape_string($input);
	}
}


function add_user_online() {
	if (!isset($_SESSION['member_id']) || !($_SESSION['member_id'] > 0)) {
		return;
	}
	global $db;

    $expiry = time() + 900; // 15min
    $sql    = 'REPLACE INTO '.TABLE_PREFIX.'users_online VALUES ('.$_SESSION['member_id'].', '.$_SESSION['course_id'].', "'.addslashes(get_display_name($_SESSION['member_id'])).'", '.$expiry.')';
    $result = mysql_query($sql, $db);

	/* garbage collect and optimize the table every so often */
	mt_srand((double) microtime() * 1000000);
	$rand = mt_rand(1, 20);
	if ($rand == 1) {
		$sql = 'DELETE FROM '.TABLE_PREFIX.'users_online WHERE expiry<'.time();
		$result = @mysql_query($sql, $db);
	}
}

/**
 * Returns the login name of a member.
 * @access  public
 * @param   int $id	The ID of the member.
 * @return  Returns the login name of the member whose ID is $id.
 * @author  Joel Kronenberg
 */
function get_login($id){
	global $db, $_config_defaults;

	if (is_array($id)) {
		$id		= implode(',',$id);
		$sql	= 'SELECT login, member_id FROM '.TABLE_PREFIX.'members WHERE member_id IN ('.$id.') ORDER BY login';

		$rows = array();
		$result	= mysql_query($sql, $db);
		while( $row	= mysql_fetch_assoc($result)) {
			$rows[$row['member_id']] = $row['login'];
		}
		return $rows;
	} else {
		$id		= intval($id);
		$sql	= 'SELECT login FROM '.TABLE_PREFIX.'members WHERE member_id='.$id;

		$result	= mysql_query($sql, $db);
		$row	= mysql_fetch_assoc($result);

		return $row['login'];
	}

}

function get_display_name($id) {
	static $db, $_config, $display_name_formats;
	if (!$id) {
		return $_SESSION['login'];
	}

	if (!isset($db, $_config)) {
		global $db, $_config, $display_name_formats;
	}

	if (substr($id, 0, 2) == 'g_' || substr($id, 0, 2) == 'G_')
	{
		$sql	= "SELECT name FROM ".TABLE_PREFIX."guests WHERE guest_id='".$id."'";
		$result	= mysql_query($sql, $db);
		$row	= mysql_fetch_assoc($result);

		return _AT($display_name_formats[$_config['display_name_format']], '', $row['name'], '', '');
	}
	else
	{
		$sql	= 'SELECT login, first_name, second_name, last_name FROM '.TABLE_PREFIX.'members WHERE member_id='.$id;
		$result	= mysql_query($sql, $db);
		$row	= mysql_fetch_assoc($result);

		return _AT($display_name_formats[$_config['display_name_format']], $row['login'], $row['first_name'], $row['second_name'], $row['last_name']);
	}
}

function get_forum_name($fid){
	global $db;

	$fid = intval($fid);

	$sql	= 'SELECT title FROM '.TABLE_PREFIX.'forums WHERE forum_id='.$fid;
	$result	= mysql_query($sql, $db);
	if (($row = mysql_fetch_assoc($result)) && $row['title']) {
		return $row['title'];		
	}

	$sql = "SELECT group_id FROM ".TABLE_PREFIX."forums_groups WHERE forum_id=$fid";
	$result	= mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		return get_group_title($row['group_id']);
	}

	return FALSE;
}

/* takes the array of valid prefs and assigns them to the current session */
function assign_session_prefs($prefs) {
	unset($_SESSION['prefs']);
	if (is_array($prefs)) {
		foreach($prefs as $pref_name => $value) {
			$_SESSION['prefs'][$pref_name] = $value;
		}
	}
}

function save_prefs( ) {
	global $db;

	if ($_SESSION['valid_user']) {
		$data	= addslashes(serialize($_SESSION['prefs']));
		$sql	= 'UPDATE '.TABLE_PREFIX.'members SET preferences="'.$data.'", creation_date=creation_date, last_login=last_login WHERE member_id='.$_SESSION['member_id'];
		$result = mysql_query($sql, $db); 
	}
 
	/* else, we're not a valid user so nothing to save. */
}

/**
* Saves the last viewed content page in a user's course so that on next visit, user can start reading where they left off
* @access  public
* @param   int $cid		the content page id
* @return  none
* @see     $db			in include/vitals.inc.php
* @author  Joel Kronenberg
*/
function save_last_cid($cid) {
	if ($_SESSION['enroll'] == AT_ENROLL_NO) {
		return;
	}
	global $db;

	$_SESSION['s_cid']    = intval($_GET['cid']);

	if (!$_SESSION['is_admin']   && 
		!$_SESSION['privileges'] && 
		!isset($in_get)          && 
		!$_SESSION['cid_time']   && 
		($_SESSION['course_id'] > 0) ) 
		{
			$_SESSION['cid_time'] = time();
	}

	$sql = "UPDATE ".TABLE_PREFIX."course_enrollment SET last_cid=$cid WHERE course_id=$_SESSION[course_id] AND member_id=$_SESSION[member_id]";
	mysql_query($sql, $db);
}

// there has to be a better way of expressing this if-statement!
// and, does it really have to be here?
if ((!isset($_SESSION['is_admin']) || !$_SESSION['is_admin'])       && 
	(!isset($_SESSION['privileges']) || !$_SESSION['privileges'])     &&
	!isset($in_get)              && 
	isset($_SESSION['s_cid']) && $_SESSION['s_cid'] && 
	isset($_SESSION['cid_time']) && $_SESSION['cid_time'] &&
    ($_SESSION['course_id'] > 0) && 
	($_SESSION['s_cid'] != $_GET['cid']) && 
	($_SESSION['enroll'] != AT_ENROLL_NO) )  
	{
		$diff = time() - $_SESSION['cid_time'];
		if ($diff > 0) {
			$sql = "UPDATE ".TABLE_PREFIX."member_track SET counter=counter+1, duration=duration+$diff, last_accessed=NOW() WHERE member_id=$_SESSION[member_id] AND content_id=$_SESSION[s_cid]";

			$result = mysql_query($sql, $db);

			if (mysql_affected_rows($db) == 0) {
				$sql = "INSERT INTO ".TABLE_PREFIX."member_track VALUES ($_SESSION[member_id], $_SESSION[course_id], $_SESSION[s_cid], 1, $diff, NOW())";
				$result = mysql_query($sql, $db);
			}
		}

		$_SESSION['cid_time'] = 0;
}


/**
* Checks if the $_SESSION[member_id] is an instructor (true) or not (false)
* The result is only fetched once - it is then available via a static variable, $is_instructor
* @access  public
* @param   none
* @return  bool	true if is instructor, false otherwise.
* @see     $db   in include/vitals.inc.php
* @author  Joel Kronenberg
*/	
function get_instructor_status() {
	static $is_instructor;

	if (isset($is_instructor)) {
		return $is_instructor;
	}

	global $db;

	$is_instructor = false;

	$sql = 'SELECT status FROM '.TABLE_PREFIX.'members WHERE member_id='.$_SESSION['member_id'];
	$result = mysql_query($sql, $db);
	if (!($row = mysql_fetch_assoc($result))) {
		$is_instructor = FALSE;
		return FALSE;
	}

	if ($row['status'] == AT_STATUS_INSTRUCTOR) {
		$is_instructor = TRUE;
		return TRUE;
	}

	$is_instructor = FALSE;
	return FALSE;
}

/****************************************************/
/* update the user online list						*/
if (isset($_SESSION['valid_user']) && $_SESSION['valid_user']) {
	$new_minute = time()/60;
	if (!isset($_SESSION['last_updated'])) {
		$_SESSION['last_updated'] = $new_minute;
	}
	$diff       = abs($_SESSION['last_updated'] - $new_minute);
	if ($diff > ONLINE_UPDATE) {
		$_SESSION['last_updated'] = $new_minute;
		add_user_online();
	}
}

/****************************************************/
/* compute the $_my_uri variable					*/
	$bits	  = explode(SEP, getenv('QUERY_STRING'));
	$num_bits = count($bits);
	$_my_uri  = '';

	for ($i=0; $i<$num_bits; $i++) {
		if (	(strpos($bits[$i], 'enable=')	=== 0) 
			||	(strpos($bits[$i], 'disable=')	=== 0)
			||	(strpos($bits[$i], 'expand=')	=== 0)
			||	(strpos($bits[$i], 'collapse=')	=== 0)
			||	(strpos($bits[$i], 'lang=')		=== 0)
			) {
			/* we don't want this variable added to $_my_uri */
			continue;
		}

		if (($_my_uri == '') && ($bits[$i] != '')) {
			$_my_uri .= '?';
		} else if ($bits[$i] != ''){
			$_my_uri .= SEP;
		}
		$_my_uri .= $bits[$i];
	}
	if ($_my_uri == '') {
		$_my_uri .= '?';
	} else {
		$_my_uri .= SEP;
	}
	$_my_uri = $_SERVER['PHP_SELF'].$_my_uri;

function my_add_null_slashes( $string ) {
    return mysql_real_escape_string(stripslashes($string));
}
function my_null_slashes($string) {
	return $string;
}

if ( get_magic_quotes_gpc() == 1 ) {
	$addslashes   = 'my_add_null_slashes';
	$stripslashes = 'stripslashes';
} else {
	$addslashes   = 'mysql_real_escape_string';
	$stripslashes = 'my_null_slashes';
}


/**
 * If MBString extension is loaded, 4.3.0+, then use it.
 * Otherwise we will have to use include/utf8 library
 * @author	Harris
 * @date Oct 10, 2007
 * @version	1.5.6
 */
 if (extension_loaded('mbstring')){
	 $strtolower = 'mb_strtolower';
	 $strtoupper = 'mb_strtoupper';
	 $substr = 'mb_substr';
	 $strpos = 'mb_strpos';
	 $strrpos = 'mb_strrpos';
	 $strlen = 'mb_strlen';
 } else {
 	 $strtolower = 'utf8_strtolower';
	 $strtoupper = 'utf8_strtoupper';
	 $substr = 'utf8_substr';
	 $strpos = 'utf8_strpos';
	 $strrpos = 'utf8_strrpos';
	 $strlen = 'utf8_strlen';
 }


/*~~~~~~~~~~~~~~~~~flash detection~~~~~~~~~~~~~~~~*/
if(isset($_COOKIE["flash"])){
    $_SESSION['flash'] = $_COOKIE["flash"];

    //delete the cookie
    setcookie("flash",'',time()-3600);
}

if (!isset($_SESSION["flash"])) {
	$_custom_head .= '
		<script type="text/javascript">
		<!--

			//VB-Script for InternetExplorer
			function iExploreCheck()
			{
				document.writeln("<scr" + "ipt language=\'VBscript\'>");
				//document.writeln("\'Test to see if VBScripting works");
				document.writeln("detectableWithVB = False");
				document.writeln("If ScriptEngineMajorVersion >= 2 then");
				document.writeln("   detectableWithVB = True");
				document.writeln("End If");
				//document.writeln("\'This will check for the plugin");
				document.writeln("Function detectActiveXControl(activeXControlName)");
				document.writeln("   on error resume next");
				document.writeln("   detectActiveXControl = False");
				document.writeln("   If detectableWithVB Then");
				document.writeln("      detectActiveXControl = IsObject(CreateObject(activeXControlName))");
				document.writeln("   End If");
				document.writeln("End Function");
				document.writeln("</scr" + "ipt>");
				return detectActiveXControl("ShockwaveFlash.ShockwaveFlash.1");
			}


			var plugin = (navigator.mimeTypes && navigator.mimeTypes["application/x-shockwave-flash"]) ? navigator.mimeTypes["application/x-shockwave-flash"].enabledPlugin : false;
			if(!(plugin) && (navigator.userAgent && navigator.userAgent.indexOf("MSIE")>=0 && (navigator.appVersion.indexOf("Win") != -1)))
				if (iExploreCheck())
					flash_detect = "flash=yes";
				else
					flash_detect = "flash=no";

			else if(plugin)
				flash_detect = "flash=yes";
			else
				flash_detect = "flash=no";

			writeCookie(flash_detect);

			function writeCookie(value)
			{
				var today = new Date();
				var the_date = new Date("December 31, 2099");
				var the_cookie_date = the_date.toGMTString();
				var the_cookie = value + ";expires=" + the_cookie_date;
				document.cookie = the_cookie;
			}
		//-->
		</script>
';
}



/*~~~~~~~~~~~~~~end flash detection~~~~~~~~~~~~~~~*/



/**
* Checks if the data exceeded the database predefined length, if so,
* truncate it.
* This is used on data that are being inserted into the database.
* If this function is used for display purposes, you may want to add the '...' 
*  at the end of the string by setting the $forDisplay=1
* @param	the mbstring that needed to be checked
* @param	the byte length of what the input should be in the database.
* @param	(OPTIONAL)
*			append '...' at the end of the string.  Should not use this when 
*			dealing with database.  This should only be set for display purposes.
* @return	the mbstring safe sql entry
* @author	Harris Wong
*/
function validate_length($input, $len, $forDisplay=0){
	global $strlen, $substr;
	$input_bytes_len = strlen($input);
	$input_len = $strlen($input);

	//If the input has exceeded the db column limit
	if ($input_bytes_len > $len){
		//calculate where to chop off the string
		$percentage = $input_bytes_len / $input_len;
		//Get the suitable length that should be stored in the db
		$suitable_len = floor($len / $percentage);

		if ($forDisplay===1){
			return $substr($input, 0, $suitable_len).'...';
		}
		return $substr($input, 0, $suitable_len);
	}
	//if valid length
	return $input;

/*
 * Instead of blindly cutting off the input from the given param
 * 
	global $strlen, $substr;
	if ($strlen($input) > $len) {
		if ($forDisplay===1){
			return $substr($input, 0, $len).'...';
		}
		return $substr($input, 0, $len);
	}
	return $input;
*/
}

/**
 * If pretty URL within admin config is switched on.  We will apply pretty URL 
 * to all the links in ATutor.  This function will authenticate itself towards the current pages.
 * In our definition, admins, login, registration pages shouldn't have pretty url applied.  However,
 * if one want to use url_rewrite on these pages, please force it by using the third parameter.  
 * Note: If system config has turned off this feature, $force will have no effect.
 * @param	string	the Url should be a relative link, have to improve this later on, to check if 
 *					it's a relative link, if not, truncate it.
 * @param	boolean	Available values are AT_PRETTY_URL_IS_HEADER, AT_PRETTY_URL_NOT_HEADER(default)
 *			use AT_PRETTY_URL_IS_HEADER if url_rewrite is used in php header('Location:..'), absolute path is needed for this.
 * @param	boolean	true to force the url_rewrite, false otheriwse.  False is the default.
 * @author	Harris Wong
 */
function url_rewrite($url, $is_rewriting_header=AT_PRETTY_URL_NOT_HEADER, $force=false){
	global $_config, $db;
	$url_parser = new UrlParser();
	$pathinfo = $url_parser->getPathArray();

	/* If this is any kind of admins, don't prettify the url
	 * $_SESSION['is_guest'] is used to check against login/register/browse page, the links on this page will 
	 * only be prettified when a user has logged in.
	 * Had used $_SESSION[valid_user] before but it created this problem: 
	 * http://www.atutor.ca/atutor/mantis/view.php?id=3426
	 */
	if ($force || (isset($_SESSION['course_id']) && $_SESSION['course_id'] > 0)) {
		//if course id is defined, apply pretty url.
	} 
	//if this is something that is displayed on the login page, don't modify the urls.
	else if ( (admin_authenticate(AT_ADMIN_PRIV_ADMIN, AT_PRIV_RETURN) 
			|| (isset($_SESSION['privileges']) && admin_authenticate($_SESSION['privileges'], AT_PRIV_RETURN))) 
			|| (isset($_SESSION['is_guest']) && $_SESSION['is_guest']==1)){
		return $url;
	} 

	//if we allow pretty url in the system
	if ($_config['pretty_url'] > 0){
		$course_id = 0;
		//If we allow course dir name from sys perf		
		if ($_config['course_dir_name'] > 0){
			if (preg_match('/bounce.php\?course=([\d]+)$/', $url, $matches) == 1){
				// bounce has the highest priority, even if session is set, work on 
				// bounce first.
				$course_id = $url_parser->getCourseDirName($matches[1]);
			} elseif (isset($_REQUEST['course'])){
				//jump menu
				$course_id = $url_parser->getCourseDirName($_REQUEST['course']);
			} elseif (isset($_SESSION['course_id']) && $_SESSION['course_id'] > 0){
				$course_id = $url_parser->getCourseDirName($_SESSION['course_id']);
			}
		} else {
			$course_id = $_SESSION['course_id'];
		}
		$url = $pathinfo[1]->convertToPrettyUrl($course_id, $url);
	} elseif ($_config['course_dir_name'] > 0) {
		//enabled course directory name, disabled pretty url
		if (preg_match('/bounce.php\?course=([\d]+)$/', $url, $matches) == 1){
			// bounce has the highest priority, even if session is set, work on 
			// bounce first.
			$course_id = $url_parser->getCourseDirName($matches[1]);
		} elseif (isset($_REQUEST['course'])){
			$course_id = $url_parser->getCourseDirName($_REQUEST['course']);
		} elseif (isset($_SESSION['course_id']) && $_SESSION['course_id'] > 0){
			$course_id = $url_parser->getCourseDirName($_SESSION['course_id']);
		} 
		$url = $pathinfo[1]->convertToPrettyUrl($course_id, $url);
	}

	//instead of putting AT_BASE_HREF in all the headers location, we will put it here.
	//Abs paths are required for pretty url because otherwise the url location will be appeneded.
	//ie.	ATutor_161/blogs/CoURSe_rOAd/blogs/view.php/ot/1/oid/1/ instead of 
	//		ATutor_161/CoURSe_rOAd/blogs/view.php/ot/1/oid/1/
	if ($is_rewriting_header==true){
		return AT_BASE_HREF.$url;
	} 
	return $url;
}


/**
* Applies $addslashes or intval() recursively.
* @access  public
* @param   mixed $input	The input to clean.
* @return  A safe version of $input
* @author  Joel Kronenberg
*/
function sql_quote($input) {
	global $addslashes;

	if (is_array($input)) {
		foreach ($input as $key => $value) {
			if (is_array($input[$key])) {
				$input[$key] = sql_quote($input[$key]);
			} else if (!empty($input[$key]) && is_numeric($input[$key])) {
				$input[$key] = intval($input[$key]);
			} else {
				$input[$key] = $addslashes(trim($input[$key]));
			}
		}
	} else {
		if (!empty($input) && is_numeric($input)) {
			$input = intval($input);
		} else {
			$input = $addslashes(trim($input));
		}
	}
	return $input;
}

function query_bit( $bitfield, $bit ) {
	if (!is_int($bitfield)) {
		$bitfield = intval($bitfield);
	}
	if (!is_int($bit)) {
		$bit = intval($bit);
	}
	return ( $bitfield & $bit ) ? true : false;
} 

/**
* Authenticates the current user against the specified privilege.
* @access  public
* @param   int	$privilege		privilege to check against.
* @param   bool	$check			whether or not to return the result or to abort/exit.
* @return  bool	true if this user is authenticated, false otherwise.
* @see	   query_bit() in include/vitals.inc.php
* @author  Joel Kronenberg
*/
function authenticate($privilege, $check = false) {
	if ($_SESSION['is_admin']) {
		return true;
	}

	$auth = query_bit($_SESSION['privileges'], $privilege);
	
	if (!$_SESSION['valid_user'] || !$auth) {
		if (!$check){
			global $msg;
			$msg->addInfo('NO_PERMISSION');
			require(AT_INCLUDE_PATH.'header.inc.php'); 
			require(AT_INCLUDE_PATH.'footer.inc.php'); 
			exit;
		} else {
			return false;
		}
	}
	return true;
}

function admin_authenticate($privilege = 0, $check = false) {
	if (!isset($_SESSION['valid_user']) || !$_SESSION['valid_user'] || ($_SESSION['course_id'] != -1)) {
		if ($check) {
			return false;
		}
		header('Location: '.AT_BASE_HREF.'login.php');
		exit;
	}

	if ($_SESSION['privileges'] == AT_ADMIN_PRIV_ADMIN) {
		return true;
	}

	if ($privilege) {
		$auth = query_bit($_SESSION['privileges'], $privilege);

		if (!$auth) {
			if ($check) {
				return false;
			}
			global $msg;
			$msg->addError('ACCESS_DENIED');
			require(AT_INCLUDE_PATH.'header.inc.php'); 
			require(AT_INCLUDE_PATH.'footer.inc.php'); 
			exit;
		}
	}
	return true;
}

function get_default_theme() {
	global $db;

	$sql	= "SELECT dir_name FROM ".TABLE_PREFIX."themes WHERE status=2";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);

	return $row;
}

if (isset($_GET['expand'])) {
	$_SESSION['menu'][intval($_GET['expand'])] = 1;
} else if (isset($_GET['collapse'])) {
	unset($_SESSION['menu'][intval($_GET['collapse'])]);
}

/**
* Writes present action to admin log db
* @access  private
* @param   string $operation_type	The type of operation
* @param   string $table_name		The table affected
* @param   string $num_affected		The number of rows in the table affected
* @author  Shozub Qureshi
*/
function write_to_log($operation_type, $table_name, $num_affected, $details) {
	global $db;

	if ($num_affected > 0) {
		$details = addslashes(stripslashes($details));
		$sql    = "INSERT INTO ".TABLE_PREFIX."admin_log VALUES ('$_SESSION[login]', NULL, $operation_type, '$table_name', $num_affected, '$details')";
		$result = mysql_query($sql, $db);
	}
}

function get_group_title($group_id) {
	global $db;
	$sql = "SELECT title FROM ".TABLE_PREFIX."groups WHERE group_id=$group_id";
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		return $row['title'];
	}
	return FALSE;
}

function get_status_name($status_id) {
	switch ($status_id) {
		case AT_STATUS_DISABLED:
				return _AT('disabled');
				break;
		case AT_STATUS_UNCONFIRMED:
			return _AT('unconfirmed');
			break;
		case AT_STATUS_STUDENT:
			return _AT('student');
			break;
		case AT_STATUS_INSTRUCTOR:
			return _AT('instructor');
			break;
	}
}

function profile_image_exists($id) {
	$extensions = array('gif', 'jpg', 'png');

	foreach ($extensions as $extension) {
		if (file_exists(AT_CONTENT_DIR.'profile_pictures/originals/'. $id.'.'.$extension)) {
			return true;
		}
	}
}

/**
 * print thumbnails or profile pic
 * @param	int		image id
 * @param	int		1 for thumbnail, 2 for profile
 */
function print_profile_img($id, $type=1) {
	global $moduleFactory;
	$mod = $moduleFactory->getModule('_standard/profile_pictures');
	if ($mod->isEnabled() === FALSE) {
		return;
	}
	if (profile_image_exists($id)) {
		if ($type==1){
			echo '<img src="get_profile_img.php?id='.$id.'" class="profile-picture" alt="" />';
		} elseif($type==2){
			echo '<img src="get_profile_img.php?id='.$id.SEP.'size=p" class="profile-picture" alt="" />';
		}
	} else {
		echo '<img src="images/clr.gif" height="100" width="100" class="profile-picture" alt="" />';
	}
}

function profile_image_delete($id) {
	$extensions = array('gif', 'jpg', 'png');

	foreach ($extensions as $extension) {
		if (file_exists(AT_CONTENT_DIR.'profile_pictures/originals/'. $id.'.'.$extension)) {
			unlink(AT_CONTENT_DIR.'profile_pictures/originals/'. $id.'.'.$extension);
		}
		if (file_exists(AT_CONTENT_DIR.'profile_pictures/profile/'. $id.'.'.$extension)) {
			unlink(AT_CONTENT_DIR.'profile_pictures/profile/'. $id.'.'.$extension);
		}
		if (file_exists(AT_CONTENT_DIR.'profile_pictures/thumbs/'. $id.'.'.$extension)) {
			unlink(AT_CONTENT_DIR.'profile_pictures/thumbs/'. $id.'.'.$extension);
		}		
	}
}

/**
 * get_group_concat
 * returns a list of $field values from $table using $where_clause, separated by $separator.
 * uses mysql's GROUP_CONCAT() if available and if within the limit (default is 1024), otherwise
 * it does it the old school way.
 * returns the list (as a string) or (int) 0, if none found.
 */
function get_group_concat($table, $field, $where_clause = 1, $separator = ',') {
	global $_config, $db;
	if (!isset($_config['mysql_group_concat_max_len'])) {
		$sql = "SELECT  @@global.group_concat_max_len AS max";
		$result = mysql_query($sql, $db);
		if ($result && ($row = mysql_fetch_assoc($result))) {
			$_config['mysql_group_concat_max_len'] = $row['max'];
		} else {
			$_config['mysql_group_concat_max_len'] = 0;
		}
		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('mysql_group_concat_max_len', '{$_config['mysql_group_concat_max_len']}')";
		mysql_query($sql, $db);
	}
	if ($_config['mysql_group_concat_max_len'] > 0) {
		$sql = "SELECT GROUP_CONCAT($field SEPARATOR '$separator') AS list FROM ".TABLE_PREFIX."$table WHERE $where_clause";
		$result = mysql_query($sql, $db);
		if ($row = mysql_fetch_assoc($result)) {
			if (!$row['list']) {
				return 0; // empty
			} else if ($row['list'] && strlen($row['list']) < $_config['mysql_group_concat_max_len']) {
				return $row['list'];
			} // else: list is truncated, do it the old way
		} else {
			// doesn't actually get here.
			return 0; // empty
		}
	} // else:

	$list = '';
	$sql = "SELECT $field AS id FROM ".TABLE_PREFIX."$table WHERE $where_clause";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$list .= $row['id'] . ',';
	}
	if ($list) {
		return substr($list, 0, -1);
	}
	return 0;
}

function get_human_time($seconds) {
	if ($seconds < 0) { 
		$out = '0'._AT('second_short'); 
	} else if ($seconds > 60 * 60) { // more than 60 minutes.
		$hours = floor($seconds / 60 / 60);
		$minutes = floor(($seconds - $hours * 60 * 60) / 60);
		$out = $hours ._AT('hour_short').' '.$minutes._AT('minute_short');

		//$out = ($seconds
	} else if ($seconds > 60) { // more than a minute
		$minutes = floor($seconds / 60);
		$out = $minutes ._AT('minute_short').' '.($seconds - $minutes * 60)._AT('second_short');
	} else { // less than a minute
		$out = $seconds . _AT('second_short');
	}

	return $out;
}

require(AT_INCLUDE_PATH . 'classes/Module/Module.class.php');

$moduleFactory =& new ModuleFactory(TRUE); // TRUE is for auto_loading the module.php files

if (isset($_GET['submit_language']) && $_SESSION['valid_user']) {
	if ($_SESSION['course_id'] == -1) {
		$sql = "UPDATE ".TABLE_PREFIX."admins SET language = '$_SESSION[lang]' WHERE login = '$_SESSION[login]'";
		$result = mysql_query($sql, $db);
	} else {
		$sql = "UPDATE ".TABLE_PREFIX."members SET language = '$_SESSION[lang]', creation_date=creation_date, last_login=last_login WHERE member_id = $_SESSION[member_id]";
		$result = mysql_query($sql, $db);
	}
}

?>
