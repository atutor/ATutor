<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$
if (!defined('AT_INCLUDE_PATH')) { exit; }

define('AT_DEVEL', 1);
define('AT_DEVEL_TRANSLATE', 0);

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


/********************************************/
/* timing stuff								*/
if (defined('AT_DEVEL') && AT_DEVEL) {
	$microtime = microtime();
	$microsecs = substr($microtime, 2, 8);
	$secs = substr($microtime, 11);
	$startTime = "$secs.$microsecs";
}
/********************************************/

/**** 0. start system configuration options block ****/
	error_reporting(0);
		include(AT_INCLUDE_PATH.'config.inc.php');
	error_reporting(E_ALL ^ E_NOTICE);

	if (!defined('AT_INSTALL') || !AT_INSTALL) {
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Pragma: no-cache');

		$relative_path = substr(AT_INCLUDE_PATH, 0, -strlen('include/'));
		header('Location: ' . $relative_path . 'install/not_installed.php');
		exit;
	}
/*** end system config block ****/


require(AT_INCLUDE_PATH.'lib/constants.inc.php'); // 1. constants

/***** 2. start session initilization block ****/
	if (headers_sent()) {
		require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
		$err =& new ErrorHandler();
		trigger_error('VITAL#<br /><br /><code><strong>An error occurred. ' .
						'Output sent before it should have. Please correct the above error(s).' .
						'</strong></code><br /><hr /><br />', E_USER_ERROR);
		//echo '<br /><br /><code><strong>An error occurred. Output sent before it should have. Please correct the above error(s).</strong></code><br /><hr /><br />';
	}

	@set_magic_quotes_runtime(0);
	@set_time_limit(0);
	@ini_set('session.gc_maxlifetime', '36000'); /* 10 hours */

	@session_cache_limiter('private, must-revalidate');
	session_name('ATutorID');
	error_reporting(E_ALL ^ E_NOTICE);

	if (headers_sent()) {
		//echo '<br /><code><strong>Headers already sent. Cannot initialise session.</strong></code><br /><hr /><br />';
		
		require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
		$err =& new ErrorHandler();
		trigger_error('VITAL#<br /><code><strong>Headers already sent. ' .
						'Cannot initialise session.</strong></code><br /><hr /><br />', E_USER_ERROR);
		exit;
	}

	ob_start();
		session_start();
		$str = ob_get_contents();
	ob_end_clean();

	if ($str) {
		require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
		$err =& new ErrorHandler();
		trigger_error('VITAL#<br /><code><strong>Error initializing session. ' .
						'Please varify that session.save_path is correctly set in your php.ini file ' .
						'and the directory exists.</strong></code><br /><hr /><br />', E_USER_ERROR);
		//echo '<br /><code><strong>Error initializing session. Please varify that session.save_path is correctly set in your php.ini file and the directory exists.</strong></code><br /><hr /><br />';
		exit;
	}

	if (!isset($_SESSION['course_id']) && !isset($_SESSION['valid_user']) && ($_user_location != 'public')) {
		header('Location: '.$_base_href.'login.php');
		exit;
	}
/***** end session initilization block ****/

// 4. enable output compression, if it isn't already enabled:
if ((@ini_get('output_handler') == '') && (@ini_get('zlib.output_handler') == '')) {
	@ini_set('zlib.output_compression', 1);
}

/* 5. database connection */
if (AT_INCLUDE_PATH !== 'NULL') {
	$db = @mysql_connect(DB_HOST . ':' . DB_PORT, DB_USER, DB_PASSWORD);
	if (!$db) {
		/* AT_ERROR_NO_DB_CONNECT */
		require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
		$err =& new ErrorHandler();
		trigger_error('VITAL#Unable to connect to db.', E_USER_ERROR);
		//echo 'Unable to connect to db.';
		exit;
	}
	if (!@mysql_select_db(DB_NAME, $db)) {
		require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
		$err =& new ErrorHandler();
		//echo 'DB connection established, but database "'.DB_NAME.'" cannot be selected.';
		trigger_error('VITAL#DB connection established, but database "'.DB_NAME.'" cannot be selected.',
						E_USER_ERROR);
		exit;
	}

	/* development uses a common language db */
	if (file_exists(AT_INCLUDE_PATH.'cvs_development.inc.php')) {
		require(AT_INCLUDE_PATH.'cvs_development.inc.php');
	} else {
		define('TABLE_PREFIX_LANG', TABLE_PREFIX);
		define('AT_CVS_DEVELOPMENT', '');
		define('TABLE_SUFFIX_LANG', '');

		$lang_db =& $db;
	}
}

