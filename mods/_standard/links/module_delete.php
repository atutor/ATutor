<?php

function links_delete($course) {
	global $db;

	$sql	= "SELECT cat_id FROM ".TABLE_PREFIX."links_categories WHERE owner_id=$course AND owner_type=".LINK_CAT_COURSE;
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$sql = "DELETE FROM ".TABLE_PREFIX."links WHERE cat_id=$row[cat_id]";
		mysql_query($sql, $db);
	}
	$sql = "DELETE FROM ".TABLE_PREFIX."links_categories WHERE owner_id=$course AND owner_type=".LINK_CAT_COURSE;
	mysql_query($sql, $db);
}

?>