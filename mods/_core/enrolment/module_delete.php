<?php

function enrolment_delete($course) {
	global $db;

	$sql	= "DELETE FROM ".TABLE_PREFIX."course_enrollment WHERE course_id=$course";
	$result = mysql_query($sql, $db);

	$sql	= "DELETE FROM ".TABLE_PREFIX."auto_enroll_courses WHERE course_id=$course";
	$result = mysql_query($sql, $db);
}

?>