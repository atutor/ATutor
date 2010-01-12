<?php

function assignments_delete($course) {
	global $db;
	
	require_once(AT_INCLUDE_PATH.'../mods/_standard/file_storge/lib/file_storage.inc.php');

	$sql	= "SELECT assignment_id FROM ".TABLE_PREFIX."assignments WHERE course_id=$course";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) 
	{
		fs_delete_workspace(WORKSPACE_ASSIGNMENT, $row['assignment_id']);
	}

	// delete assignment folders/files from file storage
	$sql = "DELETE FROM ".TABLE_PREFIX."assignments WHERE course_id=$course";
	mysql_query($sql, $db);
}

?>