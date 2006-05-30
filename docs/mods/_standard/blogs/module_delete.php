<?php

function blogs_delete($course) {
	global $db;

	// delete group blogs
	$sql	= "SELECT G.group_id FROM ".TABLE_PREFIX."groups G INNER JOIN ".TABLE_PREFIX."groups_types T USING (type_id) WHERE T.course_id=$course";
	$result = mysql_query($sql, $db);
	while ($group_row = mysql_fetch_assoc($result)) {
		// for each entry, delete the comments
		$sql = "SELECT post_id FROM ".TABLE_PREFIX."blog_posts WHERE owner_type=".BLOG_GROUP." AND owner_id=$row[group_id]";
		$result2 = mysql_query($sql, $db);
		while ($row = mysql_fetch_assoc($result2)) {
			$sql = "DELETE FROM ".TABLE_PREFIX."blog_posts_comments WHERE post_id=$row[post_id]";
			mysql_query($sql, $db);
		}

		$sql = "DELETE FROM ".TABLE_PREFIX."blog_posts WHERE owner_type=".BLOG_GROUP." AND owner_id=$row[group_id]";
		mysql_query($sql, $db);
	}
}

?>