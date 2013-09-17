<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_standard/file_storage/file_storage.inc.php');

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
	header('Location: '.url_rewrite('mods/_standard/file_storage/revisions.php'.$owner_arg_prefix.'id='.$first['file_id'], AT_PRETTY_URL_IS_HEADER));
	exit;
} else if (isset($_POST['submit_yes'])) {
	$path = fs_get_revisions($id, $owner_type, $owner_id);

	// set the new parent //
	$sql = "SELECT parent_file_id, owner_type, owner_id, folder_id FROM %sfiles WHERE file_id=%d AND owner_type=%d AND owner_id=%d";
	$row = queryDB($sql, array(TABLE_PREFIX, $id, $owner_type, $owner_id), TRUE);

	$sql = "UPDATE %sfiles SET parent_file_id=%d, date=date WHERE parent_file_id=%d AND owner_type=%d AND owner_id=%d";
	queryDB($sql, array(TABLE_PREFIX, $row['parent_file_id'], $id, $owner_type, $owner_id));
		
	$sql = "UPDATE %sfiles SET num_revisions=num_revisions-1, date=date WHERE file_id>%d AND owner_type=%d AND owner_id=%d AND folder_id=%d";
	queryDB($sql, array(TABLE_PREFIX, $id, $row['owner_type'], $row['owner_id'], $row['folder_id']));

	$sql = "DELETE FROM %sfiles WHERE file_id=%d AND owner_type=%d AND owner_id=%d";
	queryDB($sql, array(TABLE_PREFIX, $id, $owner_type, $owner_id));

	$sql = "DELETE FROM %sfiles_comments WHERE file_id=%d";
	queryDB($sql, array(TABLE_PREFIX, $id));
	
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

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	if ($back_id) {
		header('Location: '.url_rewrite('mods/_standard/file_storage/revisions.php'.$owner_arg_prefix.'id='.$back_id, AT_PRETTY_URL_IS_HEADER));
	} else {
		header('Location: '.url_rewrite('mods/_standard/file_storage/index.php'.$owner_arg_prefix, AT_PRETTY_URL_IS_HEADER));
	}
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$sql = "SELECT file_id, file_name, owner_type, owner_id, date, member_id FROM %sfiles WHERE file_id=%d AND owner_type=%d AND owner_id=%d";
$row = queryDB($sql, array(TABLE_PREFIX, $id, $owner_type, $owner_id), TRUE);

if(count($row) == 0){
	$msg->printErrors('FILE_NOT_EXIST');
} else {
	$hidden_vars = array('id' => $id, 'ot' => $owner_type, 'oid' => $owner_id);
	$msg->addConfirm(array('FILE_DELETE', '<li>'.$row['date'].' - '. $row['file_name'].' - '.get_display_name($row['member_id']).'</li>'), $hidden_vars);
	$msg->printConfirm();
}

require(AT_INCLUDE_PATH.'footer.inc.php');
?>