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

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/file_storage.inc.php');

$owner_type = abs($_REQUEST['ot']);
$owner_id   = abs($_REQUEST['oid']);
$owner_arg_prefix = '?ot='.$owner_type.SEP.'oid='.$owner_id. SEP;
if (!($owner_status = fs_authenticate($owner_type, $owner_id)) || !query_bit($owner_status, WORKSPACE_AUTH_WRITE)) { 
	exit('NOT AUTHENTICATED');
}

$id = abs($_REQUEST['id']);

if (isset($_POST['submit_no'])) {
	$path = fs_get_revisions($id, $owner_type, $owner_id);
	reset($path);
	$first = current($path);

	$msg->addFeedback('CANCELLED');
	header('Location: revisions.php'.$owner_arg_prefix.'id='.$first['file_id']);
	exit;
} else if (isset($_POST['submit_yes'])) {
	$path = fs_get_revisions($id, $owner_type, $owner_id);

	// set the new parent //
	$sql = "SELECT parent_file_id, owner_type, owner_id, folder_id FROM ".TABLE_PREFIX."files WHERE file_id=$id AND owner_type=$owner_type AND owner_id=$owner_id";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);

	$sql = "UPDATE ".TABLE_PREFIX."files SET parent_file_id=$row[parent_file_id] WHERE parent_file_id=$id AND owner_type=$owner_type AND owner_id=$owner_id";
	mysql_query($sql, $db);

	$sql = "UPDATE ".TABLE_PREFIX."files SET num_revisions=num_revisions-1 WHERE file_id>$id AND owner_type=$row[owner_type] AND owner_id=$row[owner_id] AND folder_id=$row[folder_id]";
	mysql_query($sql, $db);

	$sql = "DELETE FROM ".TABLE_PREFIX."files WHERE file_id=$id AND owner_type=$owner_type AND owner_id=$owner_id";
	mysql_query($sql, $db);

	$sql = "DELETE FROM ".TABLE_PREFIX."files_comments WHERE file_id=$id";
	mysql_query($sql, $db);

	$file = fs_get_file_path($id);
	if (file_exists($file . $id)) {
		@unlink($file . $id);
	}

	$back_id = FALSE;
	foreach($path as $file) {
		if ($file['file_id'] != $id) {
			$back_id = $file['file_id'];
			break;
		}
	}

	$msg->addFeedback('FILE_DELETED');
	if ($back_id) {
		header('Location: revisions.php'.$owner_arg_prefix.'id='.$back_id);
	} else {
		header('Location: index.php'.$owner_arg_prefix);
	}
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$sql = "SELECT file_id, file_name, owner_type, owner_id, date, member_id FROM ".TABLE_PREFIX."files WHERE file_id=$id AND owner_type=$owner_type AND owner_id=$owner_id";
$result = mysql_query($sql, $db);
if (!$row = mysql_fetch_assoc($result)) {
	$msg->printErrors('FILE_NOT_EXIST');
} else {
	$hidden_vars = array('id' => $id, 'ot' => $owner_type, 'oid' => $owner_id);
	$msg->addConfirm(array('FILE_DELETE', '<li>'.$row['date'].' - '. $row['file_name'].' - '.get_login($row['member_id']).'</li>'), $hidden_vars);
	$msg->printConfirm();
}

require(AT_INCLUDE_PATH.'footer.inc.php');
?>