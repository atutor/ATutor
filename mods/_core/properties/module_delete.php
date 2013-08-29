<?php

function properties_delete($course) {

	$sql	= "DELETE FROM %scourse_access WHERE course_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $course));
}

?>