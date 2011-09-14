<?php

$sql = array();

$sql['glossary'] = 'SELECT word_id, word, definition, related_word_id FROM '.TABLE_PREFIX.'glossary WHERE course_id=? ORDER BY word_id ASC';


function glossary_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row[0] = $row[0];
	$new_row[1] = $course_id;
	$new_row[2] = $row[1];
	$new_row[3] = $row[2];
	if ($row[3] != 0) {
		$new_row[4] = $table_id_map['glossary'][$row[3]];
	} else {
		$new_row[4] = 0;
	}

	return $new_row;
}
?>