<?php

function reading_list_delete($course) {
	global $db;

	$sql = "DELETE FROM ".TABLE_PREFIX."reading_list WHERE course_id=$course";
	mysql_query($sql, $db);

	$sql = "DELETE FROM ".TABLE_PREFIX."external_resources WHERE course_id=$course";
	mysql_query($sql, $db);
}

?>