<?php

// create group
function blogs_create_group($group_id) {

	// nothing has to happen when a group is created
}


// delete group
function blogs_delete_group($group_id) {
	// deleting a group involves deleting all the blog entries and comments for that group

	// for each entry, delete the comments
	$sql = "SELECT post_id FROM %sblog_posts WHERE owner_type=%d AND owner_id=%d";
	$rows_posts = queryDB($sql, array(TABLE_PREFIX, BLOGS_GROUP, $group_id));
	
	foreach($rows_posts as $row){
		$sql = "DELETE FROM %sblog_posts_comments WHERE post_id=%d";
		queryDB($sql, array(TABLE_PREFIX, $row['post_id']));
	}

	$sql = "DELETE FROM %sblog_posts WHERE owner_type=%d AND owner_id=%d";
	queryDB($sql, array(TABLE_PREFIX, BLOG_GROUP, $group_id));
}

?>