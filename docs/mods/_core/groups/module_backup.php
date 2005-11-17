<?php

$sql = array();
$sql['groups'] = 'SELECT title FROM '.TABLE_PREFIX.'groups WHERE course_id=? ORDER BY title';


function groups_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row[0] = 0;
	$new_row[1] = $course_id;
	$new_row[2] = $row[0];

	return $new_row;
}
?>