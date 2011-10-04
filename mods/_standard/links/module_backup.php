<?php

$sql = array();

$sql['links_categories'] = 'SELECT cat_id, name, parent_id FROM '.TABLE_PREFIX.'links_categories WHERE owner_id=? AND owner_type='.LINK_CAT_COURSE.' ORDER BY cat_id ASC';

$sql['links'] = 'SELECT L.cat_id, Url, LinkName, Description, Approved, SubmitName, SubmitEmail, SubmitDate, hits FROM '.TABLE_PREFIX.'links L INNER JOIN '.TABLE_PREFIX.'links_categories C  USING (cat_id) WHERE C.owner_id=? AND C.owner_type='.LINK_CAT_COURSE.' ORDER BY link_id ASC';


function links_categories_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row[0] = $row[0];
	$new_row[1] = LINK_CAT_COURSE;
	$new_row[2] = $course_id;
	$new_row[3] = $row[1];
	$new_row[4] = $row[2];

	return $new_row;
}

function links_convert($row, $course_id, $table_id_map, $version) {
	static $i;

	$new_row = array();
	$new_row[0] = 0;
	$new_row[1] = $table_id_map['links_categories'][$row[0]];
	$new_row[2] = $row[1];
	$new_row[3] = $row[2];
	$new_row[4] = $row[3];
	$new_row[5] = $row[4];
	$new_row[6] = $row[5];
	$new_row[7] = $row[6];
	$new_row[8] = $row[7];
	$new_row[9] = $row[8];

	return $new_row;
}

?>