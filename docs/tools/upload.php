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
// $Id$

define('AT_INCLUDE_PATH', '../include/');
$_ignore_page = true; /* used for the close the page option */
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');

authenticate(AT_PRIV_FILES);

/* get this courses MaxQuota and MaxFileSize: */
$sql	= "SELECT max_quota, max_file_size FROM ".TABLE_PREFIX."courses WHERE course_id=$_SESSION[course_id]";
$result = mysql_query($sql, $db);
$row	= mysql_fetch_array($result);
$my_MaxCourseSize	= $row['max_quota'];
$my_MaxFileSize	= $row['max_file_size'];

	if ($my_MaxCourseSize == AT_COURSESIZE_DEFAULT) {
		$my_MaxCourseSize = $MaxCourseSize;
	}
	if ($my_MaxFileSize == AT_FILESIZE_DEFAULT) {
		$my_MaxFileSize = $MaxFileSize;
	} else if ($my_MaxFileSize == AT_FILESIZE_SYSTEM_MAX) {
		$my_MaxFileSize = megabytes_to_bytes(substr(ini_get('upload_max_filesize'), 0, -1));
	}

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('file_manager');

if ($_GET['frame'] == 1) {
	$_header_file = 'html/frameset/header.inc.php';
	$_footer_file = 'html/frameset/footer.inc.php';
} else {
	$_header_file = 'header.inc.php';
	$_footer_file = 'footer.inc.php';
}

$path = AT_CONTENT_DIR . $_SESSION['course_id'].'/'.$_POST['pathext'];

if ($_POST['submit']) {

require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

	if($_FILES['uploadedfile']['name'])	{


		$_FILES['uploadedfile']['name'] = trim($_FILES['uploadedfile']['name']);
		$_FILES['uploadedfile']['name'] = str_replace(' ', '_', $_FILES['uploadedfile']['name']);

		$path_parts = pathinfo($_FILES['uploadedfile']['name']);
		$ext = $path_parts['extension'];

		/* check if this file extension is allowed: */
		/* $IllegalExtentions is defined in ./include/config.inc.php */
		if (in_array($ext, $IllegalExtentions)) {
			require(AT_INCLUDE_PATH.$_header_file);
			$errors = array('FILE_ILLEGAL', $ext);
			$msg->printErrors($errors);
			echo '<a href="tools/file_manager.php?frame='.$_GET['frame'].'">'._AT('back').'</a>.';
			require(AT_INCLUDE_PATH.$_footer_file);
			exit;
		}

		/* also have to handle the 'application/x-zip-compressed'  case	*/
		if (($_FILES['uploadedfile']['type'] == 'application/x-zip-compressed')
			|| ($_FILES['uploadedfile']['type'] == 'application/zip')){
			$is_zip = true;						
		}

	
		/* anything else should be okay, since we're on *nix.. hopefully */
		$_FILES['uploadedfile']['name'] = str_replace(array(' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|', '\''), '', $_FILES['uploadedfile']['name']);


		/* if the file size is within allowed limits */
		if( ($_FILES['uploadedfile']['size'] > 0) && ($_FILES['uploadedfile']['size'] <= $my_MaxFileSize) ) {

			/* if adding the file will not exceed the maximum allowed total */
			$course_total = dirsize($path);

			if ((($course_total + $_FILES['uploadedfile']['size']) <= ($my_MaxCourseSize + $MaxCourseFloat)) || ($my_MaxCourseSize == AT_COURSESIZE_UNLIMITED)) {

				/* check if this file exists first */
				if (file_exists($path.$_FILES['uploadedfile']['name'])) {
					/* this file already exists, so we want to prompt for override */

					/* save it somewhere else, temporarily first			*/
					/* file_name.time ? */
					$_FILES['uploadedfile']['name'] = substr(time(), -4).'.'.$_FILES['uploadedfile']['name'];

					$f = array('FILE_EXISTS',
									substr($_FILES['uploadedfile']['name'], 5), 
									$_FILES['uploadedfile']['name'],
									$_POST['pathext'],
									SEP);
					$msg->addFeedback($f);
				}

				/* copy the file in the directory */
				$result = move_uploaded_file( $_FILES['uploadedfile']['tmp_name'], $path.$_FILES['uploadedfile']['name'] );

				if (!$result) {
					require(AT_INCLUDE_PATH.$_header_file);
					$msg->printErrors('FILE_NOT_SAVED');
					echo '<a href="tools/file_manager.php?frame='.$_GET['frame'].'">'._AT('back').'</a>.';
					require(AT_INCLUDE_PATH.$_footer_file);
					exit;
				} else {
					if ($is_zip) {
						$f = array('FILE_UPLOADED_ZIP',
										$_POST['pathext'].$_FILES['uploadedfile']['name'], 
										$_GET['frame'],
										SEP);
						$msg->addFeedback($f);
						
						$_SESSION['done'] = 1;
						header('Location: ./file_manager.php?pathext='
								.$_POST['pathext']
								.SEP.'frame='.$_GET[frame]);
						exit;

					} /* else */

					$msg->addFeedback('FILE_UPLOADED');

					$_SESSION['done'] = 1;
					header('Location: ./file_manager.php?pathext='.
							$_POST['pathext']
							.SEP.'frame='.$_GET['frame']);
					exit;
				}
			} else {
				$_SESSION['done'] = 1;
				require(AT_INCLUDE_PATH.$_header_file);
				$errors = array('MAX_STORAGE_EXCEEDED','('.$my_MaxCourseSize.' Bytes)');
				$msg->printErrors($errors);
				echo '<a href="tools/file_manager.php?frame='.$_GET['frame'].'">'._AT('back').'</a>.';
				require(AT_INCLUDE_PATH.$_footer_file);
				exit;
			}
		} else {
			$_SESSION['done'] = 1;
			require(AT_INCLUDE_PATH.$_header_file);
			$errors = array('FILE_TOO_BIG','('.$my_MaxFileSize.' Bytes)');
			$msg->printErrors($errors);
			echo '<a href="tools/file_manager.php?frame='.$_GET['frame'].'">'._AT('back').'</a>.';
			require(AT_INCLUDE_PATH.$_footer_file);
			exit;
		}
	} else {
		$_SESSION['done'] = 1;
		require(AT_INCLUDE_PATH.$_header_file);
		$msg->printErrors('FILE_NOT_SELECTED');
		echo '<a href="tools/file_manager.php?frame='.$_GET['frame'].'">'._AT('back').'</a>.';
		require(AT_INCLUDE_PATH.$_footer_file);
		exit;
	}
}


?>