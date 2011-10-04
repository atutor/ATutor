<?php
/* each table to be backed up. includes the sql entry and fields */

$sql = array();
$sql['faq_topics']  = 'SELECT topic_id, name FROM '.TABLE_PREFIX.'faq_topics WHERE course_id=? ORDER BY name';
$sql['faq_entries'] = 'SELECT E.topic_id, E.revised_date, E.approved, E.question, E.answer FROM '.TABLE_PREFIX.'faq_entries E INNER JOIN '.TABLE_PREFIX.'faq_topics T USING (topic_id) WHERE T.course_id=? ORDER BY T.name, E.question';

function faq_topics_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row[0]  = $row[0];
	$new_row[1]  = $course_id;
	$new_row[2]  = $row[1];

	return $new_row;
}

function faq_entries_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row[0]  = 0;
	$new_row[1]  = $table_id_map['faq_topics'][$row[0]];
	$new_row[2]  = $row[1];
	$new_row[3]  = $row[2];
	$new_row[4]  = $row[3];
	$new_row[5]  = $row[4];

	return $new_row;
}

?>