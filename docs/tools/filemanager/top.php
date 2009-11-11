<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

if (!defined('AT_INCLUDE_PATH')) { exit; }
//require(AT_INCLUDE_PATH.'vitals.inc.php');
//require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');

if (!$_GET['f']) {
	$_SESSION['done'] = 0;
}
if (!authenticate(AT_PRIV_FILES,AT_PRIV_RETURN)) {
	authenticate(AT_PRIV_CONTENT);
}


$current_path = AT_CONTENT_DIR.$_SESSION['course_id'].'/';


if (isset($_POST['rename'])) {
	if (!is_array($_POST['check'])) {
		// error: you must select a file/dir to rename
		$msg->addError('NO_ITEM_SELECTED');
	} else if (count($_POST['check']) < 1) {
		// error: you must select one file/dir to rename
		$msg->addError('NO_ITEM_SELECTED');
	} else if (count($_POST['check']) > 1) {
		// error: you must select ONLY one file/dir to rename
		$msg->addError('SELECT_ONE_ITEM');
	} else {
		header('Location: rename.php?pathext='.urlencode($_POST['pathext']).SEP.'framed='.$framed.SEP.'popup='.$popup.SEP.'oldname='.urlencode($_POST['check'][0]));
		exit;
	}
} else if (isset($_POST['delete'])) {
	
	if (!is_array($_POST['check'])) {
		$msg->addError('NO_ITEM_SELECTED');
	} else {

		$list = implode(',', $_POST['check']);
		header('Location: delete.php?pathext=' . urlencode($_POST['pathext']) . SEP . 'framed=' . $framed . SEP . 'popup=' . $popup . SEP . 'list=' . urlencode($list));
		exit;
	}
} else if (isset($_POST['move'])) {

	if (!is_array($_POST['check'])) {
		$msg->addError('NO_ITEM_SELECTED');
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
$MaxDirDepth = 10;

if ($_GET['pathext'] != '') {
	$pathext = urldecode($_GET['pathext']);
} else if ($_POST['pathext'] != '') {
	$pathext = $_POST['pathext'];
}

if (strpos($pathext, '..') !== false) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printErrors('UNKNOWN');	
	require(AT_INCLUDE_PATH.'footer.inc.php');
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

if ($pathext != '') {
	$bits = explode('/', $pathext);
	foreach ($bits as $bit) {
		if ($bit != '') {
			$bit_path .= $bit;

			$_section[$start_at][0] = $bit;
			$_section[$start_at][1] = 'tools/filemanager/index.php?pathext=' . urlencode($bit_path) . SEP . 'popup=' . $popup . SEP . 'framed=' . $framed;

			$start_at++;
		}
	}
	$bit_path = "";
	$bit = "";
}

/* if upload successful, close the window */
if ($f) {
	$onload = 'closeWindow(\'progWin\');';
}

/* make new directory */
if ($_POST['mkdir_value'] && ($depth < $MaxDirDepth) ) {
	$_POST['dirname'] = trim($_POST['dirname']);

	/* anything else should be okay, since we're on *nix..hopefully */
	$_POST['dirname'] = preg_replace('/[^a-zA-Z0-9._]/', '', $_POST['dirname']);

	if ($_POST['dirname'] == '') {
		$msg->addError(array('FOLDER_NOT_CREATED', $_POST['dirname'] ));
	} 
	else if (strpos($_POST['dirname'], '..') !== false) {
		$msg->addError('BAD_FOLDER_NAME');
	}	
	else {
		$result = @mkdir($current_path.$pathext.$_POST['dirname'], 0700);
		if($result == 0) {
			$msg->addError(array('FOLDER_NOT_CREATED', $_POST['dirname'] ));
		}
		else {
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		}
	}
}

$newpath = substr($current_path.$pathext, 0, -1);

/* open the directory */
if (!($dir = @opendir($newpath))) {
	if (isset($_GET['create']) && ($newpath.'/' == $current_path)) {
		@mkdir($newpath);
		if (!($dir = @opendir($newpath))) {
			require(AT_INCLUDE_PATH.'header.inc.php');
			$msg->printErrors('CANNOT_CREATE_DIR');			
			require(AT_INCLUDE_PATH.'footer.inc.php');
			exit;
		} else {
			$msg->addFeedback('CONTENT_DIR_CREATED');
		}
	} else {
		require(AT_INCLUDE_PATH.'header.inc.php');

		$msg->printErrors('CANNOT_OPEN_DIR');
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
}

require(AT_INCLUDE_PATH.'header.inc.php');
?>