<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002 - 2009                                            */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
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

//functions for properly escaping input strings
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

function regenerate_session($reload = false)
{
	if(!isset($_SESSION['IPaddress']) || $reload)
		$_SESSION['IPaddress'] = $_SERVER['REMOTE_ADDR'];

	if(!isset($_SESSION['userAgent']) || $reload)
		$_SESSION['userAgent'] = $_SERVER['HTTP_USER_AGENT'];

	$session_values = $_SESSION;

	// Set current session to expire in 10 seconds
	$_SESSION['OBSOLETE'] = true;
	$_SESSION['EXPIRES'] = time() + 10;

	// Create new session without destroying the old one
	session_regenerate_id(false);

	// Grab current session ID and close both sessions to allow other scripts to use them
	$newSession = session_id();
	session_write_close();

	// Set session ID to the new one, and start it back up again
	session_id($newSession);
	session_start();

	$_SESSION = $session_values; 
}

function check_session()
{
	if($_SESSION['OBSOLETE'] && ($_SESSION['EXPIRES'] < time())) {
		return false;
	}
	            
	if($_SESSION['IPaddress'] != $_SERVER['REMOTE_ADDR']) {
		return false;
	}
	            
	if($_SESSION['userAgent'] != $_SERVER['HTTP_USER_AGENT']) {
		return false;
	}
	            
	if(!$_SESSION['OBSOLETE']) {
		regenerate_session();
	}
	return true;
}

/*
 * structure of this document (in order):
 *
 * 0. load config.inc.php
 * 1. load constants
 * 2. initialize db connection and populate $_config
 * 3. initialize session
 * 4. enable output compression
 * 5. validate login user
 * 6. load language
 * 7. load cache/ContentManagement/output/Savant/Message libraries
 ***/

/**** 0. start system configuration options block ****/
	//set the timezone, php 5.3+ problem. http://atutor.ca/atutor/mantis/view.php?id=4409
	date_default_timezone_set('UTC');

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
/*** end system config block ***/

/*** 1. constants ***/
if (!defined('AT_REDIRECT_LOADED')){
	require_once(AT_INCLUDE_PATH.'lib/constants.inc.php');
}

/*** 2. initialize db connection and populate $_config ***/

if (!defined('AT_REDIRECT_LOADED')){
	require_once(AT_INCLUDE_PATH.'lib/mysql_connect.inc.php');
}

/* get config variables. if they're not in the db then it uses the installation default value in constants.inc.php */
$sql    = "SELECT * FROM ".TABLE_PREFIX."config";
$result = mysql_query($sql, $db);
while ($row = mysql_fetch_assoc($result)) { 
	$_config[$row['name']] = $row['value'];
}

/***** 3. start session initilization block *****/
if (headers_sent()) {
	require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
	$err = new ErrorHandler();
	trigger_error('VITAL#<br /><br /><code><strong>An error occurred. Output sent before it should have. Please correct the above error(s).' . '</strong></code><br /><hr /><br />', E_USER_ERROR);
}

@set_time_limit(0);
@ini_set('session.gc_maxlifetime', '36000'); /* 10 hours */
@session_cache_limiter('private, must-revalidate');
session_name('ATutorID');
error_reporting(AT_ERROR_REPORTING);

if (headers_sent()) {
	require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
	$err = new ErrorHandler();
	trigger_error('VITAL#<br /><code><strong>Headers already sent. ' .
					'Cannot initialise session.</strong></code><br /><hr /><br />', E_USER_ERROR);
	exit;
}

$isHttps = (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != "on")
           ? false
           : true;
ob_start();
session_set_cookie_params(0, $_config["session_path"], "", $isHttps);
session_start();

// Regenerate session id at every page refresh to prevent CSRF
$valid_session = true;
if (count($_SESSION) == 0) {
	regenerate_session();
} else {
	$valid_session = check_session();
}

$str = ob_get_contents();
ob_end_clean();
unregister_GLOBALS();

// Re-direct to login page at a potential session hijack
if (!$valid_session) {
	$_SESSION = array();
	header('Location: '.AT_BASE_HREF.'login.php');
	exit;
}

