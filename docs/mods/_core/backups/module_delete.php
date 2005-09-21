<?php

function backups_delete($course) {
	global $db;

	$path = AT_BACKUP_DIR . $course . '/';
	clr_dir($path);

	$sql	= "DELETE FROM ".TABLE_PREFIX."backups WHERE course_id=$course";
	$result = mysql_query($sql, $db);

}

?>