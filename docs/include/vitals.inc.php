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
		require(AT_INCLUDE_PATH.'config.inc.php');
	error_reporting(E_ALL ^ E_NOTICE);
	if (!defined('AT_INSTALL') || !AT_INSTALL) {
		$relative_path = substr(AT_INCLUDE_PATH, 0, -strlen('include/'));
		echo 'ATutor does not appear to be installed. <a href="'.$relative_path.'install/">Continue on to the installation</a>.';
		exit;
	}

/* constants: */
require(AT_INCLUDE_PATH.'lib/constants.inc.php');

/* session variables: */
require(AT_INCLUDE_PATH.'session.inc.php');

/* _feedback, _help, _errors constants definitions */
require(AT_INCLUDE_PATH.'lib/lang_constants.inc.php');

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
	if (file_exists(AT_INCLUDE_PATH.'cvs_development.inc.php') && false) {
		require(AT_INCLUDE_PATH.'cvs_development.inc.php');
	} else {
		define('TABLE_PREFIX_LANG', TABLE_PREFIX);
		$lang_db =& $db;
	}


/* cache library: */
if (defined('CACHE_DIR') && (CACHE_DIR != '')) {
	define('CACHE_ON', 1); /* disable cacheing */
} else {
	define('CACHE_ON', 0); /* enable cacheing */
}
require(AT_INCLUDE_PATH.'phpCache/phpCache.inc.php');

/* template language variables */
require(AT_INCLUDE_PATH.'lib/select_lang.inc.php');

/* check if this language is supported: */

if (!isset($available_languages[$temp_lang])) {
	$errors[] = AT_ERROR_NO_LANGUAGE;
} else if (($temp_lang != '') && ($available_languages[$temp_lang] != '') && ($_SESSION['lang'] != $temp_lang)) {
	$_SESSION['lang'] = $temp_lang;
}
header('Content-Type: text/html; charset='.$available_languages[$_SESSION['lang']][1]);


   if (isset($_REQUEST['jump']) && $_REQUEST['jump'] && $_POST['form_course_id']) {
		if ($_POST['form_course_id'] == 0) {
			header('Location: users/');
			exit;
		}

		header('Location: bounce.php?course='.$_POST['form_course_id']);
		exit;
   }

/* set right-to-left language */
	$rtl = '';
	if (in_array($_SESSION['lang'], $_rtl_languages)) {
		$rtl = 'rtl_'; /* basically the prefix to a rtl variant directory/filename. rtl_tree */
	}

/* date functions */
require(AT_INCLUDE_PATH.'lib/date_functions.inc.php');

/* content formatting library: */
require(AT_INCLUDE_PATH.'lib/content_functions.inc.php');

/* preference switches for ATutor HowTo: */
require(AT_INCLUDE_PATH.'lib_howto/howto_switches.inc.php');

/* content management class: */
require(AT_INCLUDE_PATH.'classes/ContentManager.class.php');

$contentManager = new ContentManager($db, $_SESSION['course_id']);
$contentManager->initContent( );
/**************************************************/

if (($_SESSION['course_id'] == 0) && ($section != 'users') && ($section != 'prog') && !$_GET['h'] && !$_public) {
	Header('Location: users/');
	exit;
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
		$glossary[$row_g['word']] = str_replace("'", "\'",$row_g['definition']);
		$glossary_ids[$row_g['word_id']] = $row_g['word'];
	}
}


