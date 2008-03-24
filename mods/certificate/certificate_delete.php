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
// $Id: certificate_delete.php 7208 2008-02-20 16:07:24Z cindy $

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_CERTIFICATE);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index_instructor.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	/* delete has been confirmed, delete this category */
	$certificate_id	= intval($_POST['certificate_id']);

	$sql = "DELETE FROM ".TABLE_PREFIX."certificate WHERE certificate_id=$certificate_id";
	$result = mysql_query($sql, $db) or die(mysql_error());

	write_to_log(AT_ADMIN_LOG_DELETE, 'certificate', mysql_affected_rows($db), $sql);

	$sql = "DELETE FROM ".TABLE_PREFIX."certificate_text WHERE certificate_id=$certificate_id";
	$result = mysql_query($sql, $db) or die(mysql_error());

	write_to_log(AT_ADMIN_LOG_DELETE, 'certificate_text', mysql_affected_rows($db), $sql);

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: index_instructor.php');
	exit;
}

//require('../../include/header.inc.php');
require(AT_INCLUDE_PATH.'header.inc.php');

$_GET['certificate_id'] = intval($_GET['certificate_id']); 

$sql = "SELECT certificate_id, title FROM ".TABLE_PREFIX."certificate c, ".TABLE_PREFIX."tests t WHERE c.certificate_id=$_GET[certificate_id] and c.test_id=t.test_id";
$result = mysql_query($sql,$db) or die(mysql_error());

if (mysql_num_rows($result) == 0) {
	$msg->printErrors('ITEM_NOT_FOUND');
} else {
	$row = mysql_fetch_assoc($result);
	
	$hidden_vars['title']= $row['title'];
	$hidden_vars['certificate_id']	= $row['certificate_id'];

	$confirm = array('DELETE_CERTIFICATE', $row['title']);
	$msg->addConfirm($confirm, $hidden_vars);
	
	$msg->printConfirm();
}

require(AT_INCLUDE_PATH.'footer.inc.php');

?>