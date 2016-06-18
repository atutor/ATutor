<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require_once(AT_INCLUDE_PATH.'../mods/_core/file_manager/filemanager.inc.php');

if (!authenticate(AT_PRIV_FILES,AT_PRIV_RETURN)) {
	authenticate(AT_PRIV_CONTENT);
}

$_SESSION['done'] = 1;
$popup = intval($_REQUEST['popup']);
$framed = intval($_REQUEST['framed']);
$alter = intval($_REQUEST['alter']);
					
/* get this courses MaxQuota and MaxFileSize: */
$sql	= "SELECT max_quota, max_file_size FROM %scourses WHERE course_id=%d";
$row = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']), TRUE);

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
	
	// Check if  the filemanager directory path is a valid dirname/filename
	if (strpbrk($_POST['pathext'], "\\?%*:|\"<>") === FALSE) {
	    $_POST['pathext']= preg_replace("/\.\./i", "", $_POST['pathext']);
        /* $_POST['pathext'] is a folder or file; doesn't contain illegal character. */
    } else {
        /* $filename contains at least one illegal character, so make empty. */
        $_POST['pathext'] = '';
    }
	
$path = AT_CONTENT_DIR . $_SESSION['course_id'].'/'.$_POST['pathext'];

if (isset($_POST['submit'])) {   
    if($_FILES['file']) {
       $_FILES['uploadedfile'] = $_FILES['file']; 
    }
	if($_FILES['uploadedfile']['name'])	{

		$_FILES['uploadedfile']['name'] = trim($_FILES['uploadedfile']['name']);
		$_FILES['uploadedfile']['name'] = strip_tags(str_replace(' ', '_', $_FILES['uploadedfile']['name']));

		/* anything else should be okay, since we're on *nix.. hopefully */
		$_FILES['uploadedfile']['name'] = str_replace(array(' ', ',', '/', '\\', ':', ';', '*', '?', '"', '<', '>', '|', '\'','\.\.'), '', $_FILES['uploadedfile']['name']);

		$path_parts = pathinfo($_FILES['uploadedfile']['name']);
		$ext = $path_parts['extension'];

		/* check if this file extension is allowed: */
		/* $IllegalExtentions is defined in ./include/config.inc.php */
		if (in_array($ext, $IllegalExtentions) || $ext=="") {
			$errors = array('FILE_ILLEGAL', $ext);
			$msg->addError($errors);
			handleAjaxUpload(500);
			header('Location: index.php?pathext='.$_POST['pathext'].SEP. 'framed='.$framed.SEP.'cp='.$_GET['cp'].SEP.'pid='.$_GET['pid'].SEP.'cid='.$_GET['cid'].SEP.'a_type='.$_GET['a_type']);
			exit;
		}

		/* also have to handle the 'application/x-zip-compressed'  case	*/
		if (   ($_FILES['uploadedfile']['type'] == 'application/x-zip-compressed')
			|| ($_FILES['uploadedfile']['type'] == 'application/zip')
			|| ($_FILES['uploadedfile']['type'] == 'application/x-zip')){
			$is_zip = true;						
		}

	


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
									$_GET['popup'],
									SEP);
					$msg->addFeedback($f);
				}

				/* copy the file in the directory */
				$result = move_uploaded_file( $_FILES['uploadedfile']['tmp_name'], $path.$_FILES['uploadedfile']['name'] );

				if (!$result) {
					require(AT_INCLUDE_PATH.'header.inc.php');
					$msg->printErrors('FILE_NOT_SAVED');
					echo '<a href="../mods/_core/file_manager/index.php?pathext=' . $_POST['pathext'] . SEP . 'popup=' . $_GET['popup'] . SEP. 'framed='.$framed.SEP.'cp='.$_GET['cp'].SEP.'pid='.$_GET['pid'].SEP.'cid='.$_GET['cid'].SEP.'a_type='.$_GET['a_type'].'">' . _AT('back') . '</a>';
					require(AT_INCLUDE_PATH.'footer.inc.php');
					exit;
				} else {
					if ($is_zip) {
						$f = array('FILE_UPLOADED_ZIP',
										urlencode($_POST['pathext']), 
										urlencode($_FILES['uploadedfile']['name']), 
										$_GET['popup'],
										SEP);
						$msg->addFeedback($f);
						handleAjaxUpload(200);
						if ($alter)
							header('Location: '.$_base_href.'editor/edit_content.php?cid='.$_REQUEST['cid'].SEP . 'pathext='.$_POST['pathext'].SEP. 'popup='.$_GET['popup'].SEP. 'tab='.$_REQUEST['tab']);
						else
							header('Location: index.php?pathext=' . $_POST['pathext'] . SEP . 'popup=' . $_GET['popup'].SEP. 'framed='.$framed.SEP.'cp='.$_GET['cp'].SEP.'pid='.$_GET['pid'].SEP.'cid='.$_GET['cid'].SEP.'a_type='.$_GET['a_type']);
						exit;
					} /* else */

					// uploading an alternative content object
					if ($_GET['a_type'] > 0) {
						header('Location: index.php?pathext=' . $_POST['pathext'] . SEP . 'popup=' . $_GET['popup'].SEP. 'framed='.$framed.SEP.'cp='.$_GET['cp'].SEP.'pid='.$_GET['pid'].SEP.'cid='.$_GET['cid'].SEP.'a_type='.$_GET['a_type'].SEP.'uploadfile='.urlencode($_FILES['uploadedfile']['name']));
					}
					else {
						$msg->addFeedback('FILE_UPLOADED');
						handleAjaxUpload(200);

						if ($alter)
							header('Location: '.$_base_href.'editor/edit_content.php?cid='.$_REQUEST['cid'].SEP . 'pathext='.$_POST['pathext'].SEP. 'popup='.$_GET['popup'].SEP. 'tab='.$_REQUEST['tab']);
						else
							header('Location: index.php?pathext=' . $_POST['pathext'] . SEP . 'popup=' . $_GET['popup'].SEP. 'framed='.$framed.SEP.'cp='.$_GET['cp'].SEP.'pid='.$_GET['pid'].SEP.'cid='.$_GET['cid'].SEP.'a_type='.$_GET['a_type']);
					}
					exit;
				}
			} else {
				$msg->addError(array('MAX_STORAGE_EXCEEDED', get_human_size($my_MaxCourseSize)));
				handleAjaxUpload(500);
				if ($alter)
							header('Location: '.$_base_href.'editor/edit_content.php?cid='.$_REQUEST['cid'].SEP . 'pathext='.$_POST['pathext'].SEP. 'popup='.$_GET['popup'].SEP. 'tab='.$_REQUEST['tab']);
						else
							header('Location: index.php?pathext=' . $_POST['pathext'] . SEP . 'popup=' . $_GET['popup'].SEP. 'framed='.$framed.SEP.'cp='.$_GET['cp'].SEP.'pid='.$_GET['pid'].SEP.'cid='.$_GET['cid'].SEP.'a_type='.$_GET['a_type']);
						
				exit;
			}
		} else {
			$msg->addError(array('FILE_TOO_BIG', get_human_size($my_MaxFileSize)));
			handleAjaxUpload(500);
			if ($alter)
							header('Location: '.$_base_href.'editor/edit_content.php?cid='.$_REQUEST['cid'].SEP . 'pathext='.$_POST['pathext'].SEP. 'popup='.$_GET['popup'].SEP. 'tab='.$_REQUEST['tab']);
						else
							header('Location: index.php?pathext=' . $_POST['pathext'] . SEP . 'popup=' . $_GET['popup'].SEP. 'framed='.$framed.SEP.'cp='.$_GET['cp'].SEP.'pid='.$_GET['pid'].SEP.'cid='.$_GET['cid'].SEP.'a_type='.$_GET['a_type']);
						
			exit;
		}
	} else {
		$msg->addError('FILE_NOT_SELECTED');
		handleAjaxUpload(500);
		if ($alter)
							header('Location: '.$_base_href.'editor/edit_content.php?cid='.$_REQUEST['cid'].SEP . 'pathext='.$_POST['pathext'].SEP. 'popup='.$_GET['popup'].SEP. 'tab='.$_REQUEST['tab']);
						else
							header('Location: index.php?pathext=' . $_POST['pathext'] . SEP . 'popup=' . $_GET['popup'].SEP. 'framed='.$framed.SEP.'cp='.$_GET['cp'].SEP.'pid='.$_GET['pid'].SEP.'cid='.$_GET['cid'].SEP.'a_type='.$_GET['a_type']);
		exit;
	}
}

?>
