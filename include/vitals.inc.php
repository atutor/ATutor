<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002 - 2012                                            */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }
include_once(AT_INCLUDE_PATH . 'lib/vital_funcs.inc.php');

define('AT_DEVEL', 0);
define('AT_ERROR_REPORTING', E_ERROR | E_WARNING | E_PARSE); // default is E_ALL ^ E_NOTICE, use E_ALL or E_ALL + E_STRICT for developing
//define('AT_ERROR_REPORTING', E_ALL + E_STRICT); // default is E_ALL ^ E_NOTICE, use E_ALL or E_ALL + E_STRICT for developing


define('AT_DEVEL_TRANSLATE', 0);

// Multisite constants and checks
define('AT_SITE_PATH', get_site_path());
define('AT_SUBSITE_THEME_DIR', realpath(AT_SITE_PATH . "themes") . "/");
define('AT_MULTISITE_CONFIG_FILE', AT_INCLUDE_PATH . 'config_multisite.inc.php');

// Inform IE6 Users They must upgrade
if(isset($_SERVER['HTTP_USER_AGENT'])){
    if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.') !== FALSE){
        header("Location: ie6.html");
    }
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
	error_reporting(0);
	if (!defined('AT_REDIRECT_LOADED')){
		include_once(AT_SITE_PATH . 'include/config.inc.php');
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
	     
    if(defined('MYSQLI_ENABLED')){
 	$db = at_db_connect(DB_HOST, DB_PORT, DB_USER, DB_PASSWORD, DB_NAME);   
    }else{
	$db = at_db_connect(DB_HOST, DB_PORT, DB_USER, DB_PASSWORD, '');
	at_db_select(DB_NAME, $db);
    }
	//$db = at_db_connect(DB_HOST, DB_PORT, DB_USER, DB_PASSWORD);
	//at_db_select(DB_NAME, $db);
}
// check if the subsite is enabled
if (defined('IS_SUBSITE') && IS_SUBSITE) {
	include_once(AT_INCLUDE_PATH . '../mods/manage_multi/lib/mysql_multisite_connect.inc.php');
	$db_tmp = $db;
	$db = $db_multisite;
	at_db_select(DB_NAME_MULTISITE, $db_multisite);
	$site_url = $_SERVER['HTTP_HOST'];
	$row = queryDB("SELECT * from %ssubsites where site_url = '%s'", array(TABLE_PREFIX_MULTISITE, $site_url), true);
	if (!$row['enabled']) {
		echo $site_url . ' has been disabled!';
		exit;
	}
	$db = $db_tmp;
}
/* get config variables. if they're not in the db then it uses the installation default value in constants.inc.php */

$rows = queryDB("SELECT * FROM %sconfig", array(TABLE_PREFIX));
foreach ($rows as $row) {
	$_config[$row['name']] = $row['value'];
}

/***** 3. start session initilization block *****/
if (headers_sent()) {
	require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
	$err = new ErrorHandler();
	trigger_error('VITAL#<br /><br /><code><strong>An error occurred. Output sent before it should have. Please correct the above error(s).' . '</strong></code><br /><hr /><br />', E_USER_ERROR);
}

@set_time_limit(0);
if($_config['session_timeout']){
	$_at_timeout = ($_config['session_timeout']*60);
}else {
	$_at_timeout = '1200'; // Default timeout is 20 minutes
}

@ini_set('session.gc_maxlifetime', $_at_timeout); 
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
	@putenv("TZ={$_config['time_zone']}");
}

/***** 6. load language *****/
// set current language
require(AT_INCLUDE_PATH . '../mods/_core/languages/classes/LanguageManager.class.php');
$languageManager = new LanguageManager();

$myLang = $languageManager->getMyLanguage();

