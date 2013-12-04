<?php

function file_storage_delete($course) {
	global $db;

	require(AT_INCLUDE_PATH.'../mods/_standard/file_storage/file_storage.inc.php');

	// delete course files:
	fs_delete_workspace(WORKSPACE_COURSE, $course);

}

?>