<?php

// create group
function forums_create_group($group_id) {
	global $db;

	$sql	= "INSERT INTO ".TABLE_PREFIX."forums VALUES (NULL,'', '', 0, 0, NOW(), 0)";
	$result = mysql_query($sql,$db);

	$sql	= "INSERT INTO ".TABLE_PREFIX."forums_groups VALUES (LAST_INSERT_ID(),  $group_id)";
	$result = mysql_query($sql,$db);
}


// delete group
function forums_delete_group($group_id) {
	global $db;

	require(AT_INCLUDE_PATH.'../mods/_standard/forums/lib/forums.inc.php');
	
	$sql = "SELECT forum_id FROM ".TABLE_PREFIX."forums_groups WHERE group_id=$group_id";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		delete_forum($row['forum_id']);
	}

	$sql = "DELETE FROM ".TABLE_PREFIX."forums_groups WHERE group_id=$group_id";
	$result = mysql_query($sql, $db);
}

?>