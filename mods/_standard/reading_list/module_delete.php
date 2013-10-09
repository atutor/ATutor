<?php

function reading_list_delete($course) {	
	$sql = "DELETE FROM %sreading_list WHERE course_id=%d";
	queryDB($sql, array(TABLE_PREFIX, $course));

	$sql = "DELETE FROM %sexternal_resources WHERE course_id=%d";
	queryDB($sql, array(TABLE_PREFIX, $course));
}

?>