<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
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

authenticate(AT_PRIV_TEST_CREATE);

if (isset($_GET['catid']) && $_GET['d']) {
	$_GET['catid'] = intval($_GET['catid']);

	//remove cat
	$sql = "DELETE FROM ".TABLE_PREFIX."tests_questions_categories WHERE course_id=$_SESSION[course_id] AND category_id=".$_GET['catid'];
	$result = mysql_query($sql, $db);

	//set all q's that use this cat to have cat=0
	$sql = "UPDATE ".TABLE_PREFIX."tests_questions SET category_id=0 WHERE course_id=$_SESSION[course_id] AND category_id=".$_GET['catid'];
	$result = mysql_query($sql, $db);

	$msg->addFeedback('CAT_DELETED');
	header('Location: question_cats.php');
	exit;

} else if ($_GET['d']) {
	$msg->addFeedback('CANCELLED');
	header('Location: question_cats.php');
	exit;
} else if (!isset($_GET['catid'])) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->addError('CAT_NOT_FOUND');
	$msg->printErrors();
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
} 

require(AT_INCLUDE_PATH.'header.inc.php');

$sql	= "SELECT title FROM ".TABLE_PREFIX."tests_questions_categories WHERE course_id=$_SESSION[course_id] AND category_id=$_GET[catid]";
$result	= mysql_query($sql, $db);
$row = mysql_fetch_array($result);

$msg->addWarning(array('DELETE_CAT_CATEGORY',$row['title']));
$msg->printWarnings();

echo '<p align="center"><a href="tools/tests/question_cats_delete.php?catid='.$_GET['catid'].SEP.'d=1'.'">'._AT('yes_delete').'</a> | <a href="tools/tests/question_cats_delete.php?d=1">'._AT('no_cancel').'</a></p>';


require(AT_INCLUDE_PATH.'footer.inc.php');
?>