<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

$page = 'file_manager_rename';

define('AT_INCLUDE_PATH', '../../include/');
$_ignore_page = true; /* used for the close the page option */
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');
if (!$_GET['f']) {
	$_SESSION['done'] = 0;
}
session_write_close();
$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('file_manager');
$_section[1][1] = 'tools/filemanager/index.php';
$_section[2][0] = _AT('file_manager_rename_file');
$_section[2][1] = 'tools/filemanager/file_manager_rename.php';

authenticate(AT_PRIV_FILES);

$_header_file = 'header.inc.php';
$_footer_file = 'footer.inc.php';

$current_path = AT_CONTENT_DIR . $_SESSION['course_id'].'/';

if (isset($_POST['cancel'])) {
	header('Location: index.php');
	exit;
}
$start_at = 3;

if ($_POST['pathext'] != '') {
	$pathext =$_POST['pathext'];
}

if ($pathext != '') {
	$bits = explode('/', $pathext);
	$bits_path = $bits[0];

	for ($i=0; $i<count($bits)-2; $i++) {
		if ($bits_path != $bits[0]) {
			$bits_path .= '/'.$bits[$i];
		}
		$_section[$start_at][0] = $bits[$i];
		$_section[$start_at][1] = 'tools/filemanager/index.php?back=1'.SEP.'pathext='.$bits_path.'/'.$bits[$i+1].'/';

		$start_at++;
	}
	$_section[$start_at][0] = $bits[count($bits)-2];
}

require(AT_INCLUDE_PATH.$_header_file);

echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/square-large-tools.gif" border="0" vspace="2"  class="menuimageh2" width="42" height="40" alt="" />';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo ' <a href="tools/" class="hide" >'._AT('tools').'</a>';
}
echo '</h2>'."\n";

echo '<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '&nbsp;<img src="images/icons/default/file-manager-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo ' <a href="tools/filemanager/" class="hide" >'._AT('file_manager').'</a>';
}
echo '</h3>'."\n";

echo '<h4>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '&nbsp;&nbsp;<img src="images/icons/default/file-manager-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo _AT('file_manager_rename_file');
}
echo '</h4>'."\n";

/* listing of path to current directory */
echo '<p>'._AT('current_path').' ';
echo '<small>';
echo '<a href="tools/filemanager/index.php">'._AT('home').'</a> / ';
if ($pathext != '') {
	$bits = explode('/', $pathext);
	$bits_path = $bits[0];
	for ($i=0; $i<count($bits)-2; $i++) {
		if ($bits_path != $bits[0]) {
			$bits_path .= '/'.$bits[$i];
		}
		echo '<a href="tools/filemanager/index.php?back=1'. SEP .'pathext='. $bits_path .'/'. $bits[$i+1] .'/">'.$bits[$i].'</a>'."\n";
		echo ' / ';
	}
	echo $bits[count($bits)-2];
}
echo '</small>'."\n";


/* check that at least one checkbox checked */		
if (isset($_POST['renamefile'])) {
	if (!is_array($_POST['checkbox'])) {
		// error: you must select a file/dir to rename
		$errors[] = AT_ERROR_NO_FILE_SELECT;
	} else if (isset($_POST['renamefile'])) {
		$count = count($_POST['checkbox']);
		if ($count > 1) {
			// error: you must select one file/dir to rename
			$errors[] = AT_ERROR_TOO_MANY_FILE;
		} else {
			$newname = $_POST['checkbox'][0];

			echo '<h3>'._AT('rename_file_dir').'</h3>';
			echo '<form name="rename" action="'.$_SERVER['PHP_SELF'].'?frame='.$_GET['frame'].'" method="post"><p>'."\n";
			echo '<input type="hidden" name="frame" value="'.$_GET['frame'].'" />';
			echo '<input type="hidden" name="pathext" value="'.$_POST['pathext'].'" />';
			echo '<input type="hidden" name="old_name" value="'.$newname.'" />';

			echo $_POST['pathext'] . '<input type="text" name="new_name" value="'.$newname.'" class="formfield" size="30" /> ';
			echo '<input type="submit" name="rename_action" value="'._AT('rename').'" class="button" />';
			echo ' - <input type="submit" name="cancel" value="'._AT('cancel').'" class="button" />';
			echo '</p></form>';
			echo '<hr />';
			require(AT_INCLUDE_PATH.$_footer_file);
			exit;
		}
	}
} else if (isset($_POST['rename_action'])) {
	$_POST['new_name'] = trim($_POST['new_name']);
	$_POST['new_name'] = str_replace(' ', '_', $_POST['new_name']);
	$_POST['new_name'] = str_replace(array(' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|', '\''), '', $_POST['new_name']);

	$_POST['old_name'] = trim($_POST['old_name']);
	$_POST['old_name'] = str_replace(' ', '_', $_POST['old_name']);
	$_POST['old_name'] = str_replace(array(' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|', '\''), '', $_POST['old_name']);

	if (file_exists($current_path.$pathext.$_POST['new_name']) || !file_exists($current_path.$pathext.$_POST['old_name'])) {
		$errors[] = AT_ERROR_CANNOT_RENAME;
		print_errors($errors);
		unset($errors);
	} else {
		@rename($current_path.$pathext.$_POST['old_name'], $current_path.$pathext.$_POST['new_name']);
		print_feedback(AT_FEEDBACK_RENAMED);
	}
} 


require(AT_INCLUDE_PATH.'html/feedback.inc.php');
echo '<form name="form1" action="'.$_SERVER['PHP_SELF'].'" method="post">'."\n";
echo '<input type="submit" name="cancel" value="Return to File Manager" /></form>';
require(AT_INCLUDE_PATH.$_footer_file);
?>