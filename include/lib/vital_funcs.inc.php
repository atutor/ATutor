<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002-2012                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }

/* test for mysqli presence */
if(function_exists('mysqli_connect')){
	define('MYSQLI_ENABLED', 1);
} 


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
	if(isset($_SESSION['isLoggedOutRecently'])){
		// Skip the check_session check for the case of just logged out user, 
		// as the session by all means doesnot have any extra privileges which 
		// can be hijacked. And we get to regenerate the session, and do not 
		// miss out on displaying the feedback messages added by different 
		// parts of code.
		// Previously absence of this check for a just logged out user,
		// triggered a fake possible-CSRF alarm, and hence messages in feedback were
		// not being rendered
		regenerate_session();
		unset($_SESSION['isLoggedOutRecently']);
		return true;
	}
	if(isset($_SESSION['OBSOLETE']) && ($_SESSION['EXPIRES'] < time())) {
		return false;
	}
	            
	if(isset($_SESSION['IPaddress']) != $_SERVER['REMOTE_ADDR']) {
		return false;
	}
	            
	if($_SESSION['userAgent'] != $_SERVER['HTTP_USER_AGENT']) {
		return false;
	}
	            
	if(!isset($_SESSION['OBSOLETE'])) {
		regenerate_session();
	}
	return true;
}

function get_site_path(){
	// Define Multisite paths
	$docroot_path = $_SERVER['DOCUMENT_ROOT'];
	$domain = $_SERVER['HTTP_HOST'];
	$site_path = realpath($docroot_path . '/../' . $domain . '/');
	
	if(file_exists($site_path."/include/config.inc.php") && !file_exists($site_path . "/login.php")){
		// The request is from a subdomain
		define('IS_SUBSITE', true);
		return $site_path . '/';
	}
	
	// The request is from the main site, return the current path
	return AT_INCLUDE_PATH . "../";
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
}


