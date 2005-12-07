<?php

$sql = array();

$sql['resource_categories'] = 'SELECT CatID, CatName, CatParent FROM '.TABLE_PREFIX.'resource_categories WHERE course_id=? ORDER BY CatID ASC';

$sql['resource_links'] = 'SELECT L.CatID, Url, LinkName, Description, Approved, SubmitName, SubmitEmail, SubmitDate, hits FROM '.TABLE_PREFIX.'resource_links L, '.TABLE_PREFIX.'resource_categories C  WHERE C.course_id=? AND L.CatID=C.CatID ORDER BY LinkID ASC';



function resource_categories_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row[0] = $row[0];
	$new_row[1] = $course_id;
	$new_row[2] = $row[1];
	$new_row[3] = $row[2];

	return $new_row;

}

function resource_links_convert($row, $course_id, $table_id_map, $version) {
	static $i;

	$new_row = array();
	$new_row[0] = 0;
	$new_row[1] = $table_id_map['resource_categories'][$row[0]];
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