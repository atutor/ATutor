<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

$page = 'tests';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_TESTS);
if (isset($_POST['submit_yes'])) {
	$_POST['catid'] = intval($_POST['catid']);

	//remove cat
	$sql = "DELETE FROM ".TABLE_PREFIX."tests_questions_categories WHERE course_id=$_SESSION[course_id] AND category_id=".$_POST['catid'];
	$result = mysql_query($sql, $db);

	//set all q's that use this cat to have cat=0
	$sql = "UPDATE ".TABLE_PREFIX."tests_questions SET category_id=0 WHERE course_id=$_SESSION[course_id] AND category_id=".$_POST['catid'];
	$result = mysql_query($sql, $db);

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: '.AT_BASE_HREF.'tools/tests/question_cats.php');
	exit;

} else if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'tools/tests/question_cats.php');
	exit;
} else if (!isset($_GET['catid'])) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->addError('ITEM_NOT_FOUND');
	$msg->printErrors();
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
} 

require(AT_INCLUDE_PATH.'header.inc.php');

$_GET['catid'] = intval($_GET['catid']);

$sql	= "SELECT title FROM ".TABLE_PREFIX."tests_questions_categories WHERE course_id=$_SESSION[course_id] AND category_id=$_GET[catid]";
$result	= mysql_query($sql, $db);
$row = mysql_fetch_array($result);

$hidden_vars['catid'] = $_GET['catid'];

$msg->addConfirm(array('DELETE_TEST_CATEGORY', $row['title']), $hidden_vars);
	
$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php');
?>