<?php

$sql = array();
$sql['course_stats'] = 'SELECT login_date, guests, members FROM '.TABLE_PREFIX.'course_stats WHERE course_id=?';


function course_stats_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row[0] = $course_id;
	$new_row[1] = $row[0];
	$new_row[2] = $row[1];
	$new_row[3] = $row[2];

	return $new_row;
}

?>