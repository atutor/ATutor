<?php

// create group
function blogs_create_group($group_id) {

	// nothing has to happen when a group is created
}


// delete group
function blogs_delete_group($group_id) {
	global $db;

	// deleting a group involves deleting all the blog entries for that group

	// for each entry, delete the comments
	$sql = "SELECT post_id FROM ".TABLE_PREFIX."blog_posts WHERE owner_type=".BLOG_GROUP." AND owner_id=$group_id";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$sql = "DELETE FROM ".TABLE_PREFIX."blog_posts_comments WHERE post_id=$row[post_id]";
		mysql_query($sql, $db);
	}

	$sql = "DELETE FROM ".TABLE_PREFIX."blog_posts WHERE owner_type=".BLOG_GROUP." AND owner_id=$group_id";
	mysql_query($sql, $db);
}

?>