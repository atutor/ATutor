<?php

// create group
function links_create_group($group_id) {
	global $db;

	$sql	= "INSERT INTO ".TABLE_PREFIX."links_categories VALUES (NULL, ".LINK_CAT_GROUP.", $group_id, '', 0)";
	$result = mysql_query($sql,$db);
}


// delete group
function links_delete_group($group_id) {
	global $db;

	$sql	= "SELECT cat_id FROM ".TABLE_PREFIX."links_categories WHERE owner_type=".LINK_CAT_GROUP." AND owner_id=$group_id";
	$result = mysql_query($sql, $db);

	while ($row = mysql_fetch_assoc($result)) {
		$sql = "DELETE FROM ".TABLE_PREFIX."links WHERE cat_id=$row[cat_id]";
		$result2 = mysql_query($sql, $db);
	}
	$sql	= "DELETE FROM ".TABLE_PREFIX."links_categories WHERE owner_type=".LINK_CAT_GROUP." AND owner_id=$group_id";
	$result = mysql_query($sql, $db);
}

?>