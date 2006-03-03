<?php

function assignments_delete($course) {
	global $db;

	$sql = "DELETE FROM ".TABLE_PREFIX."assignments WHERE course_id=$course";
	mysql_query($sql, $db);
}

?>