if ($str) {
	require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
	$err = new ErrorHandler();
	trigger_error('VITAL#<br /><code><strong>Error initializing session. ' .
					'Please varify that session.save_path is correctly set in your php.ini file ' .
					'and the directory exists.</strong></code><br /><hr /><br />', E_USER_ERROR);
	exit;
}
/***** end session initilization block ****/

/**** 4. enable output compression, if it isn't already enabled: ****/
if ((@ini_get('output_handler') == '') && (@ini_get('zlib.output_handler') == '')) {
	@ini_set('zlib.output_compression', 1);
}

/**** 5. validate login user ****/
if (!isset($_SESSION['course_id']) && !isset($_SESSION['valid_user']) && (!isset($_user_location) || $_user_location != 'public') && !isset($_pretty_url_course_id)) {
	if (isset($in_get) && $in_get && (($pos = strpos($_SERVER['PHP_SELF'], 'get.php/')) !== FALSE)) {
		$redirect = substr($_SERVER['PHP_SELF'], 0, $pos) . 'login.php';
		header('Location: '.$redirect);
		exit;
	}

	header('Location: '.AT_BASE_HREF.'login.php');
	exit;
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

if ($_config['time_zone']) {
	//$sql = "SET time_zone='{$_config['time_zone']}'";
	//mysql_query($sql, $db);

	if (function_exists('date_default_timezone_set')) {
	foreach($utc_timezones as $zone){

		if($zone[1] ==  $_config['time_zone']){
		$zone_name = $zone[2];
		break;
		}
	}
		date_default_timezone_set($zone_name);
	} else {
		@putenv("TZ={$_config['time_zone']}");
	}
}
/***** 6. load language *****/
// set current language
require(AT_INCLUDE_PATH . '../mods/_core/languages/classes/LanguageManager.class.php');
$languageManager = new LanguageManager();

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

/* 7. load common libraries */
require(AT_INCLUDE_PATH.'classes/ContentManager.class.php');  /* content management class */
require_once(AT_INCLUDE_PATH.'lib/output.inc.php');           /* output functions */
if (!(defined('AT_REDIRECT_LOADED'))){
	require_once(AT_INCLUDE_PATH . 'classes/UrlRewrite/UrlParser.class.php');	/* pretty url tool */
}
require(AT_INCLUDE_PATH.'classes/Savant2/Savant2.php');       /* for the theme and template management */

// set default template paths:
$savant = new Savant2();
$savant->addPath('template', AT_INCLUDE_PATH . '../themes/default/');

//if user has requested theme change, make the change here
if (($_POST['theme'] || $_POST['mobile_theme']) && $_POST['submit']) {
    //http://atutor.ca/atutor/mantis/view.php?id=4781
    //Themes should be in the same folder, disallow '../'
    $newTheme = str_replace("../", "", $_POST['theme']);
    $newMobileTheme = str_replace("../", "", $_POST['mobile_theme']);
    if ($newTheme != $_POST['theme'] || $newMobileTheme != $_POST['mobile_theme']) {
        header('Location:'.AT_BASE_HREF.'users/preferences.php');
	    exit;
    }
    
    $_SESSION['prefs']['PREF_THEME'] = $addslashes($_POST['theme']);
    $_SESSION['prefs']['PREF_MOBILE_THEME'] = $addslashes($_POST['mobile_theme']);
} else if ($_POST['set_default']) {
    $_SESSION['prefs']['PREF_THEME'] = 'default';
    $_SESSION['prefs']['PREF_MOBILE_THEME'] = 'mobile';
}

// Reset PREF_THEME when:
// 1. If PREF_THEME is not set 
// 2. The request is from the mobile device but PREF_THEME is not a mobile theme 
if (!isset($_SESSION['prefs']['PREF_THEME']) ||
    $_SESSION['prefs']['PREF_THEME'] == "" ||
    (is_mobile_device() && !is_mobile_theme($_SESSION['prefs']['PREF_THEME']))) {
	// get default
	$default_theme = get_default_theme();
	
	$_SESSION['prefs']['PREF_THEME'] = $default_theme['dir_name'];
}

if (!is_dir(AT_INCLUDE_PATH . '../themes/' . $_SESSION['prefs']['PREF_THEME']) || $_SESSION['prefs']['PREF_THEME'] == '') {
	$_SESSION['prefs']['PREF_THEME'] = get_system_default_theme();
}

// use "mobile" theme for mobile devices. For now, there's only one mobile theme and it's hardcoded.
// When more mobile themes come in, this should be changed.
if (isset($_SESSION['prefs']['PREF_THEME']) && file_exists(AT_INCLUDE_PATH . '../themes/' . $_SESSION['prefs']['PREF_THEME']) && isset($_SESSION['valid_user']) && $_SESSION['valid_user']) {
	if ($_SESSION['course_id'] == -1) {
		if ($_SESSION['prefs']['PREF_THEME'] == '' || !is_dir(AT_INCLUDE_PATH . '../themes/' . $_SESSION['prefs']['PREF_THEME'])) {
			$_SESSION['prefs']['PREF_THEME'] = get_system_default_theme();
		}
	} else {
		//check if enabled
		$sql    = "SELECT status FROM ".TABLE_PREFIX."themes WHERE dir_name = '".$_SESSION['prefs']['PREF_THEME']."'";
		$result = mysql_query($sql, $db);
		$row = mysql_fetch_assoc($result);
		if ($row['status'] > 0) {
		} else {
			// get default
			$default_theme = get_default_theme();
			if (!is_dir(AT_INCLUDE_PATH . '../themes/' . $default_theme['dir_name'])) {
				$default_theme = array('dir_name' => get_system_default_theme());
			}
			$_SESSION['prefs']['PREF_THEME'] = $default_theme['dir_name'];
		}
	}
}

$savant->addPath('template', AT_INCLUDE_PATH . '../themes/' . $_SESSION['prefs']['PREF_THEME'] . '/');
require(AT_INCLUDE_PATH . '../themes/' . $_SESSION['prefs']['PREF_THEME'] . '/theme.cfg.php');

require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');
$msg = new Message($savant);

$contentManager = new ContentManager($db, isset($_SESSION['course_id']) ? $_SESSION['course_id'] : $_GET['p_course']);
$contentManager->initContent();

/**************************************************/
require(AT_INCLUDE_PATH.'phpCache/phpCache.inc.php'); // cache library
require(AT_INCLUDE_PATH.'lib/utf8.php');			//UTF-8 multibyte library

if (!file_exists(AT_INCLUDE_PATH.'../sha-1factory.js')) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printErrors('MISSING_SHA1');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

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
   * This function is used for printing variables into log file for debugging.
   * @access  public
   * @param   mixed $var	The variable to output
   * @param   string $log	The location of the log file. If not provided, use the default one.
   * @author  Cindy Qi Li
   */
function debug_to_log($var, $log='') {
	if (!defined('AT_DEVEL') || !AT_DEVEL) {
		return;
	}
	
	if ($log == '') $log = AT_CONTENT_DIR. 'atutor.log';
	$handle = fopen($log, 'a');
	fwrite($handle, "\n\n");
	fwrite($handle, date("F j, Y, g:i a"));
	fwrite($handle, "\n");
	fwrite($handle, var_export($var,1));
	
	fclose($handle);
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
// p_course is set when pretty url is on and guests access a public course. @see bounce.php
// First, santinize p_course
if (isset($_REQUEST['p_course'])) {
	$_REQUEST['p_course'] = intval($_REQUEST['p_course']);
}

if (isset($_SESSION['course_id']) && $_SESSION['course_id'] > 0 || $_REQUEST['p_course'] > 0) {
	$sql = 'SELECT * FROM '.TABLE_PREFIX.'glossary 
	         WHERE course_id='.($_SESSION['course_id']>0 ? $_SESSION['course_id'] : $_REQUEST['p_course']).' 
	         ORDER BY word';
	$result = mysql_query($sql, $db);
	$glossary = array();
	$glossary_ids = array();
	while ($row_g = mysql_fetch_assoc($result)) {		
		$row_g['word'] = htmlspecialchars($row_g['word'], ENT_QUOTES, 'UTF-8');
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
//	$text = strtolower($text);

	/* strip everything before <head> */
	$start_pos	= stripos($text, '<head');
	if ($start_pos !== false) {
		$start_pos	+= strlen('<head');
		$end_pos	= stripos($text, '>', $start_pos);
		$end_pos	+= strlen('>');

		$text = substr($text, $end_pos);
	}

	/* strip everything after </head> */
	$end_pos	= stripos($text, '</head');
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
		$start_pos	= stripos($head, '<'.$tag);
		$temp_head = $head;
		
		while ($start_pos !== false) 
		{
			$temp_text = substr($temp_head, $start_pos);
	
			/* strip everything after </{tag}> or />*/
			$end_pos	= stripos($temp_text, '</' . $tag . '>');
	
			if ($end_pos !== false) 
			{
				$end_pos += strlen('</' . $tag . '>');
				
				// add an empty line after each tag information
				$rtn_text .= trim(substr($temp_text, 0, $end_pos)) . '
	
';
			}
			else  // match /> as ending tag if </tag> is not found
			{
				$end_pos	= stripos($temp_text, '/>');
				
				if($end_pos === false && stripos($temp_text, $tag.'>')===false){
					//if /> is not found, then this is not a valid XHTML
					//text iff it's not tag>
					$end_pos = stripos($temp_text, '>');
					$end_pos += strlen('>');
				} else {
					$end_pos += strlen('/>');
				}
				// add an empty line after each tag information
				$rtn_text .= trim(substr($temp_text, 0, $end_pos)) . '
	
';
			}
			
			// initialize vars for next round of matching
			$temp_head = substr($temp_text, $end_pos);
			$start_pos = stripos($temp_head, '<'.$tag);
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
	global $db, $addslashes;

    $expiry = time() + 900; // 15min
    $sql    = 'REPLACE INTO '.TABLE_PREFIX.'users_online VALUES ('.$_SESSION['member_id'].', '.$_SESSION['course_id'].', "'.$addslashes(get_display_name($_SESSION['member_id'])).'", '.$expiry.')';
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

// takes the array of valid prefs and assigns them to the current session 
// @params: prefs - an array of preferences
// @params: optional. Values are 0 or 1. Default value is 0
//          when 1, assign PREF_MOBILE_THEME to PREF_THEME if the request is from a mobile device
//                  this value is to set when the prefs values are set for display
//                  if this function is used as a front shot for save_prefs(), the value should be 0
function assign_session_prefs($prefs, $switch_mobile_theme = 0) {
	if (is_array($prefs)) {
		foreach($prefs as $pref_name => $value) {
			$_SESSION['prefs'][$pref_name] = $value;
		}
	}
	if (is_mobile_device() && $switch_mobile_theme) {
		$_SESSION['prefs']['PREF_THEME'] = $_SESSION['prefs']['PREF_MOBILE_THEME'];
	}
}

function save_prefs( ) {
	global $db, $addslashes;

	if ($_SESSION['valid_user']) {
		$data	= $addslashes(serialize($_SESSION['prefs']));
		$sql	= 'UPDATE '.TABLE_PREFIX.'members SET preferences="'.$data.'", creation_date=creation_date, last_login=last_login WHERE member_id='.$_SESSION['member_id'];
		$result = mysql_query($sql, $db); 
	}
}

function save_email_notification($mnot) {
    global $db;
    
    if ($_SESSION['valid_user']) {
        $sql = "UPDATE ".TABLE_PREFIX."members SET inbox_notify =". $mnot .", creation_date=creation_date, last_login=last_login WHERE member_id =".$_SESSION['member_id'];
        $result = mysql_query($sql, $db);
    }
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
	if (!($row = @mysql_fetch_assoc($result))) {
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
			$_my_uri .= htmlentities('?');
		} else if ($bits[$i] != ''){
			$_my_uri .= htmlentities(SEP);
		}
		$_my_uri .= $bits[$i];
	}
	if ($_my_uri == '') {
		$_my_uri .= htmlentities('?');
	} else {
		$_my_uri .= htmlentities(SEP);
	}
	$_my_uri = $_SERVER['PHP_SELF'].$_my_uri;

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
    ATutor.setcookie("flash",'',time()-3600);
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
			} elseif (isset($_REQUEST['p_course'])){
				// is set when guests access public course. @see bounce.php
				$course_id = $url_parser->getCourseDirName($_REQUEST['p_course']);
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
		} elseif (isset($_REQUEST['p_course'])){
			// is set when guests access public course. @see bounce.php
			$course_id = $url_parser->getCourseDirName($_REQUEST['p_course']);
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

	if (is_mobile_device()) {
		$default_status = 3;
	} else {
		$default_status = 2;
	}
	$sql	= "SELECT dir_name FROM ".TABLE_PREFIX."themes WHERE status=".$default_status;
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);

	return $row;
}

function get_system_default_theme() {
	if (is_mobile_device()) {
		return 'mobile';
	} else {
		return 'default';
	}
}

function is_mobile_theme($theme) {
	global $db;

	$sql	= "SELECT dir_name FROM ".TABLE_PREFIX."themes WHERE type='".MOBILE_DEVICE."'";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		if ($row['dir_name'] == $theme && is_dir(AT_INCLUDE_PATH . '../themes/' . $theme)) return true;
	}

	return false;
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
	global $db, $addslashes;

	if ($num_affected > 0) {
		$details = $addslashes(stripslashes($details));
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
		return substr($list, 0, -1); }
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

function is_mobile_device() {
	$http_user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
	return ((stripos($http_user_agent, IPOD_DEVICE) !== false && stripos($http_user_agent, IPOD_DEVICE) >= 0) ||
			(stripos($http_user_agent, IPHONE_DEVICE) !== false && stripos($http_user_agent, IPHONE_DEVICE) >= 0) ||
	        (stripos($http_user_agent, BLACKBERRY_DEVICE) !== false && stripos($http_user_agent, BLACKBERRY_DEVICE) >= 0) ||
	        (stripos($http_user_agent, IPAD_DEVICE) !== false && stripos($http_user_agent, IPAD_DEVICE) >= 0) ||
	        (stripos($http_user_agent, ANDROID_DEVICE) !== false && stripos($http_user_agent, ANDROID_DEVICE) >= 0)) 
	        ? true : false;
}

function get_mobile_device_type() {
	$http_user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
	if (stripos($http_user_agent, IPOD_DEVICE) !== false && stripos($http_user_agent, IPOD_DEVICE) >= 0) {
		return IPOD_DEVICE;
	} else if (stripos($http_user_agent, IPHONE_DEVICE) !== false && stripos($http_user_agent, IPHONE_DEVICE) >= 0) {
		return IPHONE_DEVICE;
	} else if (stripos($http_user_agent, BLACKBERRY_DEVICE) !== false && stripos($http_user_agent, BLACKBERRY_DEVICE) >= 0) {
		return BLACKBERRY_DEVICE;
	} else if (stripos($http_user_agent, IPAD_DEVICE) !== false && stripos($http_user_agent, IPAD_DEVICE) >= 0) {
		return IPAD_DEVICE;
	} else if (stripos($http_user_agent, ANDROID_DEVICE) !== false && stripos($http_user_agent, ANDROID_DEVICE) >= 0) {
		return ANDROID_DEVICE;
	} else {
		return UNKNOWN_DEVICE;
	}
}

/**
 * Convert all input to htmlentities output, in UTF-8.
 * @param	string	input to be convert
 * @param	boolean	true if we wish to change all newlines(\r\n) to a <br/> tag, false otherwise.  
 *			ref: http://php.net/manual/en/function.nl2br.php
 * @author	Harris Wong
 * @date	March 12, 2010
 */
function htmlentities_utf8($str, $use_nl2br=true){
	$return = htmlentities($str, ENT_QUOTES, 'UTF-8');
	if ($use_nl2br){
		return nl2br($return);
	} 
	return $return;
}

/**
 * Convert all '&' to '&amp;' from the input
 * @param   string  any string input, mainly URLs.
 * @return  input with & replaced to '&amp;'
 * @author  Harris Wong
 * @date    Oct 7, 2010
 */
function convert_amp($input){
    $input = str_replace('&amp;', '&', $input); //convert everything to '&' first
    return str_replace('&', '&amp;', $input);
}

/**
 * Check if json_encode/json_decode exists, if not, use the json service library.
 * NOTE:  json_encode(), json_decode() are NOT available piror to php 5.2
 * @author	Harris Wong
 * @date	April 21, 2010
 */
 if ( !function_exists('json_encode') ){
    function json_encode($content){
		require_once (AT_INCLUDE_PATH.'lib/json.inc.php');
		$json = new Services_JSON;               
        return $json->encode($content);
    }
}
if ( !function_exists('json_decode') ){
    function json_decode($content, $assoc=false){
		require_once (AT_INCLUDE_PATH.'lib/json.inc.php');
		if ( $assoc ){
			$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
        } else {
			$json = new Services_JSON;
		}
        return $json->decode($content);
    }
}

/*
 * Finds the image in the theme image folder first. If the image does not exist, look up in 
 * core image folder.
 * @param: image_name - The relative path and name to the image. 
 *             "Relative" means relative to the "image" folder, with subfolders and image name.
 *         actual_relative_path - Used when find_image() is called, for example in a class,
 *             which is then called by different scripts that give different AT_INCLUDE_PATH. However,
 *             the path to the image itself is consistent regardless of the caller script. This value
 *             should be the consistent relative path to the image itself.
 * @return: The path to the image in the theme folder, if exists. 
 *          Otherwise, the path to the image in the core image folder.
 * Example: 
 *   1. pass in "rtl_tree/tree/collapse.gif"
 *   2. return the path to this image in the theme folder: include/../themes/default/images/rtl_tree/tree/collapse.gif
 *      if the theme image does not exist, return the path to the image in the core "image" folder: include/../images/rtl_tree/tree/collapse.gif
 *      These pathes are relative to ATutor installation directory.
 */
function find_image($image_name, $actual_relative_path = AT_INCLUDE_PATH) {
	// The returned path is determined by AT_INCLUDE_PATH. If AT_INCLUDE_PATH is undefined, return the parameter itself.
	if (!defined('AT_INCLUDE_PATH')) return $image_name;
	
	// string concanation cannot be used at assigning parameter default value
	if ($actual_relative_path == AT_INCLUDE_PATH) $actual_relative_path .= '../';
	
	// remove leading "/"
	if (substr($image_name, 0, 1) == "/") $image_name = substr($image_name, 1);
	
	$theme_image_folder = 'themes/'.$_SESSION['prefs']['PREF_THEME'].'/images/';
	$atutor_image_folder = 'images/';
	
	// Use the path that is relative to AT_INCLUDE_PATH in the caller script, to check the existence of the image
	// but the return path is based on the pass-in actual path parameter.
	if (file_exists(AT_INCLUDE_PATH.'../'.$theme_image_folder.$image_name)) {
		return $actual_relative_path.$theme_image_folder.$image_name;
	} else {
		return $actual_relative_path.$atutor_image_folder.$image_name;
	}
}

require(AT_INCLUDE_PATH . '../mods/_core/modules/classes/Module.class.php');

$moduleFactory = new ModuleFactory(TRUE); // TRUE is for auto_loading the module.php files

if (isset($_GET['submit_language']) && $_SESSION['valid_user']) {
	if ($_SESSION['course_id'] == -1) {
		$sql = "UPDATE ".TABLE_PREFIX."admins SET language = '$_SESSION[lang]' WHERE login = '$_SESSION[login]'";
		$result = mysql_query($sql, $db);
	} else {
		$sql = "UPDATE ".TABLE_PREFIX."members SET language = '$_SESSION[lang]', creation_date=creation_date, last_login=last_login WHERE member_id = $_SESSION[member_id]";
		$result = mysql_query($sql, $db);
	}
}

if (isset($_SESSION['course_id']) && $_SESSION['course_id'] > 0) {
    $_custom_head .= '<script type="text/javascript" src="'.$_base_path.'jscripts/ATutorCourse.js"></script>';
}
?>
