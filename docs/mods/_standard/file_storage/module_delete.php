<?php

function file_storage_delete($course) {
	global $db;

	require(AT_INCLUDE_PATH.'lib/file_storage.inc.php');

	// delete course files:
	$sql = "SELECT folder_id, owner_type, owner_id FROM ".TABLE_PREFIX."folders WHERE owner_type=".WORKSPACE_COURSE." AND owner_id=$course AND parent_folder_id=0";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		fs_delete_folder($row['folder_id'], $row['owner_type'], $row['owner_id']);
	}

	$sql = "SELECT file_id, owner_type, owner_id FROM ".TABLE_PREFIX."files WHERE owner_type=".WORKSPACE_COURSE." AND owner_id=$course";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		fs_delete_file($row['file_id'], $row['owner_type'], $row['owner_id']);
	}

	// delete private student files:
	$sql = "SELECT member_id FROM ".TABLE_PREFIX."course_enrollment WHERE course_id=$course";
	$result = mysql_query($sql, $db);
	while ($student_row = mysql_fetch_assoc($result)) {
		$sql = "SELECT folder_id, owner_type, owner_id FROM ".TABLE_PREFIX."folders WHERE owner_type=".WORKSPACE_PERSONAL." AND owner_id=$student_row[member_id] AND parent_folder_id=0";
		$result = mysql_query($sql, $db);
		while ($row = mysql_fetch_assoc($result)) {
			fs_delete_folder($row['folder_id'], $row['owner_type'], $row['owner_id']);
		}

		$sql = "SELECT file_id, owner_type, owner_id FROM ".TABLE_PREFIX."files WHERE owner_type=".WORKSPACE_PERSONAL." AND owner_id=$student_row[member_id]";
		$result = mysql_query($sql, $db);
		while ($row = mysql_fetch_assoc($result)) {
			fs_delete_file($row['file_id'], $row['owner_type'], $row['owner_id']);
		}
	}

	// delete group files
	$sql	= "SELECT G.group_id FROM ".TABLE_PREFIX."groups G INNER JOIN ".TABLE_PREFIX."groups_types USING (type_id) WHERE T.course_id=$course";
	$result = mysql_query($sql, $db);
	while ($group_row = mysql_fetch_assoc($result)) {
		$sql = "SELECT folder_id, owner_type, owner_id FROM ".TABLE_PREFIX."folders WHERE owner_type=".WORKSPACE_GROUP." AND owner_id=$group_row[group_id] AND parent_folder_id=0";
		$result = mysql_query($sql, $db);
		while ($row = mysql_fetch_assoc($result)) {
			fs_delete_folder($row['folder_id'], $row['owner_type'], $row['owner_id']);
		}

		$sql = "SELECT file_id, owner_type, owner_id FROM ".TABLE_PREFIX."files WHERE owner_type=".WORKSPACE_GROUP." AND owner_id=$group_row[member_id]";
		$result = mysql_query($sql, $db);
		while ($row = mysql_fetch_assoc($result)) {
			fs_delete_file($row['file_id'], $row['owner_type'], $row['owner_id']);
		}
	}
}

?>