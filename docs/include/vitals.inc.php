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
// $Id: vitals.inc.php,v 1.30 2004/02/17 18:38:35 joel Exp $

if (!defined('AT_INCLUDE_PATH')) { exit; }

/********************************************/
/* timing stuff								*/
$microtime = microtime();
$microsecs = substr($microtime, 2, 8);
$secs = substr($microtime, 11);
$startTime = "$secs.$microsecs";
/********************************************/

define('AT_DEVEL', 1);

/* system configuration options: */

	error_reporting(0);
		include(AT_INCLUDE_PATH.'config.inc.php');
	error_reporting(E_ALL ^ E_NOTICE);
	if (!defined('AT_INSTALL') || !AT_INSTALL) {
		$relative_path = substr(AT_INCLUDE_PATH, 0, -strlen('include/'));
		echo 'ATutor does not appear to be installed. <a href="'.$relative_path.'install/">Continue on to the installation</a>.';
		exit;
	}

require(AT_INCLUDE_PATH.'lib/constants.inc.php');      /* constants & db connection */
require(AT_INCLUDE_PATH.'session.inc.php');            /* session variables: */
require(AT_INCLUDE_PATH.'lib/lang_constants.inc.php'); /* _feedback, _help, _errors constants definitions */

	/* bounce into a course */
   if (isset($_REQUEST['jump'], $_REQUEST['jump'], $_POST['form_course_id'])) {
		if ($_POST['form_course_id'] == 0) {
			header('Location:'.$_base_href.'users/');
			exit;
		}

		header('Location: bounce.php?course='.$_POST['form_course_id']);
		exit;
   }
/* database connection */
if (AT_INCLUDE_PATH !== 'NULL') {
	$db = @mysql_connect(DB_HOST . ':' . DB_PORT, DB_USER, DB_PASSWORD);
	if (!$db) {
		/* AT_ERROR_NO_DB_CONNECT */
		echo 'Unable to connect to db.';
		exit;
	}
	if (!mysql_select_db(DB_NAME, $db)) {
		echo 'DB connection established, but database "'.DB_NAME.'" cannot be selected.';
		exit;
	}

	/* development uses a common language db */
	if (file_exists(AT_INCLUDE_PATH.'cvs_development.inc.php')) {
		require(AT_INCLUDE_PATH.'cvs_development.inc.php');
	} else {
		define('TABLE_PREFIX_LANG', TABLE_PREFIX);
		$lang_db =& $db;
	}
}

require(AT_INCLUDE_PATH.'phpCache/phpCache.inc.php');         /* cache library */
require(AT_INCLUDE_PATH.'lib/select_lang.inc.php');           /* set current language */
require(AT_INCLUDE_PATH.'lib_howto/howto_switches.inc.php');  /* preference switches for ATutor HowTo */
require(AT_INCLUDE_PATH.'classes/ContentManager.class.php');  /* content management class */
require(AT_INCLUDE_PATH.'lib/output.inc.php');                /* output functions */


$contentManager = new ContentManager($db, $_SESSION['course_id']);
$contentManager->initContent( );
/**************************************************/

if (($_SESSION['course_id'] == 0) && ($section != 'users') && ($section != 'prog') && !$_GET['h'] && !$_public) {
	header('Location:'.$_base_href.'users/');
	exit;
}

function debug($value, $title='') {
	if (!AT_DEVEL) {
		return;
	}
	
	echo '<pre style="border: 1px black solid; padding: 0px; margin: 10px;" title="debugging box">';
	if ($title) {
		echo '<h4>'.$title.'</h4>';
	}
	
	ob_start();
	print_r($value);
	$str = ob_get_contents();
	ob_clean();

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
		$system_courses[$row['course_id']] = array(	'title' => $row['title'], 
													'description' => $row['description'], 
													'subject' => $row['subject']);
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

function get_login($id){
	global $db;

	$id		= intval($id);

	$sql	= 'SELECT login FROM '.TABLE_PREFIX.'members WHERE member_id='.$id;
	$result	= mysql_query($sql, $db);
	$row	= mysql_fetch_assoc($result);

	return $row['login'];
}

function get_forum($fid){
	global $db;

	$fid = intval($fid);

	$sql	= 'SELECT title FROM '.TABLE_PREFIX.'forums WHERE forum_id='.$fid.' AND course_id='.$_SESSION['course_id'];
	$result	= mysql_query($sql, $db);
	$row	= mysql_fetch_assoc($result);

	return $row['title'];
}

	/* defaults: */
	if(!$_SESSION['valid_user'] && !$_SESSION['prefs']){
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

function save_prefs($override = false) {
	global $db;

	if ($_SESSION['valid_user'] && $_SESSION['enroll'] && $_SESSION['course_id'] && !$override) {
		// save for this course only
		$data	= addslashes(serialize($_SESSION['prefs']));

		$sql	= 'REPLACE INTO '.TABLE_PREFIX.'preferences VALUES ('.$_SESSION['member_id'].', '.$_SESSION['course_id'].', "'.$data.'")';
		$result = mysql_query($sql, $db);

	} else if ($_SESSION['valid_user']) {
		$data	= addslashes(serialize($_SESSION['prefs']));
		$sql	= 'UPDATE '.TABLE_PREFIX.'members SET preferences="'.$data.'" WHERE member_id='.$_SESSION['member_id'];
		$result = mysql_query($sql, $db); 

		/* these prefs will become global, but must also override this course's prefs	*/
		/* to override this course's prefs, just delete it to take the global.			*/
		$sql	= 'DELETE FROM '.TABLE_PREFIX.'preferences WHERE member_id='.$_SESSION['member_id'];
		$result	= mysql_query($sql, $db);
	}
 
	/* else, we're not a valid user so nothing to save. */
}

function urlencode_feedback($f) {
	if (is_array($f)) {
		return urlencode(serialize($f));
	}
	return $f;
}

function save_last_cid($cid) {
	if (!$_SESSION['enroll']) {
		return;
	}
	global $db;

	$sql = "UPDATE ".TABLE_PREFIX."course_enrollment SET last_cid=$cid WHERE course_id=$_SESSION[course_id]";
	mysql_query($sql, $db);
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
		&& !$_SESSION['is_admin'] 
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

if (!isset($_ignore_page) || !$_ignore_page) {
	$_SESSION['my_referer'] = $_SERVER['REQUEST_URI'];
}

function sql_quote($input) {
	if (is_array($input)) {
		foreach ($input as $key => $value) {
			if (is_array($input[$key])) {
				$input[$key] = sql_quote($input[$key]);
			} else if (!empty($input[$key]) && is_numeric($input[$key])) {
				$input[$key] = intval($input[$key]);
			} else {
				$input[$key] = str_replace(array('\r', '\n'), array("\r", "\n"), mysql_real_escape_string(trim($input[$key])));
			}
		}
	} else {
		if (!empty($input) && is_numeric($input)) {
			$input = intval($input);
		} else {
			$input = str_replace(array('\r', '\n'), array("\r", "\n"), mysql_real_escape_string(trim($input)));
		}
	}
	return $input;
}


if (!get_magic_quotes_gpc()) {
	if (isset($_POST))    { $_POST    = sql_quote($_POST);    }
	if (isset($_GET))     { $_GET     = sql_quote($_GET);     }
	if (isset($_COOKIE))  { $_COOKIE  = sql_quote($_COOKIE);  }
	if (isset($_REQUEST)) { $_REQUEST = sql_quote($_REQUEST); }
}


	/* Return true or false, depending on if the bit is set */ 
	function query_bit( $bitfield, $bit ) {
		return ( $bitfield & $bit ) ? true : false;
	} 

?>