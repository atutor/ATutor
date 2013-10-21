<?php

function statistics_delete($course) {

	$sql = "DELETE FROM %scourse_stats WHERE course_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $course));
}

?>