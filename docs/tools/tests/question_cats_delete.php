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
require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

authenticate(AT_PRIV_TEST_CREATE);

global $savant;
$msg =& new Message($savant);

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/index.php';
$_section[1][0] = _AT('test_manager');
$_section[1][1] = 'tools/tests/index.php';
$_section[2][0] = _AT('question_database');
$_section[2][1] = 'tools/tests/question_db.php';
$_section[3][0] = _AT('cats_categories');
$_section[3][1] = 'tools/tests/question_cats.php';
$_section[4][0] = _AT('delete_category');

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

echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<a href="tools/" class="hide"><img src="images/icons/default/square-large-tools.gif"  class="menuimageh2" border="0" vspace="2" width="42" height="40" alt="" /></a>';
}
echo ' <a href="tools/" class="hide">'._AT('tools').'</a>';
echo '</h2>';

echo '<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '&nbsp;<img src="images/icons/default/test-manager-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
}
echo '<a href="tools/tests/index.php">'._AT('test_manager').'</a>';
echo '</h3>';

echo '<h4><a href="tools/tests/question_db.php">' . _AT('question_database') . '</h4>';


$sql	= "SELECT title FROM ".TABLE_PREFIX."tests_questions_categories WHERE course_id=$_SESSION[course_id] AND category_id=$_GET[catid]";
$result	= mysql_query($sql, $db);
$row = mysql_fetch_array($result);

$msg->addWarning(array('DELETE_CAT_CATEGORY',$row['title']));
$msg->printWarnings();

echo '<p align="center"><a href="tools/tests/question_cats_delete.php?catid='.$_GET['catid'].SEP.'d=1'.'">'._AT('yes_delete').'</a> | <a href="tools/tests/question_cats_delete.php?d=1">'._AT('no_cancel').'</a></p>';


require(AT_INCLUDE_PATH.'footer.inc.php');
?>