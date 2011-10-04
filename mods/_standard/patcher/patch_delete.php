<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_PATCHER);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: myown_patches.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	/* delete has been confirmed, delete this category */
	$myown_patch_id	= intval($_POST['myown_patch_id']);

	$sql = "DELETE FROM ".TABLE_PREFIX."myown_patches WHERE myown_patch_id=$myown_patch_id";
	$result = mysql_query($sql, $db) or die(mysql_error());

	write_to_log(AT_ADMIN_LOG_DELETE, 'myown_patches', mysql_affected_rows($db), $sql);

	$sql = "DELETE FROM ".TABLE_PREFIX."myown_patches_dependent WHERE myown_patch_id=$myown_patch_id";
	$result = mysql_query($sql, $db) or die(mysql_error());

	write_to_log(AT_ADMIN_LOG_DELETE, 'myown_patches_dependent', mysql_affected_rows($db), $sql);

	$sql = "DELETE FROM ".TABLE_PREFIX."myown_patches_files WHERE myown_patch_id=$myown_patch_id";
	$result = mysql_query($sql, $db) or die(mysql_error());

	write_to_log(AT_ADMIN_LOG_DELETE, 'myown_patches_files', mysql_affected_rows($db), $sql);

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: myown_patches.php');
	exit;
}

//require('../../include/header.inc.php');
require(AT_INCLUDE_PATH.'header.inc.php');

$_GET['myown_patch_id'] = intval($_GET['myown_patch_id']); 

$sql = "SELECT myown_patch_id, atutor_patch_id FROM ".TABLE_PREFIX."myown_patches m WHERE m.myown_patch_id=$_GET[myown_patch_id]";
$result = mysql_query($sql,$db) or die(mysql_error());

if (mysql_num_rows($result) == 0) {
	$msg->printErrors('ITEM_NOT_FOUND');
} else {
	$row = mysql_fetch_assoc($result);
	
	$hidden_vars['atutor_patch_id']= $row['atutor_patch_id'];
	$hidden_vars['myown_patch_id']	= $row['myown_patch_id'];

	$confirm = array('DELETE_MYOWN_PATCH', $row['atutor_patch_id']);
	$msg->addConfirm($confirm, $hidden_vars);
	
	$msg->printConfirm();
}

require(AT_INCLUDE_PATH.'footer.inc.php');

?>