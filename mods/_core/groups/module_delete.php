<?php

function groups_delete($course) {
	global $db;

	$sql	= "SELECT G.group_id FROM %sgroups G INNER JOIN %sgroups_types T USING (type_id) WHERE T.course_id=%d";
	$rows_groups = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $course));
	
	foreach($rows_groups as $row){
	
		$sql	= "DELETE FROM %sgroups_members WHERE group_id=%d";
		$result2 = queryDB($sql, array(TABLE_PREFIX, $row['group_id']));
		
		// -- remove assoc between tests and groups:
		$sql	= "DELETE FROM %stests_groups WHERE group_id=%d";
		$result2 = queryDB($sql, array(TABLE_PREFIX, $row['group_id']));
		
		$sql	= "DELETE FROM %sgroups WHERE group_id=%d";
		$result2 = queryDB($sql, array(TABLE_PREFIX, $row['group_id']));
	}

	$sql	= "DELETE FROM %sgroups_types WHERE course_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $course));
}

?>