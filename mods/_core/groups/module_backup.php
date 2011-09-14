<?php

$sql = array();
$sql['groups_types'] = 'SELECT type_id, title FROM '.TABLE_PREFIX.'groups_types WHERE course_id=? ORDER BY title';

$sql['groups'] = 'SELECT G.type_id, G.title, G.description, G.modules FROM '.TABLE_PREFIX.'groups G INNER JOIN '.TABLE_PREFIX.'groups_types T USING (type_id) WHERE T.course_id=? ORDER BY T.type_id, G.title';


function groups_types_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row[0] = $row[0];
	$new_row[1] = $course_id;
	$new_row[2] = $row[1];

	return $new_row;
}

function groups_convert($row, $course_id, $table_id_map, $version) {
	if (version_compare($version, '1.5.3', '<')) {
		// groups prior to 1.5.3 are not compatible (due to the group types).
		// backwards compatibility breaks prior to 1.5.3
		return array();
	}
	$new_row = array();
	$new_row[0] = 0;
	$new_row[1] = $table_id_map['groups_types'][$row[0]];
	$new_row[2] = $row[1];
	$new_row[3] = $row[2];
	$new_row[4] = $row[3];

	return $new_row;
}
?>