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

require(AT_INCLUDE_PATH.'classes/Backup/Backup.class.php');

authenticate(AT_PRIV_ADMIN);


$course = $_SESSION['course_id'];
$isadmin   = FALSE;

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} else if (isset($_POST['course'])) {
	require(AT_INCLUDE_PATH.'lib/course.inc.php');
	$_POST['instructor'] = $_SESSION['member_id'];

	$errors = add_update_course($_POST);

	if (is_numeric($errors)) {
		$msg->addFeedback('COURSE_PROPERTIES');
		header('Location: '.$_base_href.'tools/index.php');	
		exit;
	}
}

$onload = 'document.course_form.title.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');

require(AT_INCLUDE_PATH.'html/course_properties.inc.php');

require(AT_INCLUDE_PATH.'footer.inc.php');

?>