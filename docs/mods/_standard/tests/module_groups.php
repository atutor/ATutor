<?php

// create group
function tests_create_group($group_id) { }


// delete group
function tests_delete_group($group_id) {
	global $db;

	$sql	= "DELETE FROM ".TABLE_PREFIX."tests_groups WHERE group_id=$row[group_id]";
	$result2 = mysql_query($sql, $db);
}

?>