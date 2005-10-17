<?php

function hello_world_delete($course) {
	global $db;

	$sql = "DELETE FROM ".TABLE_PREFIX."hello_world WHERE course_id=$course";
	mysql_query($sql, $db);

	$path = AT_CONTENT_DIR .'hello_world/' . $course .'/';

	clr_dir($path);
}

?>