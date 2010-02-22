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
// $Id: course_properties.php 9178 2010-01-25 18:45:40Z cindy $

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/tinymce.inc.php');
require(AT_INCLUDE_PATH.'../mods/_core/backups/classes/Backup.class.php');
require(AT_INCLUDE_PATH.'../mods/_standard/file_storage/file_storage.inc.php');

authenticate(AT_PRIV_ADMIN);

$course = $_SESSION['course_id'];
$isadmin   = FALSE;

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: ../../../tools/index.php');
	exit;


}else if($_POST['submit']){
	require(AT_INCLUDE_PATH.'../mods/_core/properties/lib/course.inc.php');
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
	require(AT_INCLUDE_PATH.'mods/_core/properties/lib/course.inc.php');
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

require(AT_INCLUDE_PATH.'../mods/_core/properties/html/course_properties.inc.php');

require(AT_INCLUDE_PATH.'footer.inc.php');


?>