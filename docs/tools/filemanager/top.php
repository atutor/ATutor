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

require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');

if (!$_GET['f']) {
	$_SESSION['done'] = 0;
}
authenticate(AT_PRIV_FILES);

$current_path = AT_CONTENT_DIR.$_SESSION['course_id'].'\\';

if (isset($_POST['rename'])) {
	if (!is_array($_POST['check'])) {
		// error: you must select a file/dir to rename
		$msg->addError('NO_FILE_SELECT');
	} else if (count($_POST['check']) != 1) {
		// error: you must select one file/dir to rename
		$msg->addError('SELECT_ONE_FILE');
	} else {
		header('Location: rename.php?pathext='.urlencode($_POST['pathext']).SEP.'framed='.$framed.SEP.'popup='.$popup.SEP.'oldname='.urlencode($_POST['check'][0]));
		exit;
	}
}

else if (isset($_POST['edit'])) {
	if (!isset($_POST['check'][0])) {
		// error: you must select a file/dir 
		$msg->addError('NO_FILE_SELECT');
	} else if (count($_POST['check']) != 1) {
		// error: you must select one file/dir to rename
		$msg->addError('SELECT_ONE_FILE');
	} else {
		$file = $_POST['check'][0];
		header('Location: edit.php?pathext='.urlencode($_POST['pathext']).SEP.'framed='.$framed.SEP.'popup='.$popup.SEP.'file=' . $file);
		exit;
	}
}

else if (isset($_POST['delete'])) {
	
	if (!is_array($_POST['check'])) {
		$msg->addError('NO_FILE_SELECT');
	} else {

		$list = implode(',', $_POST['check']);
		header('Location: delete.php?pathext=' . urlencode($_POST['pathext']) . SEP . 'framed=' . $framed . SEP . 'popup=' . $popup . SEP . 'list=' . urlencode($list));
		exit;
	}
}

else if (isset($_POST['move'])) {

	if (!is_array($_POST['check'])) {
		$msg->addError('NO_FILE_SELECT');
	} else {

		$list = implode(',', $_POST['check']);		
		header('Location: move.php?pathext='.urlencode($_POST['pathext']).SEP.'framed='.$framed.SEP.'popup='.$popup.SEP.'list='.urlencode($list));
		exit;
	}
}

$MakeDirOn = true;

/* get this courses MaxQuota and MaxFileSize: */
$sql	= "SELECT max_quota, max_file_size FROM ".TABLE_PREFIX."courses WHERE course_id=$_SESSION[course_id]";
$result = mysql_query($sql, $db);
$row	= mysql_fetch_array($result);
$my_MaxCourseSize	= $row['max_quota'];
$my_MaxFileSize		= $row['max_file_size'];

if ($my_MaxCourseSize == AT_COURSESIZE_DEFAULT) {
	$my_MaxCourseSize = $MaxCourseSize;
}
if ($my_MaxFileSize == AT_FILESIZE_DEFAULT) {
	$my_MaxFileSize = $MaxFileSize;
} else if ($my_MaxFileSize == AT_FILESIZE_SYSTEM_MAX) {
	$my_MaxFileSize = megabytes_to_bytes(substr(ini_get('upload_max_filesize'), 0, -1));
}

$MaxSubDirs  = 5;
$MaxDirDepth = 3;

if ($_GET['pathext'] != '') {
	$pathext = urldecode($_GET['pathext']);
} else if ($_POST['pathext'] != '') {
	$pathext = $_POST['pathext'];
}

if (strpos($pathext, '..') !== false) {
	require($_header_file);
	$msg->printErrors('UNKNOWN');	
	require($_footer_file);
	exit;
}
if($_GET['back'] == 1) {
	$pathext  = substr($pathext, 0, -1);
	$slashpos = strrpos($pathext, '/');
	if($slashpos == 0) {
		$pathext = '';
	} else {
		$pathext = substr($pathext, 0, ($slashpos+1));
	}

}

$start_at = 2;
/* remove the forward or backwards slash from the path */
$newpath = $current_path;
$depth = substr_count($pathext, '/');

/*if ($pathext != '') {
	$bits = explode('/', $pathext);
	foreach ($bits as $bit) {
		if ($bit != '') {
			$bit_path .= $bit . '/';
			echo ' / ';
			if ($bit_path == $pathext) {
				echo $bit;
			}
			else {
				echo '<a href="'.$_SERVER['PHP_SELF'].'?pathext=' . urlencode($bit_path) . SEP . 'popup=' . $popup . SEP . 'framed=' . $framed . '">' . $bit . '</a>';
			}
		}
	}
}*/

/* if upload successful, close the window */
if ($f) {
	$onload = 'onbeforeload="closeWindow(\'progWin\');"';
}

require($_header_file);

/* make new directory */
if ($_POST['mkdir_value'] && ($depth < $MaxDirDepth) ) {
	$_POST['dirname'] = trim($_POST['dirname']);

	/* anything else should be okay, since we're on *nix..hopefully */
	$_POST['dirname'] = ereg_replace('[^a-zA-Z0-9._]', '', $_POST['dirname']);

	if ($_POST['dirname'] == '') {
		$msg->addErrors('FOLDER_NOT_CREATED');
	} 
	else if (strpos($_POST['dirname'], '..') !== false) {
		$msg->addError('UNKNOWN');
	}	
	else {
		$result = @mkdir($current_path.$pathext.$_POST['dirname'], 0700);
		if($result == 0) {
			$msg->addErrors('FOLDER_NOT_CREATED');
		}
		else {
			$msg->addFeedback('FOLDER_CREATED');
		}
	}
}

$newpath = substr($current_path.$pathext, 0, -1);

/* open the directory */
if (!($dir = @opendir($newpath))) {
	if (isset($_GET['create']) && ($newpath.'/' == $current_path)) {
		@mkdir($newpath);
		if (!($dir = @opendir($newpath))) {
			$msg->printErrors('CANNOT_CREATE_DIR');			
			require($_footer_file);
			exit;
		} else {
			$msg->addFeedback('CONTENT_DIR_CREATED');
		}
	} else {
		$msg->printErrors('CANNOT_OPEN_DIR');
		require($_footer_file);
		exit;
	}
}


if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
}
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
		echo _AT('file_manager')."\n";
	}
	echo '</h3>'."\n";
}
	
?>