require(AT_INCLUDE_PATH.'phpCache/phpCache.inc.php'); // 6. cache library

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
	$myLang->sendContentTypeHeader();

	/* set right-to-left language */
	$rtl = '';
	if ($myLang->isRTL()) {
		$rtl = 'rtl_'; /* basically the prefix to a rtl variant directory/filename. eg. rtl_tree */
	}

	if (AT_DEVEL_TRANSLATE) {
		require_once(AT_INCLUDE_PATH . 'classes/Language/LanguageEditor.class.php');
		$langEditor =& new LanguageEditor($myLang);
	}
/***** end language block ****/

/* 8. load common libraries */
require(AT_INCLUDE_PATH.'classes/ContentManager.class.php');  /* content management class */
require_once(AT_INCLUDE_PATH.'lib/output.inc.php');                /* output functions */

require(AT_INCLUDE_PATH.'classes/Savant2/Savant2.php');         /* for the theme and template management */

// set default template paths:
$conf = array('template_path' => AT_INCLUDE_PATH . '../themes/default/');
$savant =& new Savant2($conf);


require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');
$msg =& new Message($savant);

$contentManager = new ContentManager($db, $_SESSION['course_id']);
$contentManager->initContent( );
/**************************************************/

if (($_SESSION['course_id'] == 0) && ($_user_location != 'users') && ($_user_location != 'prog') && !$_GET['h'] && ($_user_location != 'public')) {
	header('Location:'.$_base_href.'users/');
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
if ( !($et_l=cache(120, 'system_courses', 'system_courses')) ) {

	$sql = 'SELECT * FROM '.TABLE_PREFIX.'courses ORDER BY title';
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$course = $row['course_id'];
		unset($row['course_id']);
		$system_courses[$course] = $row;
	}
	cache_variable('system_courses');
	endcache(true, false);
}
/*																	*/
/********************************************************************/

/**********
 * the learning concepts
 * $learning_concepts[concept_id] = array (title, description, icon_name)
 * $learning_concept_tags[tag]	  = array (concept_id, title, description, icon_name)
 */
$learning_concept_tags = array();
if ($_SESSION['course_id'] > 0) {
	if ( !($et_lc = cache(0, 'learning_concepts', $_SESSION['course_id'])) ) {
		$sql = 'SELECT tag FROM '.TABLE_PREFIX.'learning_concepts WHERE course_id=0 OR course_id='.$_SESSION['course_id'].' ORDER BY tag';
		$result = mysql_query($sql,$db);
		while ($row = mysql_fetch_assoc($result)) {
			$learning_concept_tags[] = $row['tag'];
		}

		cache_variable('learning_concept_tags');
		endcache(true, false);
	} /* end learning concepts cache */
}

