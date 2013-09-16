<?php

// create group
function file_storage_create_group($group_id) {

	$sql = "REPLACE INTO %sfile_storage_groups VALUES (%s)";
	queryDB($sql, array(TABLE_PREFIX, $group_id));
}


// delete group
function file_storage_delete_group($group_id) {

	$sql = "DELETE FROM %sfile_storage_groups WHERE group_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $group_id));
	
	require_once(AT_INCLUDE_PATH.'../mods/_standard/file_storage/file_storage.inc.php');
	fs_delete_workspace(WORKSPACE_GROUP, $group_id);
}


?>