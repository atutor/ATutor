<?php

$sql = array();

$sql['tests'] = 'SELECT test_id, title, format, start_date, end_date, randomize_order, num_questions, instructions, content_id, result_release, random, difficulty, num_takes, anonymous, out_of FROM '.TABLE_PREFIX.'tests WHERE course_id=? ORDER BY test_id ASC';

$sql['tests_questions_categories'] = 'SELECT category_id, title FROM '.TABLE_PREFIX.'tests_questions_categories WHERE course_id=?';

$sql['tests_questions'] = 'SELECT question_id, category_id, type, feedback, question, choice_0, choice_1, choice_2, choice_3, choice_4, choice_5, choice_6, choice_7, choice_8, choice_9, answer_0, answer_1, answer_2, answer_3, answer_4, answer_5, answer_6, answer_7, answer_8, answer_9, properties, content_id FROM '.TABLE_PREFIX.'tests_questions WHERE course_id=?';

$sql['tests_questions_assoc'] = 'SELECT TQ.test_id, question_id, weight, ordering, required FROM '.TABLE_PREFIX.'tests_questions_assoc TQ, '.TABLE_PREFIX.'tests T WHERE T.course_id=? AND T.test_id=TQ.test_id ORDER BY TQ.test_id';

function tests_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row[0]  = $row[0];
	$new_row[1]  = $course_id;
	$new_row[2]  = $row[1];
	$new_row[3]  = $row[2];
	$new_row[4]  = $row[3];
	$new_row[5]  = $row[4];
	$new_row[6]  = $row[5];
	$new_row[7]  = $row[6];
	$new_row[8]  = $row[7];
	$new_row[9]  = 0;
	$new_row[10] = $row[9]  ? $row[9]  : 0;
	$new_row[11] = $row[10] ? $row[10] : 0;
	$new_row[12] = $row[11] ? $row[11] : 0;
	$new_row[13] = $row[12] ? $row[12] : 0;
	$new_row[14] = $row[13] ? $row[13] : 0;
	$new_row[15] = $row[14] ? $row[14] : 0;

	return $new_row;
}

function tests_questions_categories_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row[0] = $row[0];
	$new_row[1] = $course_id;
	$new_row[2] = $row[1];

	return $new_row;
}

function tests_questions_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row[0]  = $row[0];
	$new_row[1]  = $table_id_map['tests_questions_categories'][$row[1]];
	$new_row[2]  = $course_id;
	$new_row[3]  = $row[2];
	$new_row[4]  = $row[3];
	$new_row[5]  = $row[4];
	$new_row[6]  = $row[5];
	$new_row[7]  = $row[6];
	$new_row[8]  = $row[7];
	$new_row[9]  = $row[8];
	$new_row[10] = $row[9];
	$new_row[11] = $row[10];
	$new_row[12] = $row[11];
	$new_row[13] = $row[12];
	$new_row[14] = $row[13];
	$new_row[15] = $row[14];
	$new_row[16] = $row[15];
	$new_row[17] = $row[16];
	$new_row[18] = $row[17];
	$new_row[19] = $row[18];
	$new_row[20] = $row[19];
	$new_row[21] = $row[20];
	$new_row[22] = $row[21];
	$new_row[23] = $row[22];
	$new_row[24] = $row[23];
	$new_row[25] = $row[24];
	$new_row[26] = $row[25];
	$new_row[27] = $row[26];

	return $new_row;
}

function tests_questions_assoc_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row[0] = $table_id_map['tests'][$row[0]];
	$new_row[1] = $table_id_map['tests_questions'][$row[1]];
	$new_row[2] = $row[2];
	$new_row[3] = $row[3];
	$new_row[4] = $row[4];

	return $new_row;
}
?>