function debug($value) {
	if (!AT_DEVEL) {
		return;
	}
	
	echo '<pre style="border: 1px black solid; padding: 0px; margin: 10px;" title="debugging box">';
	
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


function getMessage($codes) {
	/* this is where we want to get the msgs from the database inside a static variable */
	global $_cache_msgs;
	static $_msgs;

	if (!isset($_msgs)) {
		if ( !($lang_et = cache(120, 'msgs', $_SESSION['lang'])) ) {
			global $lang_db, $_base_path;
			/* get $_msgs from the DB */
			if ($_SESSION['lang'] == 'en') {
				$sql	= 'SELECT * FROM '.TABLE_PREFIX_LANG.'lang_base WHERE variable="_msgs"';
			} else {
				$sql	= 'SELECT * FROM '.TABLE_PREFIX_LANG.'lang2 WHERE variable="_msgs" AND lang="'.$_SESSION['lang'].'"';
			}
			$result	= @mysql_query($sql, $lang_db);
			$i = 1;
			while ($row = @mysql_fetch_assoc($result)) {
				$_cache_msgs[constant($row['key'])] = str_replace('SITE_URL/', $_base_path, $row['text']);
				if (AT_DEVEL) {
					$_cache_msgs[constant($row['key'])] .= '<small><small>('.$row['key'].')</small></small>';
				}

				/*
				if (strpos($_cache_msgs[constant($row['key'])], '%')) {
					debug($row['key']);
				}
				*/
			}

			cache_variable('_cache_msgs');
			endcache(true, false);
		}
		$_msgs = $_cache_msgs;
	}

	if (is_array($codes)) {
		/* this is an array with terms to replace */
		$code		= array_shift($codes);
		$message	= $_msgs[$code];
		$terms		= $codes;

		/* replace the tokens with the terms */
		$message	= vsprintf($message, $terms);

	} else {
		$message = $_msgs[$codes];

		if ($message == '') {
			/* the language for this msg is missing: */
		
			$sql	= 'SELECT * FROM '.TABLE_PREFIX_LANG.'lang_base WHERE variable="_msgs"';
			$result	= @mysql_query($sql, $lang_db);
			$i = 1;
			while ($row = @mysql_fetch_assoc($result)) {
				if (constant($row['key']) == $codes) {
					$message = '['.$row['key'].']';
					break;
				}
			}
		}
		$code = $codes;
	}
	return $message;
}


function print_errors( $errors ) {
	if (empty($errors)) {
		return;
	}
	global $_base_path;

	?>	<br />
	<table border="0" class="errbox" cellpadding="3" cellspacing="2" width="90%" summary="" align="center">
	<tr class="errbox">
	<td>
		<h3><img src="<?php echo $_base_path; ?>images/error_x.gif" align="top" class="menuimage5" height="25" width="28" alt="<?php echo _AT('error'); ?>" /><?php echo _AT('error'); ?></h3><hr />
		<?php
			print_items($errors);

		?>
		</td>
	</tr>
	</table>
	<br />
<?php
}

function print_feedback( $feedback ) {
	if (empty($feedback)) {
		return;
	}

	global $_base_path;

	?>	<br />
	<table border="0" class="fbkbox" cellpadding="3" cellspacing="2" width="90%" summary="" align="center">
	<tr class="fbkbox">
	<td>
		<h3><img src="<?php echo $_base_path; ?>images/feedback_x.gif" align="top" alt="<?php echo _AT('feedback'); ?>" class="menuimage5" /><?php echo _AT('feedback'); ?></h3><hr />
		<?php

			print_items($feedback);

		?>
		</td>
	</tr>
	</table>
	<br />
<?php
}

function print_help( $help ) {
	if (empty($help)) {
		return;
	}
	global $_my_uri, $_base_path;
	echo '<a name="help"></a>';
	if (!isset($_GET['e']) && !$_SESSION['prefs']['PREF_HELP'] && !$_GET['h']) {
		if($_SESSION['prefs']['PREF_CONTENT_ICONS'] == 2){
			echo '<small>( <a href="'.$_my_uri.'e=1#help">'._AT('help').'</a> )</small><br /><br />';

		}else{
			echo '<a href="'.$_my_uri.'e=1#help"><img src="'.$_base_path.'images/help_open.gif" class="menuimage"  alt="'._AT('help').'" border="0" /></a><br />';
		}
		return;
	}
	?>	<br />
	<table border="0" class="hlpbox" cellpadding="3" cellspacing="2" width="90%" summary="" align="center">
	<tr class="hlpbox">
	<td>
		<h3><?php
			if (isset($_GET['e'])) {
				echo '<a href="'.$_my_uri.'#help">';
				echo '<img src="'.$_base_path.'images/help_close.gif" class="menuimage5" align="top" alt="'._AT('close_help').'" border="0" title="'._AT('close_help').'"/></a> ';
			} else {
				echo '<img src="'.$_base_path.'images/help.gif" class="menuimage5" align="top" alt="'._AT('help').'" border="0" /> ';
			}
		echo _AT('help').'</h3><hr />';

			print_items($help);
		if($_SESSION['course_id']){
		?>
		<div align="right"><br /><small><a href="<?php echo $_base_path; ?>help/about_help.php?h=1"><?php echo _AT('about_help'); ?></a>.</small></div>
		<?php } ?>
		</td>
	</tr>
	</table>
	<br />
<?php
}

function print_warnings( $warnings ) {
	if (empty($warnings)) {
		return;
	}

	global $_base_path;

	?>	<br />
	<table border="0" class="wrnbox" cellpadding="3" cellspacing="2" width="90%" summary="" align="center">
	<tr class="wrnbox">
	<td>
		<h3><img src="<?php echo $_base_path; ?>images/warning_x.gif" align="top" class="menuimage5" alt="<?php echo _AT('warning'); ?>" /><?php echo _AT('warning'); ?></h3><hr />
		<?php

			print_items($warnings);


		?>
		</td>
	</tr>
	</table>
	<br />
<?php
}

function print_infos( $infos ) {
	if (empty($infos)) {
		return;
	}

	global $_base_path;
	
	?>
	<table border="0" cellpadding="3" cellspacing="2" width="90%" summary="" align="center"  class="hlpbox">
	<tr class="hlpbox">
	<td><h3><img src="<?php echo $_base_path;?>images/infos.gif" align="top" class="menuimage5" alt="<?php echo _AT('info'); ?>" /><?php echo _AT('info'); ?></h3><hr /><?php

	print_items($infos);
	?>
	</td>
	</tr></table>

<?php
}

function print_items( $items ) {
	if (!$items) {
		return;
	}

	if (is_object($items)) {
		/* this is a PEAR::ERROR object.	*/
		/* for backwards compatability.		*/
		echo $items->getMessage();
		echo '.<p>';
		echo '<small>';
		echo $items->getUserInfo();
		echo '</small></p>';

	} else if (is_array($items)) {
		/* this is an array of errors */
		echo '<ul>';
		foreach($items as $e => $info){
			echo '<li>'.getMessage($info).'</li>';
		}
		echo'</ul>';
	} else if (is_int($items)){
		/* this is a single error not an array of errors */
		echo '<ul>';
		echo '<li>'.getMessage($items).'</li>';
		echo '</ul>';
	
	} else {
		/* not really sure what this is.. some kind of string.	*/
		/* for backwards compatability							*/
		echo '<ul>';
		echo '<li>'.$items.'</li>';
		echo'</ul>';
	}
}

function print_popup_help($help, $align="left") {
	if (!$_SESSION['prefs'][PREF_MINI_HELP]) {
		return;
	}
	//if (!is_array($help)) {
		$text = getMessage($help);
		$text = str_replace('"','&quot;',$text);
		$text = str_replace("'",'&#8217;',$text);
		$text = str_replace('`','&#8217;',$text);
		$text = str_replace('<','&lt;',$text);
		$text = str_replace('>','&gt;',$text);

		global $_base_path;

		$help_link = urlencode(serialize(array($help)));
		
		//echo '<a href="popuphelp.php?h='.$help.'" target="_'.$help.'" onmouseover="return overlib(\'&lt;small&gt;'.$text.'&lt;/small&gt;\', CAPTION, \''._AT('help').'\', CSSCLASS, FGCLASS, \'row1\', BGCLASS, \'cat2\', TEXTFONTCLASS, \'row1\', CENTER);" onmouseout="return nd();"><img src="images/help3.gif" border="0" style="height:.95em; width:1em" align="'.$align.'" alt="'._AT('open_help').'" /></a>';

		if($_SESSION['prefs'][PREF_CONTENT_ICONS] == 2){
			echo '<span><a href="'.$_base_path.'popuphelp.php?h='.$help_link.'" target="help" onmouseover="return overlib(\'&lt;small&gt;'.$text.'&lt;/small&gt;\', CAPTION, \''._AT('help').'\', CSSCLASS, FGCLASS, \'row1\', BGCLASS, \'cat2\', TEXTFONTCLASS, \'row1\', CENTER);" onmouseout="return nd();"><small>('._AT('help').')</small> </a></span>';
		}else{
			echo '<a href="'.$_base_path.'popuphelp.php?h='.$help_link.'" target="help" onmouseover="return overlib(\'&lt;small&gt;'.$text.'&lt;/small&gt;\', CAPTION, \''._AT('help').'\', CSSCLASS, FGCLASS, \'row1\', BGCLASS, \'cat2\', TEXTFONTCLASS, \'row1\', CENTER);" onmouseout="return nd();"><img src="'.$_base_path.'images/help3.gif" border="0" class="menuimage10" align="'.$align.'" alt="'._AT('open_help').'" /></a>';

		}
	//}
}

function print_editor( $editor_links ) {
	$num_args = func_num_args();
	$args	  = func_get_args();

	if (!$num_args || !($_SESSION['is_admin'] && $_SESSION['prefs'][PREF_EDIT])) {
		return;
	}
	global $_base_path;

	echo ' <span class="editorsmallbox"><small>';
	if($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2){
		echo '<img src="'.$_base_path.'images/pen2.gif" border="0" class="menuimage12" alt="'._AT('editor_on').'" title="'._AT('editor_on').'" height="14" width="16" /> ';
	}
	for ($i=0; $i<$num_args; $i+=2) {
		echo '<a href="'.$args[$i+1].'">'.$args[$i].'</a>';
		if ($i+2 < $num_args){
			echo ' | ';
		}
	}
	echo '</small></span> '."\n";

	return;
}
function print_editorlg( $editor_links ) {
	$num_args = func_num_args();
	$args	  = func_get_args();

	if (!$num_args || !($_SESSION['is_admin'] && $_SESSION['prefs'][PREF_EDIT])) {
		return;
	}
	global $_base_path;

	echo '<p><span class="editorlargebox">';
	if($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2){
		echo '<img src="'.$_base_path.'images/pen3.gif" border="0" class="menuimage11" alt="'._AT('editor_on').'" title="'._AT('editor_on').'" height="28" width="32" /> ';
	}
	echo '<small>';
	for ($i=0; $i<$num_args; $i+=2) {
		echo '<a href="'.$args[$i+1].'">'.$args[$i].'</a>';
		if ($i+2 < $num_args){
			echo ' | ';
		}
	}
	echo '</small>';
	echo '</span></p>';

	return;
}
function &get_html_body(&$text) {
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
}

	/* _AC is from ACollab */
	function & _AC( ) {
		$args 	  = func_get_args();

		return _AT($args);
	}

	/*
		$args[0] = the key to the format string $_template[key]
		$args[1..x] = optional arguments to the formatting string 
	*/
	function & _AT( ) {
		global $_cache_template, $lang_et;
		static $_template;

		if (!isset($_template)) {
			global $_base_href;
			$url_parts = parse_url($_base_href);
			$name = substr($_SERVER['PHP_SELF'], strlen($url_parts['path'])-1);


			if ( !($lang_et = cache(120, 'lang', $_SESSION['lang'].'_'.$name)) ) {
				global $lang_db;

				/* get $_template from the DB */
				if ($_SESSION['lang'] == 'en') {
					$sql	= 'SELECT L.* FROM '.TABLE_PREFIX_LANG.'lang_base L, '.TABLE_PREFIX_LANG.'lang_base_pages P WHERE L.variable="_template" AND L.key=P.key AND P.page="'.$_SERVER['PHP_SELF'].'"';
				} else {
					$sql	= 'SELECT L.* FROM '.TABLE_PREFIX_LANG.'lang2 L, '.TABLE_PREFIX_LANG.'lang_base_pages P WHERE L.lang="'.$_SESSION['lang'].'" AND L.variable="_template" AND L.key=P.key AND P.page="'.$_SERVER['PHP_SELF'].'"';
				}
				$result	= mysql_query($sql, $lang_db);
				while ($row = @mysql_fetch_assoc($result)) {
					$_cache_template[$row['key']] = stripslashes($row['text']);
				}
		
				cache_variable('_cache_template');
				endcache(true, false);
			}
			$_template = $_cache_template;
		}

		$num_args = func_num_args();
		$args 	  = func_get_args();

		/* fix for the _AC() wrapper: */
		if (is_array($args[0])) {
			$args = $args[0];
			$num_args = count($args);
		}

		$format		= array_shift($args);

		$c_error	= error_reporting(0);
		$outString	= vsprintf($_template[$format], $args);
		if ($outString === false) {
			return ('[Error parsing language.'."\n".'Variable: '.$format.'. Value: '.$_template[$format].'. Language: '.$_SESSION['lang'].']');
		}
		error_reporting($c_error);

		if (empty($outString) && ($_SESSION['lang'] == 'en')) {
			global $lang_db;
			$sql	= 'SELECT L.* FROM '.TABLE_PREFIX_LANG.'lang_base L WHERE L.variable="_template" AND `key`="'.$format.'"';
			$result	= @mysql_query($sql, $lang_db);
			$row = @mysql_fetch_array($result);

			$_template[$row['key']] = stripslashes($row['text']);
			$outString = $_template[$row['key']];
			if (empty($outString)) {
				return ('['."\n".'Variable: '.$format.']');
			}
			$outString = $_template[$row['key']];
			$outString = vsprintf($outString, $args);

			/* purge the language cache */
			/* update the locations */
			$sql = 'INSERT INTO '.TABLE_PREFIX_LANG.'lang_base_pages VALUES ("template", "'.$format.'", "'.$_SERVER['PHP_SELF'].'")';
			@mysql_query($sql, $lang_db);

		} else if (empty($outString)) {
			global $lang_db;

			$sql	= 'SELECT L.* FROM '.TABLE_PREFIX_LANG.'lang2 L WHERE L.variable="_template" AND `key`="'.$format.'" AND lang="'.$_SESSION['lang'].'"';
			$result	= @mysql_query($sql, $lang_db);
			$row = @mysql_fetch_array($result);

			$_template[$row['key']] = stripslashes($row['text']);
			$outString = $_template[$row['key']];
			if (empty($outString)) {
				return ('['."\n".'Variable: '.$format.']');
			}
			$outString = vsprintf($outString, $args);

			/* purge the language cache */
			/* update the locations */
			$sql = 'INSERT INTO '.TABLE_PREFIX_LANG.'lang_base_pages VALUES ("template", "'.$format.'", "'.$_SERVER['PHP_SELF'].'")';
			@mysql_query($sql, $lang_db);
		}

		return $outString;
	}

function add_user_online() {
	if ($_SESSION['member_id'] == 0) {
		return;
	}
	global $db;

    $expiry = time() + 900; // 15min
    $sql    = 'REPLACE INTO '.TABLE_PREFIX.'users_online VALUES ('.$_SESSION['member_id'].', '.$_SESSION['course_id'].', "'.$_SESSION['login'].'", '.$expiry.')';
    $result = mysql_query($sql, $db);

	/* should garbage collect and optimize the table every so often */
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


?>
