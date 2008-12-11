<?php
/************************************************************************/
/* ATutor								*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto		*/
/* http://atutor.ca							*/
/*									*/
/* This program is free software. You can redistribute it and/or	*/
/* modify it under the terms of the GNU General Public License		*/
/* as published by the Free Software Foundation.			*/
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_CONTENT);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: ./index.php');
	exit;
}

else if (isset($_POST['submit_yes'])) {
	//clean up the db
	$sql    = "DELETE FROM ".TABLE_PREFIX."member_track WHERE course_id=$_SESSION[course_id]";
	$result = mysql_query($sql, $db);

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: ./index.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

	//print confirmation
	$hidden_vars['all'] = TRUE;

	$msg->addConfirm('DELETE_TRACKING', $hidden_vars);
	$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php');

?>