function add_user_online() {
	if (!isset($_SESSION['member_id']) || !($_SESSION['member_id'] > 0)) {
		return;
	}
	global $addslashes;

	$expiry = time() + 900; // 15min
	
	$sql = "REPLACE INTO %susers_online VALUES (%d, %d, '%s', %d)";
	$user_name = get_display_name($_SESSION['member_id']);
	$result = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id'], $_SESSION['course_id'], $user_name, $expiry));
	
	/* garbage collect and optimize the table every so often */
	mt_srand((double) microtime() * 1000000);
	$rand = mt_rand(1, 20);
	if ($rand == 1) {
		$sql = 'DELETE FROM %susers_online WHERE expiry<'.time();
		$result = queryDB($sql, array(TABLE_PREFIX));
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
	global  $_config_defaults;

	if (is_array($id)) {
		$id		= implode(',',$id);
		$rows = array();
		$sql	= "SELECT login, member_id FROM %smembers WHERE member_id IN (%s) ORDER BY login";
		$result = queryDB($sql, array(TABLE_PREFIX, $id));
		
		foreach($result as $row){
		    $rows[$row['member_id']] = $row['login'];
		}

		return $rows;
	} else {
		$id		= intval($id);
		$sql	= 'SELECT login FROM %smembers WHERE member_id=%d';
		$row    = queryDB($sql, array(TABLE_PREFIX, $id), TRUE);
		return $row['login'];
	}
}

function get_display_name($id) {
	static $_config, $display_name_formats;
	if (!$id) {
		return $_SESSION['login'];
	}

	if (!isset($db, $_config)) {
		global $db, $_config, $display_name_formats;
	}

	if (substr($id, 0, 2) == 'g_' || substr($id, 0, 2) == 'G_'){
		$sql = "SELECT name FROM %sguests WHERE guest_id='%d'";
        $row = queryDB($sql, array(TABLE_PREFIX, $id), TRUE);
		return _AT($display_name_formats[$_config['display_name_format']], '', $row['name'], '', '');
	}else{
		$sql    = "SELECT login, first_name, second_name, last_name FROM %smembers WHERE member_id='%d'";
        $row    = queryDB($sql, array(TABLE_PREFIX, $id), TRUE);
		return _AT($display_name_formats[$_config['display_name_format']], $row['login'], $row['first_name'], $row['second_name'], $row['last_name']);
	}
}

function get_forum_name($fid){

	$fid = intval($fid);

	$sql	= 'SELECT title FROM %sforums WHERE forum_id=%d';
	$row	= queryDB($sql, array(TABLE_PREFIX, $fid), TRUE);

	if(isset($row['title']) && $row['title'] != ''){
	   return $row['title'];
	}

	$sql = "SELECT group_id FROM %sforums_groups WHERE forum_id=%d";
	$row	= queryDB($sql, array(TABLE_PREFIX, $fid), TRUE);	

	if(isset($row['group_id'])){
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
	//if (is_mobile_device() && $switch_mobile_theme) {
	//	$_SESSION['prefs']['PREF_THEME'] = $_SESSION['prefs']['PREF_MOBILE_THEME'];
	//}
}

function save_prefs( ) {
	global $addslashes;

	if ($_SESSION['valid_user'] === true) {
		$data	= $addslashes(serialize($_SESSION['prefs']));

		$sql = 'UPDATE %smembers SET preferences="%s", creation_date=creation_date, last_login=last_login WHERE member_id=%d';
		$result = queryDB($sql, array(TABLE_PREFIX, $data, $_SESSION['member_id']));
	}
}

function save_email_notification($mnot) {
	
	if ($_SESSION['valid_user'] === true) {
		
		$sql = "UPDATE %smembers SET inbox_notify = %d, creation_date=creation_date, last_login=last_login WHERE member_id = %d";
		$result = queryDB($sql, array(TABLE_PREFIX, $mnot, $_SESSION['member_id']));
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

	$_SESSION['s_cid'] = intval($_GET['cid']);

	if (!$_SESSION['is_admin']   && 
		!$_SESSION['privileges'] && 
		!isset($in_get)          && 
		!$_SESSION['cid_time']   && 
		($_SESSION['course_id'] > 0) ) 
		{
			$_SESSION['cid_time'] = time();
	}
	
	$sql = "UPDATE %scourse_enrollment SET last_cid=%d WHERE course_id=%d AND member_id=%d";
	queryDB($sql, array(TABLE_PREFIX, $cid, $_SESSION['course_id'],$_SESSION['member_id']));
	
	 
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

	$is_instructor = false;
	
	$sql = 'SELECT status FROM %smembers WHERE member_id=%d';
	$row = queryDB($sql, array(TABLE_PREFIX,$_SESSION['member_id']), TRUE);
	
	if(!isset($row['status']) || $row['status'] == '' ){
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
		    if(isset($_SESSION['course_id'])){
			$course_id = $_SESSION['course_id'];
			}
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
	if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
		return true;
	}
    if(isset($_SESSION['privileges'])){
	    $auth = query_bit($_SESSION['privileges'], $privilege);
	}
	if (!isset($_SESSION['valid_user']) || $_SESSION['valid_user'] === false || !$auth) {
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
/**
 * Check if referer is in the $_pages array to prevent CSRF access
 * @access public
 * @return error message access denied
 */
function check_referer(){
    global $_pages, $_base_href, $msg;
    if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] !='' && $_SERVER['HTTP_REFERER'] != $_SERVER['PHP_SELF']){
        $referer_script = preg_replace('#'.$_base_href.'#', '', $_SERVER['HTTP_REFERER']);
        if( !in_array($_pages[$referer_script], $_pages)){
			$msg->addError('ACCESS_DENIED');
			require(AT_INCLUDE_PATH.'header.inc.php'); 
			require(AT_INCLUDE_PATH.'footer.inc.php'); 
			exit;
        }
    }
}
/**
 * Check if the give theme is a subsite customized theme. Return true if it is, otherwise, return false
 * @access public
 * @param string $theme_name
 * @return true or false
 */
function is_customized_theme($theme_name) {
	
	$sql = "SELECT customized FROM %sthemes WHERE dir_name = '%s'";
	$row = queryDB($sql, array(TABLE_PREFIX, $theme_name), TRUE);
	
	return !!$row["customized"];
}

/**
 * Return the main theme path based on the "customized" flag
 * @access  private
 * @param   int customized   whether this is a customized theme
 * @return  string           main theme folder, 
 *          for example, 
 *          for subsite-specific customized theme, return "[Document_root]/sub-site/themes"
 *          for main site theme, return "[Document_root]/main-site/themes/"
 */
function get_main_theme_dir($customized) {
	if ($customized) {
		return AT_SUBSITE_THEME_DIR;
	} else {
		return AT_SYSTEM_THEME_DIR;
	}
}

/**
 * Return the directory name of the user-defined default theme
 * @param: none
 * @return: string - the directory name of the user-defined default theme
 */
function get_default_theme() {

	if (is_mobile_device()) {
		//$default_status = 3;
		if($_SESSION['prefs']['PREF_RESPONSIVE'] == 1 || $_COOKIE['responsive'] == 1){
		    $default_status = 2;
		}else{
		    $default_status = 3;
		}
	} else {
		$default_status = 2;
	}

	$sql = "SELECT dir_name FROM %sthemes WHERE status=%d";
	$row = queryDB($sql, array(TABLE_PREFIX, $default_status), TRUE);
	
	return $row['dir_name'];
}

function get_system_default_theme() {
	if (is_mobile_device()) {
		if($_SESSION['prefs']['PREF_RESPONSIVE'] == 1 || $_COOKIE['responsive'] == 1){
		    return 'default';
		}else{
		    return 'mobile';
		}
	} else {
		return 'default';
	}
}

function is_mobile_theme($theme) {
	if($_SESSION['prefs']['PREF_RESPONSIVE']  == 1 || $_COOKIE['responsive'] == 1){
	    return false;
	}
	$sql	= "SELECT dir_name FROM %sthemes WHERE type='%s'";
	$rows = queryDB($sql, array(TABLE_PREFIX, MOBILE_DEVICE));	
	
	foreach ($rows as $row) {
		if ($row['dir_name'] == $theme && 
		    (is_dir(AT_SYSTEM_THEME_DIR . $theme) ||
		     is_dir(AT_SUBSITE_THEME_DIR . $theme))) {
			return true;
		}
    }
	return false;
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
	global  $addslashes;

	if ($num_affected > 0) {
		$details = $addslashes(stripslashes($details));		
        $sql    = "INSERT INTO %sadmin_log VALUES ('%s', NULL, '%d', '%s', %d, '%s')";
		$result = queryDB($sql, array(TABLE_PREFIX, $_SESSION['login'], $operation_type, $table_name, $num_affected, $details));			
	}
}

function get_group_title($group_id) {

	$sql = "SELECT title FROM %sgroups WHERE group_id=%d";
	$row = queryDB($sql, array(TABLE_PREFIX, $group_id), TRUE);
	
	if(isset($row['title']) && $row['title'] != ''){
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
 * To resize course_icon images
 * @param   uploaded image source path
 * @param   uploaded image path to be saved as
 * @param   uploaded image's height
 * @param   uploaded image width
 * @param   save file with this height
 * @param   save file with this width
 * @param   file extension type
 * @param   x-coordinate of source image
 * @param   y-coordinate of source image
 * @return  true if successful, false otherwise
 */

function resize_image($src, $dest, $src_h, $src_w, $dest_h, $dest_w, $type, $src_x=0, $src_y=0) {
	$thumbnail_img = imagecreatetruecolor($dest_w, $dest_h);

	if ($type == 'gif') {
		$source = imagecreatefromgif($src);
	} else if ($type == 'jpg') {
		$source = imagecreatefromjpeg($src);
	} else {
		$source = imagecreatefrompng($src);
	}

	if ($src_x > 0 || $src_y > 0){
		$result = imagecopyresized($thumbnail_img, $source, 0, 0, $src_x, $src_y, $dest_w, $dest_h, $src_w, $src_h);
	} else {
		$result = imagecopyresampled($thumbnail_img, $source, $src_x, $src_y, 0, 0, $dest_w, $dest_h, $src_w, $src_h);
	}

	if ($type == 'gif') {
		$result &= imagegif($thumbnail_img, $dest);
	} else if ($type == 'jpg') {
		$result &= imagejpeg($thumbnail_img, $dest, 75);
	} else {
		$result &= imagepng($thumbnail_img, $dest, 7);
	}
    return $result;
}

/**
 * get_group_concat
 * returns a list of $field values from $table using $where_clause, separated by $separator.
 * uses mysql's GROUP_CONCAT() if available and if within the limit (default is 1024), otherwise
 * it does it the old school way.
 * returns the list (as a string) or (int) 0, if none found.
 */
function get_group_concat($table, $field, $where_clause = 1, $separator = ',') {
	global $_config;
	
	if (!isset($_config['mysql_group_concat_max_len'])) {

		$sql = "SELECT  @@global.group_concat_max_len AS max";
	    $row = queryDB($sql, '', TRUE); 

		if (isset($row) && $row != '') {
			$_config['mysql_group_concat_max_len'] = $row['max'];
		} else {
			$_config['mysql_group_concat_max_len'] = 0;
		}		
		
		$sql = "REPLACE INTO %sconfig VALUES ('mysql_group_concat_max_len', '{$_config['mysql_group_concat_max_len']}')";
		queryDB($sql, array(TABLE_PREFIX));
	}
	if ($_config['mysql_group_concat_max_len'] > 0) {
	
		$sql = "SELECT GROUP_CONCAT(%s SEPARATOR '%s') AS list FROM %s%s WHERE %s";
		$rows = queryDB($sql, array($field,$separator, TABLE_PREFIX, $table, $where_clause));
		
		foreach($rows as $row){
            if (!$row['list']) {
                    return 0; // empty
            } else if ($row['list'] && strlen($row['list']) < $_config['mysql_group_concat_max_len']) {
                    return $row['list'];
            } // else: list is truncated, do it the old way
		}
	} // else:

	$list = '';
	$sql = "SELECT %s AS id FROM %s%s WHERE %s";
	$rows = queryDB($sql, array($field, TABLE_PREFIX, $table, $where_clause));
	
	foreach($rows as $row){
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
	        (stripos($http_user_agent, ANDROID_DEVICE) !== false && stripos($http_user_agent, ANDROID_DEVICE) >= 0) ||
	        (stripos($http_user_agent, PLAYBOOK) !== false && stripos($http_user_agent, PLAYBOOK) >= 0))  
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
	}else if(stripos($http_user_agent, PLAYBOOK) !== false && stripos($http_user_agent, PLAYBOOK) >= 0) {
		return PLAYBOOK;
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
	$_SESSION['log'] == 'log';
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
/**
 * Set session variable with tool origin page to create back to page
 * @param	string	url/off
 * @author	Greg Gay
 * @date	Sept 28, 2013
 */
function tool_origin($path=''){
    if($path == 'off'){
       if(isset($_SESSION['tool_origin'])){
            unset($_SESSION['tool_origin']);
        }
    } else if(!isset($_SESSION['tool_origin']['url']) && $path == ''){
        $_SESSION['tool_origin']['url'] = $_SERVER['HTTP_REFERER'];
        $_SESSION['tool_origin']['title'] = $_SESSION['origin_title'];        
    }else if(!isset($_SESSION['tool_origin']['url'])){
        $_SESSION['tool_origin']['url'] = $path;
        $_SESSION['tool_origin']['title'] = $_SESSION['origin_title'];
    }
}

/**
 * XSS safe echo replacement
 * @param	string	$text	The text that should be displayed
 * @param string	$encoding	The encoding of the text
 * @date	Jan 11, 2016
 */
function xecho($text, $encoding='UTF-8') {
	echo htmlspecialchars($text, ENT_QUOTES, $encoding);
}

?>
