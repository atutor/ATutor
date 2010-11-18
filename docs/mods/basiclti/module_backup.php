<?php
/* each table to be backed up. includes the sql entry and fields */

$dirs = array();
$dirs['basiclti/'] = AT_CONTENT_DIR . 'basiclti' . DIRECTORY_SEPARATOR;

$sql = array();
$sql['basiclti']  = 'SELECT value FROM '.TABLE_PREFIX.'basiclti WHERE course_id=?';

function basiclti_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row[0]  = $course_id;
	$new_row[1]  = $row[0];

	return $new_row;
}

?>
