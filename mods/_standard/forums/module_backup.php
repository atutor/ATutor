<?php

$sql = array();
$sql['forums']  = 'SELECT F.forum_id, F.title, F.description, C.forum_id FROM '.TABLE_PREFIX.'forums F, '.TABLE_PREFIX.'forums_courses C WHERE C.forum_id=F.forum_id';

$sql['forums_courses']  = 'SELECT forum_id, course_id FROM '.TABLE_PREFIX.'forums_courses WHERE course_id=?';

function forums_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row[0]  = $row[0]; // forum_id
	$new_row[1]  = $row[1]; // title
	$new_row[2]  = $row[2]; // description
	$new_row[3]  = 0;           // num_topics
	$new_row[4]  = 0;           // num_posts
	$new_row[5]  = 0;           // last_post
	$new_row[6]  = 0;           // mins_to_edit
	return $new_row;
}
function forums_courses_convert($row, $course_id, $table_id_map, $version) {
    global $this_forum_id;
	$new_row = array();
	$new_row[0]  = $table_id_map['forums'][$row[0]];
	$new_row[1]  = $course_id;

	return $new_row;
}
?>