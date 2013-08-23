<?php

function glossary_delete($course) {
	global $db;

	$sql	= "DELETE FROM %sglossary WHERE course_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $course));
}

?>