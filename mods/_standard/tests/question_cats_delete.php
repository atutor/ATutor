<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

$page = 'tests';
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_TESTS);
if (isset($_POST['submit_yes'])) {
	//remove cat
	$sql = "DELETE FROM %stests_questions_categories WHERE course_id=%d AND category_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'], $_POST['catid']));
	
    if($result > 0){
        $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
    }

	//set all q's that use this cat to have cat=0
	$sql = "UPDATE %stests_questions SET category_id=0 WHERE course_id=%d AND category_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'], $_POST['catid']));

	header('Location: '.AT_BASE_HREF.'mods/_standard/tests/question_cats.php');
	exit;

} else if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'mods/_standard/tests/question_cats.php');
	exit;
} else if (!isset($_GET['catid'])) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->addError('ITEM_NOT_FOUND');
	$msg->printErrors();
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
} 

require(AT_INCLUDE_PATH.'header.inc.php');

$sql	= "SELECT title FROM %stests_questions_categories WHERE course_id=%d AND category_id=%d";
$row	= queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'], $_GET['catid']), TRUE);

$hidden_vars['catid'] = $_GET['catid'];

$msg->addConfirm(array('DELETE_TEST_CATEGORY', $row['title']), $hidden_vars);
	
$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php');
?>