if ($myLang === FALSE) {
	echo 'There are no languages installed!';
	exit;
}
$myLang->saveToSession();
if (isset($_GET['lang']) && $_SESSION['valid_user'] === true) {
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

/**************************************************/
/* load in message handler                        */
/**************************************************/
require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');
$msg = new Message($savant);

//if user has requested theme change, make the change here
if ((isset($_POST['theme']) || isset($_POST['mobile_theme'])) && isset($_POST['submit'])) {
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
} else if (isset($_POST['set_default'])) {
	// Once users select to reset theme to the default theme in user perference popup window,
	// apply the default theme immediate. See users/pref_wizard/index.php for
	// resetting other prefs.
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
	$_SESSION['prefs']['PREF_THEME'] = get_default_theme();
}

if ((!is_dir(AT_SYSTEM_THEME_DIR . $_SESSION['prefs']['PREF_THEME']) && 
     !is_dir(AT_SUBSITE_THEME_DIR . $_SESSION['prefs']['PREF_THEME'])) ||
    $_SESSION['prefs']['PREF_THEME'] == '') {
	$_SESSION['prefs']['PREF_THEME'] = get_system_default_theme();
}

// use "mobile" theme for mobile devices. For now, there's only one mobile theme and it's hardcoded.
// When more mobile themes come in, this should be changed.
if (isset($_SESSION['prefs']['PREF_THEME']) && isset($_SESSION['valid_user']) && $_SESSION['valid_user'] === true) {
	//check if the theme is enabled	
	$row = queryDB("SELECT status FROM %sthemes WHERE dir_name='%s'", array(TABLE_PREFIX, $_SESSION['prefs']['PREF_THEME']), true);
	if ($row['status'] == 0) {
		// get user defined default theme if the preference theme is disabled
		$default_theme = get_default_theme();
		if (!is_dir(AT_SYSTEM_THEME_DIR . $default_theme) && !is_dir(AT_SUBSITE_THEME_DIR . $default_theme)) {
			$default_theme = get_system_default_theme();
		}
		$_SESSION['prefs']['PREF_THEME'] = $default_theme;
    $msg->addError('THEME_PREVIEW_DISABLED');
	}
}

// find out where PREF_THEME is located
$main_theme_folder = get_main_theme_dir(is_customized_theme($_SESSION['prefs']['PREF_THEME']));

$savant->addPath('template', $main_theme_folder . $_SESSION['prefs']['PREF_THEME'] . '/');
require($main_theme_folder . '../themes/' . $_SESSION['prefs']['PREF_THEME'] . '/theme.cfg.php');

// Define the directory where the customized data lives (used by multi sites):
// Main site: [ATutor-root]
// Subsites: [ATutor-root]/sites/[Subsite-URL]/
$theme_path = "";
if (is_customized_theme($_SESSION['prefs']['PREF_THEME'])) {
	$theme_path = AT_SITES_DIR . $_SERVER['HTTP_HOST'] . '/';
}

define('AT_CUSTOMIZED_DATA_DIR', AT_BASE_HREF . $theme_path);


/**************************************************/
/* load in content manager                        */
/**************************************************/
if(isset($_SESSION['course_id'])){
$contentManager = new ContentManager($db, isset($_SESSION['course_id']) ? $_SESSION['course_id'] : $_GET['p_course']);
$contentManager->initContent();
}
/**************************************************/
require(AT_INCLUDE_PATH.'phpCache/phpCache.inc.php'); // cache library
require(AT_INCLUDE_PATH.'lib/utf8.php');			//UTF-8 multibyte library

if (!file_exists(AT_INCLUDE_PATH.'../sha-1factory.js')) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printErrors('MISSING_SHA1');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

if (isset($_user_location) && ($_user_location == 'users') && $_SESSION['valid_user'] === true && ($_SESSION['course_id'] > 0)) {
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
		header('Location: '.AT_BASE_HREF.'bounce.php?course='.$_pretty_url_course_id.SEP.'pu='.$_SERVER['PATH_INFO'].urlencode('?'.$_SERVER['QUERY_STRING']), TRUE, 301);
	} else {
		header('Location: '.AT_BASE_HREF.'bounce.php?course='.$_pretty_url_course_id.SEP.'pu='.$_SERVER['PATH_INFO'], TRUE, 301);
	}
	exit;
}

/********************************************************************/
/* the system course information									*/
/* system_courses[course_id] = array(title, description, subject)	*/
$system_courses = array();

