<?php

$sql = array();

$sql['content'] = 'SELECT content_id, content_parent_id, ordering, last_modified, revision, formatting, release_date, keywords, content_path, title, text, head, use_customized_head, test_message, allow_test_export, content_type FROM '.TABLE_PREFIX.'content WHERE course_id=? ORDER BY content_parent_id, ordering';

$sql['primary_resources'] = 'SELECT P.primary_resource_id, P.content_id, P.resource, P.language_code FROM '.TABLE_PREFIX.'primary_resources P, '.TABLE_PREFIX.'content C WHERE C.course_id=? AND P.content_id=C.content_id ORDER BY P.content_id ASC';

$sql['primary_resources'] = 'SELECT P.primary_resource_id, P.content_id, P.resource, P.language_code FROM '.TABLE_PREFIX.'primary_resources P, '.TABLE_PREFIX.'content C WHERE C.course_id=? AND P.content_id=C.content_id ORDER BY P.content_id ASC';

$sql['primary_resources_types'] = 'SELECT PRT.primary_resource_id, PRT.type_id FROM '.TABLE_PREFIX.'primary_resources_types PRT, '.TABLE_PREFIX.'primary_resources P, '.TABLE_PREFIX.'content C WHERE C.course_id=? AND C.content_id=P.content_id AND P.primary_resource_id = PRT.primary_resource_id ORDER BY PRT.primary_resource_id ASC';

$sql['secondary_resources'] = 'SELECT S.secondary_resource_id, S.primary_resource_id, S.secondary_resource, S.language_code FROM '.TABLE_PREFIX.'secondary_resources S, '.TABLE_PREFIX.'primary_resources P, '.TABLE_PREFIX.'content C WHERE C.course_id=? AND C.content_id=P.content_id AND P.primary_resource_id = S.primary_resource_id ORDER BY S.primary_resource_id ASC';

$sql['secondary_resources_types'] = 'SELECT SRT.secondary_resource_id, SRT.type_id FROM '.TABLE_PREFIX.'secondary_resources_types SRT, '.TABLE_PREFIX.'secondary_resources S, '.TABLE_PREFIX.'primary_resources P, '.TABLE_PREFIX.'content C WHERE C.course_id=? AND C.content_id=P.content_id AND P.primary_resource_id = S.primary_resource_id AND S.secondary_resource_id = SRT.secondary_resource_id ORDER BY SRT.secondary_resource_id ASC';

function related_content_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row[0] = $table_id_map['content'][$row[0]];
	$new_row[1] = $table_id_map['content'][$row[1]];

	return $new_row;
}

function primary_resources_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row = $row;
	
	$new_row[1] = $table_id_map['content'][$row[1]];

	return $new_row;
}

function primary_resources_types_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row = $row;
	
	$new_row[0] = $table_id_map['primary_resources'][$row[0]];

	return $new_row;
}

function secondary_resources_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row = $row;
	
	$new_row[1] = $table_id_map['primary_resources'][$row[1]];

	return $new_row;
}

function secondary_resources_types_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row = $row;
	
	$new_row[0] = $table_id_map['secondary_resources'][$row[0]];

	return $new_row;
}

function content_convert($row, $course_id, $table_id_map, $version) {
	static $order;
	if (!isset($order)) {
		global $db;
		$sql	 = 'SELECT MAX(ordering) AS ordering FROM '.TABLE_PREFIX.'content WHERE content_parent_id=0 AND course_id='.$course_id;
		$result  = mysql_query($sql, $db);
		$tmp_row = mysql_fetch_assoc($result);
		$order   = $tmp_row['ordering'] + 1;
	}

	$new_row = array();
	$new_row[0] = $row[0];
	$new_row[1] = $course_id;
	if ($row[1] == 0) {
		$new_row[2] = 0;
		$new_row[3] = $order;
		$order++;
	} else {
		$new_row[2] = $table_id_map['content'][$row[1]];
		$new_row[3] = $row[2];
	}

	$new_row[4] = $row[3];
	$new_row[5] = $row[4];
	$new_row[6] = $row[5];
	$new_row[7] = $row[6];
	$new_row[8] = $row[7];
	$new_row[9] = $row[8];
	$new_row[10] = $row[9];
	$new_row[11] = $row[10];
	$new_row[12] = $row[11];
	$new_row[13] = $row[12];
	$new_row[14] = $row[13];	// test_message
	$new_row[15] = $row[14];	// allow_test_export
	$new_row[16] = $row[15];	// content_type
	
	return $new_row;
}
?>