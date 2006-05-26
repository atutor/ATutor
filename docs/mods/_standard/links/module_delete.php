<?php

function links_delete($course) {
	global $db;

	// get groups
	$groups = array();
	$sql	= "SELECT G.group_id FROM ".TABLE_PREFIX."groups G INNER JOIN ".TABLE_PREFIX."groups_types USING (type_id) WHERE T.course_id=$course";
	$result = mysql_query($sql, $db);
	while ($group_row = mysql_fetch_assoc($result)) {
		$groups[] = $group_row['group_id'];
		mysql_query($sql, $db);
	}

	$sql	= "SELECT * FROM ".TABLE_PREFIX."links_categories WHERE (owner_id=$course AND owner_type=".LINK_CAT_COURSE.") OR (owner_id IN ($groups) AND owner_type=".LINK_CAT_GROUP.")";
	$result = mysql_query($sql, $db);
	$total_links = 0;

	while ($row = mysql_fetch_array($result)) {
		$sql = "DELETE FROM ".TABLE_PREFIX."links WHERE cat_id=$row[0]";
		$result2 = mysql_query($sql, $db);
		$total_links += mysql_affected_rows($db);
	}
	$sql	= "DELETE FROM ".TABLE_PREFIX."links_categories WHERE (owner_id=$course AND owner_type=".LINK_CAT_COURSE.") OR (owner_id IN ($groups) AND owner_type=".LINK_CAT_GROUP.")";
	$result = mysql_query($sql, $db);

}

?>