// temporary set to a low number
$rows = queryDB('SELECT * FROM %scourses ORDER BY title', array(TABLE_PREFIX));
foreach($rows as $row){
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

if (isset($_SESSION['course_id']) && $_SESSION['course_id'] > 0 || isset($_REQUEST['p_course']) && $_REQUEST['p_course'] > 0) {
	$this_course_id = ($_SESSION['course_id']>0 ? $_SESSION['course_id'] : $_REQUEST['p_course']);
	$rows_g = queryDB('SELECT * FROM %sglossary WHERE course_id=%d ORDER BY word', array(TABLE_PREFIX, $this_course_id));
	$glossary = array();
	$glossary_ids = array();
	
	foreach($rows_g as $row_g){
		$row_g['word'] = htmlspecialchars($row_g['word'], ENT_QUOTES, 'UTF-8');
		$glossary[$row_g['word']] = str_replace("'", "\'",$row_g['definition']);
		$glossary_ids[$row_g['word_id']] = $row_g['word'];
		
		// a kludge to get the related id's for when editing content 
		// it's ugly, but beats putting this query AGAIN on the edit_content.php page 
		if (isset($get_related_glossary)) {
			$glossary_ids_related[$row_g['word']] = $row_g['related_word_id'];
		}
	}
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
			$is_tracked = queryDB('SELECT * FROM %smember_track WHERE member_id=%d AND content_id=%d',array(TABLE_PREFIX, $_SESSION['member_id'],$_SESSION['s_cid'] ));
			if(count($is_tracked) != 0){
				$sql = "UPDATE %smember_track SET counter=counter+1, duration=duration+$diff, last_accessed=NOW() WHERE member_id=%d AND content_id=%d";
				$rows = queryDB($sql,array(TABLE_PREFIX, $_SESSION['member_id'],$_SESSION['s_cid'] ));
			} else{
				$result = queryDB("INSERT INTO %smember_track VALUES (%d, %d, %d, 1, %d, NOW())", array(TABLE_PREFIX, $_SESSION['member_id'], $_SESSION['course_id'],$_SESSION['s_cid'], $diff ));
			}
		}
		$_SESSION['cid_time'] = 0;
}


/****************************************************/
/* update the user online list						*/
if (isset($_SESSION['valid_user']) && $_SESSION['valid_user'] === true) {
	$new_minute = time()/60;
	if (!isset($_SESSION['last_updated'])) {
		$_SESSION['last_updated'] = $new_minute;
	}
	$diff = abs($_SESSION['last_updated'] - $new_minute);
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
	"ATutor".setcookie("flash",'',time()-3600);
}

if (!isset($_SESSION["flash"])) {
	$_custom_head .= '    <script type="text/javascript" src="'.AT_print($_base_path, 'url.base').'jscripts/flash_detection.js"></script>';
}

/*~~~~~~~~~~~~~~end flash detection~~~~~~~~~~~~~~~*/

if (isset($_GET['expand'])) {
	$_SESSION['menu'][intval($_GET['expand'])] = 1;
} else if (isset($_GET['collapse'])) {
	unset($_SESSION['menu'][intval($_GET['collapse'])]);
}

require(AT_INCLUDE_PATH . '../mods/_core/modules/classes/Module.class.php');

$moduleFactory = new ModuleFactory(TRUE); // TRUE is for auto_loading the module.php files

if (isset($_GET['submit_language']) && $_SESSION['valid_user'] === true) {
	if ($_SESSION['course_id'] == -1) {
		$sql = "UPDATE %sadmins SET language = %s WHERE login = %s";
		$row = queryDB($sql,array(TABLE_PREFIX, $_SESSION['lang'], $_SESSION['login']));
	} else {
		$sql = "UPDATE %smembers SET language = %s, creation_date=creation_date, last_login=last_login WHERE member_id = %d";
		$row = queryDB($sql,array(TABLE_PREFIX, $_SESSION['lang'], $_SESSION['member_id']));
	}
}

if (isset($_SESSION['course_id']) && $_SESSION['course_id'] > 0) {
	$_custom_head .= '    <script type="text/javascript" src="'.AT_print($_base_path, 'url.base').'jscripts/ATutorCourse.js"></script>';
}
?>
