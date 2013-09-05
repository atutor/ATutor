<?php

$sql = array();
$sql['news'] = 'SELECT date, formatting, title, body FROM '.TABLE_PREFIX.'news WHERE course_id=? ORDER BY news_id ASC';

// takes a CSV row and returns a valid SQL row (ie. all the correct fields).

function news_convert($row, $course_id, $table_id_map, $version) {
	static $member_id;

	if (!isset($member_id)) {
		$sql        = "SELECT member_id FROM %scourses WHERE course_id=%d";
		$member_row    = queryDB($sql, array(TABLE_PREFIX, $course_id), TRUE);
		$member_id  = $member_row['member_id'];
	}
	$new_row = array();
	$new_row[0] = 0;
	$new_row[1] = $course_id;
	$new_row[2] = $member_id;
	$new_row[3] = $row[0];
	$new_row[4] = $row[1];
	$new_row[5] = $row[2];
	$new_row[6] = $row[3];

	return $new_row;
}

?>