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

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');

authenticate(AT_PRIV_FILES);

$current_path = AT_CONTENT_DIR.$_SESSION['course_id'].'/';

if (($_REQUEST['popup'] == TRUE) || ($_REQUEST['framed'] == TRUE)) {
	$_header_file = AT_INCLUDE_PATH.'fm_header.php';
	$_footer_file = AT_INCLUDE_PATH.'fm_footer.php';
} else {
	$_header_file = AT_INCLUDE_PATH.'header.inc.php';
	$_footer_file = AT_INCLUDE_PATH.'footer.inc.php';
}
$popup = $_REQUEST['popup'];
$framed = $_REQUEST['framed'];

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup']);
	exit;
}

if (isset($_POST['rename_action'])) {

	$_POST['new_name'] = trim($_POST['new_name']);
	$_POST['new_name'] = str_replace(' ', '_', $_POST['new_name']);
	$_POST['new_name'] = str_replace(array(' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|', '\''), '', $_POST['new_name']);

	$_POST['oldname'] = trim($_POST['oldname']);
	$_POST['oldname'] = str_replace(' ', '_', $_POST['oldname']);
	$_POST['oldname'] = str_replace(array(' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|', '\''), '', $_POST['oldname']);

	$path_parts_new = pathinfo($_POST['new_name']);
	$ext_new = $path_parts_new['extension'];
	$pathext = $_POST['pathext'];

	/* check if this file extension is allowed: */
	/* $IllegalExtentions is defined in ./include/config.inc.php */
	if (in_array($ext_new, $IllegalExtentions)) {
		$errors = array('FILE_ILLEGAL', $ext_new);
		$msg->addError($errors);
	}
	else if ($current_path.$pathext.$_POST['new_name'] == $current_path.$pathext.$_POST['oldname']) {
		//do nothing
		$msg->addFeedback('RENAMED');
		header('Location: index.php?pathext='.urlencode($_POST['pathext']).SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup']);
		exit;
	}

	//make sure new file is inside content directory
	else if (course_realpath($current_path . $pathext . $_POST['new_name']) == FALSE) {
		$msg->addError('CANNOT_RENAME');
	}	
	else if (course_realpath($current_path . $pathext . $_POST['oldname']) == FALSE) {
		$msg->addError('CANNOT_RENAME');
	}
	else {
		@rename($current_path.$pathext.$_POST['oldname'], $current_path.$pathext.$_POST['new_name']);
		$msg->addFeedback('RENAMED');
		header('Location: index.php?pathext='.urlencode($_POST['pathext']).SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup']);
		exit;
	}
}

require($_header_file);
if ($framed == TRUE) {
	echo '<h3>'._AT('file_manager').'</h3>';
}
else {
	if ($popup == TRUE) {
		echo '<div align="right"><a href="javascript:window.close()">' . _AT('close_file_manager') . '</a></div>';
	}
	
	echo '<h2>';
	
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<img src="images/icons/default/square-large-tools.gif" border="0" vspace="2"  class="menuimageh2" width="42" height="40" alt="" />';
	}

	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		if ($popup == TRUE)
			echo ' '._AT('tools')."\n";
		else 
			echo ' <a href="tools/" class="hide" >'._AT('tools').'</a>'."\n";
	}

	echo '</h2>'."\n";

	echo '<h3>';
	
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {	
		echo '&nbsp;<img src="images/icons/default/file-manager-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo '<a href="tools/filemanager/index.php?popup=' . $popup . SEP . 'framed=' . $framed .'">' . _AT('file_manager') . '</a>' . "\n";
	}
	echo '</h3>'."\n";
}
echo '<h3>'._AT('rename_file_dir').'</h3>';

$msg->printall();

echo '<p></p><p></p><form name="rename" action="'.$_SERVER['PHP_SELF'].'" method="post"><p>'."\n";
echo '<input type="hidden" name="pathext" value="'.$_REQUEST['pathext'].'" />';
echo '<input type="hidden" name="oldname" value="'.$_REQUEST['oldname'].'" />';

echo '<input type="hidden" name="framed" value="'.$_REQUEST['framed'].'" />';
echo '<input type="hidden" name="popup" value="'.$_REQUEST['popup'].'" />';


echo '<strong>'.$_GET['pathext'].'</strong><input type="text" name="new_name" value="'.$_REQUEST['oldname'].'" class="formfield" size="30" /> ';
echo '<input type="submit" name="rename_action" value="'._AT('rename').'" class="button" />';
echo ' - <input type="submit" name="cancel" value="'._AT('cancel').'" class="button" />';
echo '</p></form>';

require($_footer_file);

?>