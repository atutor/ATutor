<?php
/* each table to be backed up. includes the sql entry and fields */

$sql = array();
$sql['polls'] = 'SELECT question, created_date, choice1, choice2, choice3, choice4, choice5, choice6, choice7 FROM '.TABLE_PREFIX.'polls WHERE course_id=?';

/* the tables to be restored, the order matters! */
/* the key must be the module directory name.    */
/* a {table_name}Table class must exist that extends AbstractTable */
	$restore_tables = array('polls');

function polls_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row[0]  = 0;
	$new_row[1]  = $course_id;
	$new_row[2]  = $row[0];
	$new_row[3]  = $row[1];
	$new_row[4]  = 0;
	$new_row[5]  = $row[2];
	$new_row[6]  = 0;
	$new_row[7]  = $row[3];
	$new_row[8]  = 0;
	$new_row[9]  = $row[4];
	$new_row[10] = 0;
	$new_row[11] = $row[5];
	$new_row[12] = 0;
	$new_row[13] = $row[6];
	$new_row[14] = 0;
	$new_row[15] = $row[7];
	$new_row[16] = 0;
	$new_row[17] = $row[8];
	$new_row[18] = 0;

	return $new_row;
}

?>