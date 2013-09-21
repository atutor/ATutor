<?php

// create group
function forums_create_group($group_id) {

	$sql	= "INSERT INTO %sforums VALUES (NULL,'', '', 0, 0, NOW(), 0)";
	$result = queryDB($sql, array(TABLE_PREFIX));

	$sql	= "INSERT INTO %sforums_groups VALUES (LAST_INSERT_ID(), %d)";
	$result = queryDB($sql,array(TABLE_PREFIX, $group_id));
}


// delete group
function forums_delete_group($group_id) {

	require_once(AT_INCLUDE_PATH.'../mods/_standard/forums/lib/forums.inc.php');

	$sql = "SELECT forum_id FROM %sforums_groups WHERE group_id=%d";
	$rows_gforums = queryDB($sql, array(TABLE_PREFIX, $group_id));
		
	foreach($rows_gforums as $row){
		delete_forum($row['forum_id']);
	}

	$sql = "DELETE FROM %sforums_groups WHERE group_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $group_id));
}

?>