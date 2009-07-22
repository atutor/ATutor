<?php

$sql = array();
$sql['assignments']  = 'SELECT assignment_id, title, assign_to, date_due, date_cutoff, multi_submit FROM '.TABLE_PREFIX.'assignments WHERE course_id=?';

function assignments_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row[0]  = $row[0]; // assignment_id
	$new_row[1]  = $course_id;
	$new_row[2]  = $row[1]; // title
	$new_row[3]  = $row[2]; // assign_to
	$new_row[4]  = $row[3]; // date_due
	$new_row[5]  = $row[4]; // date_cutoff
	$new_row[6]  = $row[5]; // multi_submit

	return $new_row;
}

?>