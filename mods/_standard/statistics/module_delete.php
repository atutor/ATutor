<?php

function statistics_delete($course) {
	global $db;

	$sql = "DELETE FROM ".TABLE_PREFIX."course_stats WHERE course_id=$course";
	$result = mysql_query($sql, $db);

}

?>