<?php

// create group
function file_storage_create_group($group_id) {
	global $db;

	$sql = "REPLACE INTO ".TABLE_PREFIX."file_storage_groups VALUES ($group_id)";
	mysql_query($sql, $db);
}


// delete group
function file_storage_delete_group($group_id) {
	global $db;

	$sql = "DELETE FROM ".TABLE_PREFIX."file_storage_groups WHERE group_id=$group_id";
	$result = mysql_query($sql, $db);

	require(AT_INCLUDE_PATH.'lib/file_storage.inc.php');
	fs_delete_workspace(WORKSPACE_GROUP, $group_id);
}


?>