<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_TESTS);

$tid = intval($_REQUEST['tid']);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: question_db.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	$_POST['qid'] = explode(',', $_POST['qid']);
	foreach ($_POST['qid'] as $id) {
		$id = intval($id);

		$sql	= "DELETE FROM ".TABLE_PREFIX."tests_questions WHERE question_id=$id AND course_id=$_SESSION[course_id]";
		$result	= mysql_query($sql, $db);

		if (mysql_affected_rows($db) == 1) {
			$sql	= "DELETE FROM ".TABLE_PREFIX."tests_questions_assoc WHERE question_id=$id";
			$result	= mysql_query($sql, $db);
		}
	}
		
	$msg->addFeedback('QUESTION_DELETED');
	header('Location: question_db.php');
	exit;
} /* else: */

require(AT_INCLUDE_PATH.'header.inc.php');

unset($hidden_vars);
$hidden_vars['qid'] = htmlspecialchars($_GET['qid']);
$msg->addConfirm('DELETE', $hidden_vars);

$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php');
?>