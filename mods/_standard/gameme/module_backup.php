<?php
/* each table to be backed up. includes the sql entry and fields */

$dirs = array();
if(file_exists(AT_CONTENT_DIR . $_SESSION['course_id'].'/gameme')){
    $dirs['gameme/'] = AT_CONTENT_DIR . $_SESSION['course_id'].'/gameme' . DIRECTORY_SEPARATOR;
}
$sql = array();
$sql['gm_badges']  = 'SELECT id, alias, title, description, image_url FROM '.TABLE_PREFIX.'gm_badges WHERE course_id=?';
$sql['gm_events']  = 'SELECT id, alias, description, allow_repetitions, reach_required_repetitions, max_points, id_each_badge, id_reach_badge, each_points, reach_points, each_callback, reach_callback, reach_message FROM '.TABLE_PREFIX.'gm_events WHERE course_id=?';
$sql['gm_levels']  = 'SELECT id, title, description, points, icon FROM '.TABLE_PREFIX.'gm_levels WHERE course_id=?';
// See mantis 5739
$sql['gm_options']  = 'SELECT `gm_option` AS pref, `value` FROM '.TABLE_PREFIX.'gm_options WHERE course_id=?';


function gm_badges_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row[0]  = $row[0]; // badge id
	$new_row[1]  = $course_id; // course id
	$new_row[2]  = $row[1]; // alias
	$new_row[3]  = $row[2]; // title
	$new_row[4]  = $row[3]; // description
	$new_row[5]  = $row[4]; // image url

	return $new_row;
}
function gm_events_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row[0]  = $row[0]; // event id
	$new_row[1]  = $course_id; // course id
	$new_row[2]  = $row[1]; // alias
	$new_row[3]  = $row[2]; // description
	$new_row[4]  = $row[3]; // allow_repetitions
	$new_row[5]  = $row[4]; // reach_required_repetitions
	$new_row[6]  = $row[5]; // max_points
	$new_row[7]  = $row[6]; // id_each_badge
	$new_row[8]  = $row[7]; // id_reach_badge
	$new_row[9]  = $row[8]; // each_points
	$new_row[10]  = $row[9]; // reach_points
	$new_row[11]  = $row[10]; // each_callback
	$new_row[12]  = $row[11]; // reach_callback
	$new_row[13]  = $row[12]; // reach_message

	return $new_row;
}
function gm_levels_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row[0]  = $row[0]; // level id
	$new_row[1]  = $course_id; // course id
	$new_row[2]  = $row[1]; // title
	$new_row[3]  = $row[2]; // description
	$new_row[4]  = $row[3]; // points
	$new_row[5]  = $row[4]; // icon

	return $new_row;
}
// Unable to backup GameMe Option because the options field is a reserved word.
// Need to update ATutor backup restore to allow reserved words, but thats another project
// See mantis 5739

function gm_options_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row[0]  = 0;
	$new_row[1]  = $course_id;
	$new_row[2]  = $row[0];
	$new_row[3]  = $row[1];

	return $new_row;
}

?>