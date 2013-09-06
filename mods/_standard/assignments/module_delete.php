<?php

function assignments_delete($course) {
	global $db;
	
	require_once(AT_INCLUDE_PATH.'../mods/_standard/file_storage/file_storage.inc.php');

	$sql	= "SELECT assignment_id FROM %sassignments WHERE course_id=%d";
	$rows_assignments = queryDB($sql, array(TABLE_PREFIX, $course));
	
	/////
	// NOT SURE WHY THIS IS HERE
	foreach($rows_assignments as $row){
		fs_delete_workspace(WORKSPACE_ASSIGNMENT, $row['assignment_id']);
	}

	// delete assignment folders/files from file storage
	$sql = "DELETE FROM %sassignments WHERE course_id=%d";
	queryDB($sql, array(TABLE_PREFIX, $course));
}

?>