if ($_SESSION['course_id'] != 0) {
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

function &get_html_body($text) {
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
	if ($_SESSION['member_id'] == 0) {
		return;
	}
	global $db;

    $expiry = time() + 900; // 15min
    $sql    = 'REPLACE INTO '.TABLE_PREFIX.'users_online VALUES ('.$_SESSION['member_id'].', '.$_SESSION['course_id'].', "'.$_SESSION['login'].'", '.$expiry.')';
    $result = mysql_query($sql, $db);

	/* garbage collect and optimize the table every so often */
	mt_srand((double) microtime() * 1000000);
	$rand = mt_rand(1, 20);
	if ($rand == 1) {
		$sql = 'DELETE FROM '.TABLE_PREFIX.'users_online WHERE expiry<'.time();
		$result = @mysql_query($sql, $db);

		$sql = 'OPTIMIZE TABLE '.TABLE_PREFIX.'users_online';
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
	global $db;

	$id		= intval($id);

	$sql	= 'SELECT login FROM '.TABLE_PREFIX.'members WHERE member_id='.$id;
	$result	= mysql_query($sql, $db);
	$row	= mysql_fetch_assoc($result);

	return $row['login'];
}

function get_forum_name($fid){
	global $db;

	$fid = intval($fid);

	$sql	= 'SELECT title FROM '.TABLE_PREFIX.'forums WHERE forum_id='.$fid;
	$result	= mysql_query($sql, $db);
	$row	= mysql_fetch_assoc($result);

	return $row['title'];
}

/* returns the theme settings and info */
/* there is NO error correction/detecting going on! beware! */
function get_theme_info($theme) {
	global $_base_path;

	$theme_image_path = $_base_path . 'themes/'. $theme . '/images/';

	@include (AT_INCLUDE_PATH . '../themes/'.$theme.'/theme.cfg.php');


	$_theme['admin_nav']  = $admin_nav;
	$_theme['pub_nav']    = $pub_nav;
	$_theme['user_nav']   = $user_nav;
	$_theme['nav']        = $nav;
	$_theme['parent_dir'] = $parent_dir;

	if ($theme) {
		return $_theme;
	}

	return false;
}

	/* defaults: */
	if(!$_SESSION['valid_user'] && count($_SESSION['prefs'] < 3)){
		$temp_prefs = get_prefs(AT_DEFAULT_THEME);
		assign_session_prefs($temp_prefs);
	} else if (($_SESSION['prefs']['PREF_MAIN_MENU_SIDE'] == '' && $_SESSION['valid_user'] )) {
		$temp_prefs = get_prefs(AT_DEFAULT_THEME);
		assign_session_prefs($temp_prefs);
		save_prefs();
	}
	if (!isset($_SESSION['prefs']['PREF_STACK'])) {
		$_SESSION['prefs'][PREF_STACK] = array(0, 1, 2, 3, 4);
	}

	if (!$_SESSION['prefs']['PREF_THEME'] || is_numeric($_SESSION['prefs']['PREF_THEME'])) {
		$_SESSION['prefs']['PREF_THEME'] = 'default';
	}

	/* takes the array of valid prefs and assigns them to the current session */
	function assign_session_prefs ($prefs) {
		if (is_array($prefs)) {
			foreach($prefs as $pref_name => $value) {
				$_SESSION['prefs'][$pref_name] = $value;
			}
		}
	}
	/* returns the unserialized prefs array */
	function get_prefs($pref_id) {
		global $db;

		$sql	= 'SELECT preferences FROM '.TABLE_PREFIX.'theme_settings WHERE theme_id='.$pref_id;
		$result	= mysql_query($sql, $db);

		if ($row = @mysql_fetch_assoc($result)) {
			return unserialize(stripslashes($row['preferences']));
		}

		return false;
	}


/****************************************************/
/* change menu state								*/
if (isset($_GET['enable'])) {
	$_SESSION['prefs'][$_GET['enable']] = 1;
	save_prefs();

} else if (isset($_GET['disable'])) {
	$_SESSION['prefs'][$_GET['disable']] = 0;
	save_prefs();

} else if (isset($_GET['expand'])) {
	$_SESSION['menu'][intval($_GET['expand'])] = 1;

} else if (isset($_GET['collapse'])) {
	unset($_SESSION['menu'][intval($_GET['collapse'])]);
}

if (isset($_GET['cid'])) {
	$_SESSION['s_cid'] = intval($_GET['cid']);
}

function save_prefs( ) {
	global $db;

	if ($_SESSION['valid_user']) {
		$data	= addslashes(serialize($_SESSION['prefs']));
		$sql	= 'UPDATE '.TABLE_PREFIX.'members SET preferences="'.$data.'" WHERE member_id='.$_SESSION['member_id'];
		$result = mysql_query($sql, $db); 
	}
 
	/* else, we're not a valid user so nothing to save. */
}

   /**
   * Encodes a feedback code.
   * @access  public
   * @param   mixed $f		$f may be an array of feedback codes, where additionally, 
   *						each feedback code may be an array consisting of supplementary arguments.
   * @return  Returns		a URL safe encoding of a feedback code.
   * @author  Joel Kronenberg
   */
function urlencode_feedback($f) {
	if (is_array($f)) {
		return urlencode(serialize($f));
	}
	return $f;
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

	$sql = "UPDATE ".TABLE_PREFIX."course_enrollment SET last_cid=$cid WHERE course_id=$_SESSION[course_id]";
	mysql_query($sql, $db);
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
	if (($row = mysql_fetch_assoc($result)) && $row['status']) {
		$is_instructor = true;
	}

	return $is_instructor;
}

/****************************************************/
/* update the user online list						*/
	$new_minute = time()/60;
	$diff       = abs($_SESSION['last_updated'] - $new_minute);
	if ($diff > ONLINE_UPDATE) {
		$_SESSION['last_updated'] = $new_minute;
		add_user_online();
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
			||	(strpos($bits[$i], 'menu_jump=')=== 0)
			||	(strpos($bits[$i], 'g=')		=== 0)
			||	(strpos($bits[$i], 'collapse=')	=== 0)
			||	(strpos($bits[$i], 'f=')		=== 0)
			||	(strpos($bits[$i], 'e=')		=== 0)
			||	(strpos($bits[$i], 'save=')		=== 0)
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

/****************************************************/
/* update the g database							*/
if (!isset($_GET['cid'])) {
	$_GET['cid'] = 0;
}
if (!isset($_POST['g'])) {
	$_POST['g'] = 0;
}
if (!isset($_GET['g'])) {
	$_GET['g'] = 0;
}

	$new_cid = intval($_GET['cid']);
	$g		 = intval($_POST['g']);
	if ($g === 0) {
		$g = intval($_GET['g']);
	}

	if ($_SESSION['track_me']
		&& $_SESSION['valid_user']
		&& !authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN) 
		&& ($g !== 0)
		&& $_SESSION['course_id'])
	{
		$now = time();
		$sql	= 'INSERT INTO '.TABLE_PREFIX.'g_click_data VALUES ('.$_SESSION['member_id'].','.
					$_SESSION['course_id'].','.$_SESSION['from_cid'].','.$new_cid.','.$g.','.$now.',0)';

		$result = @mysql_query($sql, $db);
		if (isset($_SESSION['pretime'])){
			// calculate duration and update the previous record
			$sql = 'UPDATE '.TABLE_PREFIX.'g_click_data SET duration=('.$now.' - timestamp) WHERE timestamp='.$_SESSION['pretime'].' AND member_id='.$_SESSION['member_id'];
			@mysql_query($sql, $db);
		}
		$_SESSION['pretime'] = $now;

	}
/****************************************************/

$_SESSION['from_cid'] = intval($_GET['cid']);

function my_add_null_slashes( $string ) {
    return ( $string );
}

if ( get_magic_quotes_gpc() == 1 ) {
	$addslashes = 'my_add_null_slashes';
} else {
	$addslashes = 'addslashes';
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
		return ( $bitfield & $bit ) ? true : false;
	} 


foreach($_privs as $key => $val) {
	define($val['name'], $key);
	$_privs[$key]['name'] = _AT(substr(strtolower($val['name']), 3));
}
asort($_privs);
reset($_privs);


/**
* Checks to see if the enable/disable editor toggle link in the menu should be displayed
* @access  public
* @param   none
* @return  bool	true if display editor pen links, false otherwise.
* @see     $_privs[]	  in include/lib/constants.inc.php
* @see	   authenticate() in include/vitals.inc.php
* @author  Heidi Hazelton
*/
function show_pen() {

	if (authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)) {
		return true;
	}

	if (!$_SESSION['privileges']) {
		return false;
	}

	global $_privs;

	// check for session priv
	foreach($_privs as $key => $val) {
		if (authenticate($key, AT_PRIV_RETURN)) {
			if ($val['pen']) {
				return true;
			}
		}
	}
	return false;
}

/**
* Checks to see if a given privilege is displayed on the Tools page or not (and thus require the "Instructor Tools" header)
* @access  public
* @param   none
* @return  bool	true if needs instructor tools header, false otherwise.
* @see     $_privs[]	  in include/lib/constants.inc.php
* @see	   authenticate() in include/vitals.inc.php
* @author  Heidi Hazelton
*/
function show_tool_header() {
	if (authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)) {
		return true;
	}
	if (!$_SESSION['privileges']) {
		return false;
	}

	global $_privs;

	// check for session priv
	foreach($_privs as $key => $val) {
		if (authenticate($key, AT_PRIV_RETURN)) {
			if ($val['tools']) {
				return true;
			}
		}
	}
	return false;
}

/**
* Authenticates the current user against the specified privilege.
* @access  public
* @param   int	$privilege		privilege to check against.
* @param   bool	$check			whether or not to return the result or to abort/exit.
* @return  bool	true if this user is authenticated, false otherwise.
* @see     $_privs[]   in include/lib/constants.inc.php
* @see	   query_bit() in include/vitals.inc.php
* @author  Joel Kronenberg
*/
function authenticate($privilege, $check = false) {
	if (!$_SESSION['valid_user']) {
		return false;
	}
	if ($_SESSION['is_admin']) {
		return true;
	}
	$auth = query_bit($_SESSION['privileges'], $privilege);

	if (!$auth && $check) {
		return false;
	} else if (!$auth && !$check) {
		exit;
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
?>