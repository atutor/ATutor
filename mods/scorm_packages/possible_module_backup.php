<?php

/****
This is what the backup file MIGHT look like.

*** Problem ***
the org directories within the course directory are determined by the org id. to work correctly the restore would have to rename each org directory to the new org id. that process of renaming a directory after it has been restored is not possible.

we would need custom code to execute somewhere that would do the conversion by using the $table_id_map data to translate the org ids.

instead of just copying the directories blindly this module would have to override the directory restoring functionality to rename the org directories as they are copied.
**/

exit;

$dirs = array();
$dirs['sco/'] = realpath(AT_INCLUDE_PATH . '../').DIRECTORY_SEPARATOR.'sco'.DIRECTORY_SEPARATOR.'?'.DIRECTORY_SEPARATOR;

$sql = array();
$sql['packages']  = 'SELECT package_id, source, time, ptype FROM '.TABLE_PREFIX.'packages WHERE course_id=? ORDER BY package_id';

$sql['scorm_1_2_org']  = 'SELECT O.org_id, O.package_id, O.title, O.credit, O.lesson_mode FROM '.TABLE_PREFIX.'scorm_1_2_org O INNER JOIN '.TABLE_PREFIX.'packages P ON  (O.package_id=P.package_id) WHERE P.course_id=? ORDER BY O.org_id';

$sql['scorm_1_2_item']  = 'SELECT I.item_id, I.org_id, I.idx, I.title, I.href, I.scormtype, I.prerequisites, I.maxtimeallowed, I.timelimitaction, I.dataformlms, I.masteryscore FROM '.TABLE_PREFIX.'scorm_1_2_item I INNER JOIN '.TABLE_PREFIX.'scorm_1_2_org O ON (I.org_id=O.org_id) INNER JOIN '.TABLE_PREFIX.'packages P ON (O.package_id=P.package_id) WHERE Pcourse_id=? ORDER BY I.item_id';

function packages_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row[0]  = $row[0];
	$new_row[1]  = $row[1];
	$new_row[2]  = $row[2];
	$new_row[3]  = $course_id;
	$new_row[4]  = $row[3];

	return $new_row;
}

function scorm_1_2_org_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row[0]  = $row[0];
	$new_row[1]  = $table_id_map['packages'][$row[1]]; // package_id fk
	$new_row[2]  = $row[2];
	$new_row[3]  = $row[3];
	$new_row[4]  = $row[4];

	return $new_row;
}

function scorm_1_2_item_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row[0]  = $row[0];
	$new_row[1]  = $table_id_map['scorm_1_2_org'][$row[1]]; // org_id fk
	$new_row[2]  = $row[2];
	$new_row[3]  = $row[3];
	$new_row[4]  = $row[4];
	$new_row[5]  = $row[5];
	$new_row[6]  = $row[6];
	$new_row[7]  = $row[7];
	$new_row[8]  = $row[8];
	$new_row[9]  = $row[9];
	$new_row[10]  = $row[10];

	return $new_row;
}

?>