<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2010                                                  */
/* Inclusive Design Institute                                               */
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$

$page = 'gradebook';

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_GRADEBOOK);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: gradebook_tests.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	/* delete has been confirmed, delete this category */
	$gradebook_test_id	= intval($_POST['gradebook_test_id']);

	$sql = "DELETE FROM %sgradebook_tests WHERE gradebook_test_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $gradebook_test_id));

	$sql = "DELETE FROM %sgradebook_detail WHERE gradebook_test_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $gradebook_test_id));

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: gradebook_tests.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$_GET['gradebook_test_id'] = intval($_GET['gradebook_test_id']); 

$sql = "(SELECT g.gradebook_test_id, t.title".
				" FROM %sgradebook_tests g, %stests t".
				" WHERE g.type='ATutor Test'".
				" AND g.id = t.test_id".
				" AND g.gradebook_test_id=%d)".
				" UNION (SELECT g.gradebook_test_id, a.title".
				" FROM %sgradebook_tests g, %sassignments a".
				" WHERE g.type='ATutor Assignment'".
				" AND g.id = a.assignment_id".
				" AND g.gradebook_test_id=%d)".
				" UNION (SELECT gradebook_test_id, title ".
				" FROM %sgradebook_tests".
				" WHERE type='External'".
				" AND gradebook_test_id=%d)";
			
$row = queryDB($sql,array(TABLE_PREFIX, TABLE_PREFIX, $_GET['gradebook_test_id'], TABLE_PREFIX, TABLE_PREFIX, $_GET['gradebook_test_id'], TABLE_PREFIX, $_GET['gradebook_test_id']), TRUE);
if(count($row) == 0){
	$msg->printErrors('ITEM_NOT_FOUND');
} else {
	
	$hidden_vars['title']= $row["title"];
	$hidden_vars['gradebook_test_id']	= $row['gradebook_test_id'];

	$confirm = array('DELETE_TEST_FROM_GRADEBOOK', $row["title"]);
	$msg->addConfirm($confirm, $hidden_vars);
	
	$msg->printConfirm();
}

require(AT_INCLUDE_PATH.'footer.inc.php');

?>