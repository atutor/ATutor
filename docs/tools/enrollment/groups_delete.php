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
// $Id: questions.php 2326 2004-11-17 17:50:58Z heidi $

$page = 'tests';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

authenticate(AT_PRIV_TEST_CREATE);

global $savant;
$msg =& new Message($savant);

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/index.php';
$_section[1][0] = _AT('course_enrolment');
$_section[1][1] = 'tools/enrollment/index.php';
$_section[2][0] = _AT('groups');
$_section[2][1] = 'tools/enrollment/groups.php';
$_section[3][0] = _AT('delete_group');

if (isset($_GET['gid']) && $_GET['d']) {
	$_GET['gid'] = intval($_GET['gid']);

	//remove cat
	$sql = "DELETE FROM ".TABLE_PREFIX."groups WHERE course_id=$_SESSION[course_id] AND group_id=".$_GET['gid'];
	$result = mysql_query($sql, $db);

	if (mysql_affected_rows($db)) {
		//remove all listings in groups_members table
		$sql = "DELETE FROM ".TABLE_PREFIX."groups_members WHERE group_id=".$_GET['gid'];
		$result = mysql_query($sql, $db);

		//remove all listings in tests_groups table
		$sql = "DELETE FROM ".TABLE_PREFIX."tests_groups WHERE group_id=".$_GET['gid'];
		$result = mysql_query($sql, $db);
	}

	$msg->addFeedback('GROUP_DELETED');
	header('Location: groups.php');
	exit;

} else if ($_GET['d']) {
	$msg->addFeedback('CANCELLED');
	header('Location: groups.php');
	exit;
} else if (!isset($_GET['gid'])) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->addError('GROUP_NOT_FOUND');
	$msg->printErrors();
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
} 

require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/square-large-tools.gif" border="0" vspace="2"  class="menuimageh2" width="42" height="40" alt="" />';
} 
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo ' <a href="tools/" class="hide" >'._AT('tools').'</a>';
}
echo '</h2>';

echo '<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '&nbsp;<img src="images/icons/default/enrol_mng-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo ' <a href="tools/enrollment/">'._AT('course_enrolment').'</a>';
}
echo '</h3>';

$sql	= "SELECT title FROM ".TABLE_PREFIX."groups WHERE course_id=$_SESSION[course_id] AND group_id=$_GET[gid]";
$result	= mysql_query($sql, $db);
$row = mysql_fetch_array($result);

$msg->addWarning(array('DELETE_GROUP',$row['title']));
$msg->printWarnings();

echo '<p align="center"><a href="tools/enrollment/groups_delete.php?gid='.$_GET['gid'].SEP.'d=1'.'">'._AT('yes_delete').'</a> | <a href="tools/enrollment/groups_delete.php?d=1">'._AT('no_cancel').'</a></p>';


require(AT_INCLUDE_PATH.'footer.inc.php');
?>