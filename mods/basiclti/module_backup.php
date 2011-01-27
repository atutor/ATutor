<?php
/* each table to be backed up. includes the sql entry and fields */

//$dirs = array();
//$dirs['basiclti/'] = AT_CONTENT_DIR . 'basiclti' . DIRECTORY_SEPARATOR;

$sql = array();
// Get the LTI tools that are used in the content of this course
$sql['basiclti_content']  = 'SELECT * FROM '.TABLE_PREFIX.'basiclti_content WHERE course_id=?';
//Get the LTI tools created specifically for this course
$sql['basiclti_tools']  = 'SELECT * FROM '.TABLE_PREFIX.'basiclti_tools WHERE course_id=?';

function basiclti_content_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row[0]  = $row[0];
	$new_row[1]  = $table_id_map['content'][$row[1]];
	$new_row[2] =  $course_id;
	$new_row[3]  = $row[3];          //
	$new_row[4]  = $row[4];          //
	$new_row[5]  = $row[5];          //
	$new_row[6]  = $row[6];          //
	$new_row[7]  = $row[7];          //
	$new_row[8]  = $row[8];          //
	$new_row[9]  = $row[9];          //
	$new_row[10]  = $row[10];          //
	$new_row[11]  = $row[11];          //
	$new_row[12]  = $row[12];          //
	$new_row[13]  = $row[13];          //
	$new_row[14]  = $row[14];          //
	$new_row[15]  = $row[15];          //
	$new_row[16]  = $row[16];          //
	$new_row[17]  = $row[17];          //
	$new_row[18]  = $row[18];          //

	return $new_row;
}
function basiclti_tools_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row[0]  = $row[0];          //
	$new_row[1]  = $row[1];          //
	$new_row[2]  = $course_id;
	$new_row[3]  = $row[3];          //
	$new_row[4]  = $row[4];          //
	$new_row[5]  = $row[5];          //
	$new_row[6]  = $row[6];          //
	$new_row[7]  = $row[7];          //
	$new_row[8]  = $row[8];          //
	$new_row[9]  = $row[9];          //
	$new_row[10]  = $row[10];          //
	$new_row[11]  = $row[11];          //
	$new_row[12]  = $row[12];          //
	$new_row[13]  = $row[13];          //
	$new_row[14]  = $row[14];          //
	$new_row[15]  = $row[15];          //
	$new_row[16]  = $row[16];          //
	$new_row[17]  = $row[17];          //
	$new_row[18]  = $row[18];          //
	$new_row[19]  = $row[19];          //
	$new_row[20]  = $row[20];          //
	$new_row[21]  = $row[21];          //
	$new_row[22]  = $row[22];          //
	$new_row[23]  = $row[23];          //
	$new_row[24]  = $row[24];          //

	return $new_row;
}
?>
