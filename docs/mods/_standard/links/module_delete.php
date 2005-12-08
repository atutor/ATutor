<?php

function links_delete($course) {
	global $db;

	$sql	= "SELECT * FROM ".TABLE_PREFIX."resource_categories WHERE course_id=$course";
	$result = mysql_query($sql, $db);
	$total_links = 0;
	while ($row = mysql_fetch_array($result)) {
		$sql = "DELETE FROM ".TABLE_PREFIX."resource_links WHERE CatID=$row[0]";
		$result2 = mysql_query($sql, $db);
		$total_links += mysql_affected_rows($db);
	}
	$sql	= "DELETE FROM ".TABLE_PREFIX."resource_categories WHERE course_id=$course";
	$result = mysql_query($sql, $db);

}

?>