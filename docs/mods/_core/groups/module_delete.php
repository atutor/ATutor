<?php

function groups_delete($course) {
	global $db;

	$sql	= "SELECT G.group_id FROM ".TABLE_PREFIX."groups G INNER JOIN ".TABLE_PREFIX."groups_types T USING (type_id) WHERE T.course_id=$course";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$sql	= "DELETE FROM ".TABLE_PREFIX."groups_members WHERE group_id=$row[group_id]";
		$result2 = mysql_query($sql, $db);

		$sql	= "DELETE FROM ".TABLE_PREFIX."tests_groups WHERE group_id=$row[group_id]";
		$result2 = mysql_query($sql, $db);

		$sql	= "DELETE FROM ".TABLE_PREFIX."groups WHERE group_id=$row[group_id]";
		$result = mysql_query($sql, $db);
	}

	$sql	= "DELETE FROM ".TABLE_PREFIX."groups_types WHERE course_id=$course";
	$result = mysql_query($sql, $db);

	// -- remove assoc between tests and groups:

}

?>