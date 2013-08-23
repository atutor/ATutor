<?php

function enrolment_delete($course) {
	global $db;
	$sql	= "DELETE FROM %scourse_enrollment WHERE course_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $course));

	$sql	= "DELETE FROM %sauto_enroll_courses WHERE course_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $course));
}

?>