<?php

// create group
function links_create_group($group_id) {
	global $db;

	$sql	= "INSERT INTO ".TABLE_PREFIX."links_categories VALUES ('', ".LINK_CAT_GROUP.", $group_id, '', 0)";
	$result = mysql_query($sql,$db);
}


// delete group
function links_delete_group($group_id) {
	global $db;

	$sql	= "SELECT * FROM ".TABLE_PREFIX."links_categories WHERE owner_id=$group_id";
	$result = mysql_query($sql, $db);

	$total_links = 0;
	while ($row = mysql_fetch_array($result)) {
		$sql = "DELETE FROM ".TABLE_PREFIX."links WHERE cat_id=$row[0]";
		$result2 = mysql_query($sql, $db);
		$total_links += mysql_affected_rows($db);
	}
	$sql	= "DELETE FROM ".TABLE_PREFIX."links_categories WHERE owner_id=$group_id";
	$result = mysql_query($sql, $db);
}

?>