<?php

$sql = array();
$sql['external_resources']  = 'SELECT resource_id, type, title, author, publisher, date, comments, id, url FROM '.TABLE_PREFIX.'external_resources WHERE course_id=?';

$sql['reading_list']  = 'SELECT resource_id, required, date_start, date_end, comment FROM '.TABLE_PREFIX.'reading_list WHERE course_id=?';

function external_resources_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row[0]  = $row[0]; // resource_id
	$new_row[1]  = $course_id;
	$new_row[2]  = $row[1]; // type
	$new_row[3]  = $row[2]; // title
	$new_row[4]  = $row[3]; // author
	$new_row[5]  = $row[4]; // publisher
	$new_row[6]  = $row[5]; // date
	$new_row[7]  = $row[6]; // comments
	$new_row[8]  = $row[7]; // id
	$new_row[9]  = $row[8]; // url

	return $new_row;
}

function reading_list_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row[0]  = 0; // reading_id
	$new_row[1]  = $course_id;
	$new_row[2]  = $table_id_map['external_resources'][$row[0]]; // resource_id
	$new_row[3]  = $row[1]; // required
	$new_row[4]  = $row[2]; // date_start
	$new_row[5]  = $row[3]; // date_end
	$new_row[6]  = $row[4]; // comment

	return $new_row;
}
?>