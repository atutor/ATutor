<?php
/*******
 * this function named [module_name]_delete is called whenever a course content is deleted
 * which includes when restoring a backup with override set, or when deleting an entire course.
 * the function must delete all module-specific material associated with this course.
 * $course is the ID of the course to delete.
 */

function gameme_delete($course) {
	global $db;

	// delete GameMe course settings
	$sql = "DELETE FROM %sgm_badges WHERE course_id=%d";
	queryDB($sql, array(TABLE_PREFIX, $course));
	
	$sql = "DELETE FROM %sgm_events WHERE course_id=%d";
	queryDB($sql, array(TABLE_PREFIX, $course));
	
	$sql = "DELETE FROM %sgm_levels WHERE course_id=%d";
	queryDB($sql, array(TABLE_PREFIX, $course));
	
	$sql = "DELETE FROM %sgm_options WHERE course_id=%d";
	queryDB($sql, array(TABLE_PREFIX, $course));
	
	
    // Delete GameMe user data for the course
	$sql = "DELETE FROM %sgm_user_badges WHERE course_id=%d";
	queryDB($sql, array(TABLE_PREFIX, $course));
	
	$sql = "DELETE FROM %sgm_user_events WHERE course_id=%d";
	queryDB($sql, array(TABLE_PREFIX, $course));
	
	$sql = "DELETE FROM %sgm_user_alerts WHERE course_id=%d";
	queryDB($sql, array(TABLE_PREFIX, $course));
	
	$sql = "DELETE FROM %sgm_user_logs WHERE course_id=%d";
	queryDB($sql, array(TABLE_PREFIX, $course));

	$sql = "DELETE FROM %sgm_user_scores WHERE course_id=%d";
	queryDB($sql, array(TABLE_PREFIX, $course));
	
	// delete game course files
	$path = AT_CONTENT_DIR .$course.'/gameme/';
	clr_dir($path);
}

?>