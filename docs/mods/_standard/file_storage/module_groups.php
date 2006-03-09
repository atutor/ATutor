<?php

// create group
function file_storage_create_group($group_id) { }


// delete group
function file_storage_delete_group($group_id) {
	global $db;

	require(AT_INCLUDE_PATH.'lib/file_storage.inc.php');

	$sql = "SELECT folder_id, owner_type, owner_id FROM ".TABLE_PREFIX."folders WHERE owner_type=".WORKSPACE_GROUP." AND owner_id=$group_id AND parent_folder_id=0";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		fs_delete_folder($row['folder_id'], $row['owner_type'], $row['owner_id']);
	}


	$sql = "SELECT file_id, owner_type, owner_id FROM ".TABLE_PREFIX."files WHERE owner_type=".WORKSPACE_GROUP." AND owner_id=$group_id";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		fs_delete_file($row['file_id'], $row['owner_type'], $row['owner_id']);
	}
}

?>