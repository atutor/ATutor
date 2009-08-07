<?php
/* each table to be backed up. includes the sql entry and fields */

$dirs = array();
$dirs['hello_world/'] = AT_CONTENT_DIR . 'hello_world' . DIRECTORY_SEPARATOR;

$sql = array();
$sql['hello_world']  = 'SELECT value FROM '.TABLE_PREFIX.'hello_world WHERE course_id=?';

function hello_world_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row[0]  = $course_id;
	$new_row[1]  = $row[0];

	return $new_row;
}

?>