<?php

// create group
function tests_create_group($group_id) { }


// delete group
function tests_delete_group($group_id) {
	$sql	= "DELETE FROM %stests_groups WHERE group_id=%d";
	$result2 = queryDB($sql, array(TABLE_PREFIX, $row['group_id']));
}

?>