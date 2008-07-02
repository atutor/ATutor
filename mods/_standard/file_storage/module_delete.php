<?php

function file_storage_delete($course) {
	global $db;

	require(AT_INCLUDE_PATH.'lib/file_storage.inc.php');

	// delete course files:
	fs_delete_workspace(WORKSPACE_COURSE, $course);

	// delete private student files:
	$sql = "SELECT member_id FROM ".TABLE_PREFIX."course_enrollment WHERE course_id=$course";
	$result = mysql_query($sql, $db);
	while ($student_row = mysql_fetch_assoc($result)) {
		fs_delete_workspace(WORKSPACE_PERSONAL, $student_row['member_id']);
	}
}

?>