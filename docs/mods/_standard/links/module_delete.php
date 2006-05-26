<?php

function links_delete($course) {
	global $db;

	// get groups
	$groups = '';
	$sql	= "SELECT G.group_id FROM ".TABLE_PREFIX."groups G INNER JOIN ".TABLE_PREFIX."groups_types USING (type_id) WHERE T.course_id=$course";
	$result = mysql_query($sql, $db);
	while ($group_row = mysql_fetch_assoc($result)) {
		$groups .= $group_row['group_id'].', ';
		mysql_query($sql, $db);
	}
	$groups = substr($groups, 0, -2);
	$sql	= "SELECT cat_id FROM ".TABLE_PREFIX."links_categories WHERE (owner_id=$course AND owner_type=".LINK_CAT_COURSE.") OR (owner_id IN ($groups) AND owner_type=".LINK_CAT_GROUP.")";
	$result = mysql_query($sql, $db);

	while ($row = mysql_fetch_assoc($result)) {
		$sql = "DELETE FROM ".TABLE_PREFIX."links WHERE cat_id=$row[cat_id]";
		$result2 = mysql_query($sql, $db);
	}
	$sql	= "DELETE FROM ".TABLE_PREFIX."links_categories WHERE (owner_id=$course AND owner_type=".LINK_CAT_COURSE.") OR (owner_id IN ($groups) AND owner_type=".LINK_CAT_GROUP.")";
	$result = mysql_query($sql, $db);

}

?>