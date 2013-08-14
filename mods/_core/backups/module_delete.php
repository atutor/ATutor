<?php

function backups_delete($course) {
	global $db;

	$path = AT_BACKUP_DIR . $course . '/';
	clr_dir($path);

	$sql	= "DELETE FROM %sbackups WHERE course_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $course));
}

?>