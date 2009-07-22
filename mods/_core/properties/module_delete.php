<?php

function properties_delete($course) {
	global $db;

	$sql	= "DELETE FROM ".TABLE_PREFIX."course_access WHERE course_id=$course";
	$result = mysql_query($sql, $db);

}

?>