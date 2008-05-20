<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_GLOSSARY);

if ($_POST['submit_no']) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
}

if ($_POST['submit_yes']) {

	$_POST['gid'] = intval($_POST['gid']);

	$sql = "DELETE FROM ".TABLE_PREFIX."glossary WHERE word_id=$_POST[gid] AND course_id=$_SESSION[course_id]";
	$result = mysql_query($sql, $db);

	$sql = "UPDATE ".TABLE_PREFIX."glossary SET related_word_id=0 WHERE related_word_id=$_POST[gid] AND course_id=$_SESSION[course_id]";
	$result = mysql_query($sql, $db);

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: index.php');
	exit;
} else if ($_POST['submit_no']) {

	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$_GET['gid'] = intval($_GET['gid']);

if ($_GET['gid'] == 0) {
	$msg->printErrors('ITEM_NOT_FOUND');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$hidden_vars['word'] = $_GET['t'];
$hidden_vars['gid']  = $_GET['gid'];

$sql = "SELECT * from ".TABLE_PREFIX."glossary WHERE word_id = '$hidden_vars[gid]'";
$result = mysql_query($sql, $db);
while ($row = mysql_fetch_assoc($result)){
	$title = $row['word'];
}
		
$msg->addConfirm(array('DELETE', $title),  $hidden_vars);
$msg->addConfirm('GLOSSARY_REMAINS', $hidden_vars);
	
$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php');
?>