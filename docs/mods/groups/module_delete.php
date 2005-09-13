<?php

function groups_delete($course) {
	global $db;

	$sql	= "SELECT group_id FROM ".TABLE_PREFIX."groups WHERE course_id=$course";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$sql	= "DELETE FROM ".TABLE_PREFIX."groups_members WHERE group_id=$row[group_id]";
		$result2 = mysql_query($sql, $db);

		$sql	= "DELETE FROM ".TABLE_PREFIX."tests_groups WHERE group_id=$row[group_id]";
		$result2 = mysql_query($sql, $db);
	}
	$sql	= "DELETE FROM ".TABLE_PREFIX."groups WHERE course_id=$course";
	$result = mysql_query($sql, $db);

	// -- remove assoc between tests and groups:

}

?>