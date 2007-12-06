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
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/tinymce.inc.php');
require(AT_INCLUDE_PATH.'classes/Backup/Backup.class.php');
require(AT_INCLUDE_PATH.'lib/file_storage.inc.php');

authenticate(AT_PRIV_ADMIN);


$course = $_SESSION['course_id'];
$isadmin   = FALSE;

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;


}else if($_POST['submit']){

    // added by Martin - for custom course icons

    if($_FILES['customicon']['tmp_name'] != ''){
        
        $_POST['comments'] = trim($_POST['comments']);

        $owner_id = $_SESSION['course_id'];
        $owner_type = "1";
            
        if ($_FILES['customicon']['error'] == UPLOAD_ERR_INI_SIZE) {
            $msg->addError(array('FILE_TOO_BIG', get_human_size(megabytes_to_bytes(substr(ini_get('upload_max_filesize'), 0, -1)))));

        } else if (!isset($_FILES['customicon']['name']) || ($_FILES['customicon']['error'] == UPLOAD_ERR_NO_FILE) || ($_FILES['customicon']['size'] == 0)) {
            $msg->addError('FILE_NOT_SELECTED');

        } else if ($_FILES['customicon']['error'] || !is_uploaded_file($_FILES['customicon']['tmp_name'])) {
            $msg->addError('FILE_NOT_SAVED');
        }

        
        if (!$msg->containsErrors()) {
            $_POST['description'] = $addslashes(trim($_POST['description']));
            $_FILES['customicon']['name'] = addslashes($_FILES['customicon']['name']);

            if ($_POST['comments']) {
                $num_comments = 1;
            } else {
                $num_comments = 0;
            }

            
            $path = AT_CONTENT_DIR.$owner_id."/custom_icons/";
            if (!is_dir($path)) {
                @mkdir($path);
            }
            if (!move_uploaded_file($_FILES['customicon']['tmp_name'], $path . $_FILES['customicon']['name'])) {
                $msg->addError('FILE_NOT_SAVED');
            } else {
                $msg->addFeedback('FILE_UPLOADED');
            }
        
        } else {
            $msg->addError('FILE_NOT_SAVED');
            
        }
        $_POST['icon'] = $_FILES['customicon']['name'];
        //header('Location: index.php'.$owner_arg_prefix.'folder='.$parent_folder_id);
        //exit;
    }

    //----------------------------------------

	require(AT_INCLUDE_PATH.'lib/course.inc.php');
	$_POST['instructor'] = $_SESSION['member_id'];

	$errors = add_update_course($_POST);

	if (is_numeric($errors)) {
		$msg->addFeedback('COURSE_PROPERTIES');
		header('Location: '.AT_BASE_HREF.'tools/index.php');	
		exit;
	}
		
//}else if(($_POST['setvisual'] && !$_POST['settext']) || $_GET['setvisual'])){
} else if (($_POST['setvisual'] || $_POST['settext'])){
		//header('Location: '.$_SESSION['PHP_SELF'].'');	
		//exit;
} else if (isset($_POST['course'])) {
	require(AT_INCLUDE_PATH.'lib/course.inc.php');
	$_POST['instructor'] = $_SESSION['member_id'];

	$errors = add_update_course($_POST);

	if (is_numeric($errors)) {
		$msg->addFeedback('COURSE_PROPERTIES');
		header('Location: '.AT_BASE_HREF.'tools/index.php');	
		exit;
	}
}

$onload = 'document.course_form.title.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');

require(AT_INCLUDE_PATH.'html/course_properties.inc.php');

require(AT_INCLUDE_PATH.'footer.inc.php');


?>