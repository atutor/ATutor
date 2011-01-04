<?php

$sql = array();

$sql['tests'] = 'SELECT test_id, title, format, start_date, end_date, randomize_order, num_questions, instructions, content_id, result_release, random, difficulty, num_takes, anonymous, out_of, guests, display, description, passscore, passpercent, passfeedback, failfeedback, show_guest_form FROM '.TABLE_PREFIX.'tests WHERE course_id=? ORDER BY test_id ASC';

$sql['tests_questions_categories'] = 'SELECT category_id, title FROM '.TABLE_PREFIX.'tests_questions_categories WHERE course_id=?';

$sql['tests_questions'] = 'SELECT question_id, category_id, type, feedback, question, choice_0, choice_1, choice_2, choice_3, choice_4, choice_5, choice_6, choice_7, choice_8, choice_9, answer_0, answer_1, answer_2, answer_3, answer_4, answer_5, answer_6, answer_7, answer_8, answer_9, option_0, option_1, option_2, option_3, option_4, option_5, option_6, option_7, option_8, option_9, properties, content_id FROM '.TABLE_PREFIX.'tests_questions WHERE course_id=?';

$sql['tests_questions_assoc'] = 'SELECT TQ.test_id, question_id, weight, ordering, required FROM '.TABLE_PREFIX.'tests_questions_assoc TQ, '.TABLE_PREFIX.'tests T WHERE T.course_id=? AND T.test_id=TQ.test_id ORDER BY TQ.test_id';

/* 
 * The content test association & content prerequisites cannot be put into the content/module_backup.php, because the $table_id_map does not include
 * the test hash mapping until this is reached.
 * And, for different content prerequisite type, the backup should be separated into different modules to 
 * find out the correct mapping id. For example, test prerequistes go into "tests" module backup, content 
 * prerequisites go into "content" module backup.
 * @harris Sep 25, 08, cindy Nov 17, 2009
 */
$sql['content_tests_assoc'] = 'SELECT cta.content_id, cta.test_id FROM '.TABLE_PREFIX.'content c, '.TABLE_PREFIX.'content_tests_assoc cta WHERE c.content_id = cta.content_id AND c.course_id = ? ORDER BY c.content_id';

$sql['content_prerequisites'] = 'SELECT cp.content_id, cp.type, cp.item_id FROM '.TABLE_PREFIX.'content c, '.TABLE_PREFIX.'content_prerequisites cp WHERE c.content_id = cp.content_id AND c.course_id = ? ORDER BY c.content_id';

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
	$new_row[16] = $row[15] ? $row[15] : 0; // `guests`  added 1.5.4
	$new_row[17] = $row[16] ? $row[16] : 0; // `display` added 1.5.6
	$new_row[18]  = $row[17];
	$new_row[19]  = $row[18];
	$new_row[20]  = $row[19];
	$new_row[21]  = $row[20];
	$new_row[22]  = $row[21];
	$new_row[23]  = $row[22];
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
	$new_row[0]  = $row[0]; // question_id
	$new_row[1]  = $table_id_map['tests_questions_categories'][$row[1]]; // category_id
	$new_row[2]  = $course_id; // course_id, obviously
	$new_row[3]  = $row[2];    // type
	$new_row[4]  = $row[3];    // feedback
	$new_row[5]  = $row[4];    // question
	$new_row[6]  = $row[5];    // choice_0
	$new_row[7]  = $row[6];    // choice_1
	$new_row[8]  = $row[7];    // choice_2
	$new_row[9]  = $row[8];    // choice_3
	$new_row[10] = $row[9];    // choice_4
	$new_row[11] = $row[10];   // choice_5
	$new_row[12] = $row[11];   // choice_6
	$new_row[13] = $row[12];   // choice_7
	$new_row[14] = $row[13];   // choice_8
	$new_row[15] = $row[14];   // choice_9
	$new_row[16] = $row[15];   // answer_0
	$new_row[17] = $row[16];   // answer_1
	$new_row[18] = $row[17];   // answer_2
	$new_row[19] = $row[18];   // answer_3
	$new_row[20] = $row[19];   // answer_4
	$new_row[21] = $row[20];   // answer_5
	$new_row[22] = $row[21];   // answer_6
	$new_row[23] = $row[22];   // answer_7
	$new_row[24] = $row[23];   // answer_8
	$new_row[25] = $row[24];   // answer_9
	if (version_compare($version, '1.5.4', '<')) {
		// option_[0-9] were added in 1.5.4 before properties
		$new_row[26] = '';       // option_0
		$new_row[27] = '';       // option_1
		$new_row[28] = '';       // option_2
		$new_row[29] = '';       // option_3
		$new_row[30] = '';       // option_4
		$new_row[31] = '';       // option_5
		$new_row[32] = '';       // option_6
		$new_row[33] = '';       // option_7
		$new_row[34] = '';       // option_8
		$new_row[35] = '';       // option_9
		$new_row[36] = $row[25]; // properties
		$new_row[37] = $row[26]; // content_id
	} else {
		$new_row[26] = $row[25]; // option_0
		$new_row[27] = $row[26]; // option_1
		$new_row[28] = $row[27]; // option_2
		$new_row[29] = $row[28]; // option_3
		$new_row[30] = $row[29]; // option_4
		$new_row[31] = $row[30]; // option_5
		$new_row[32] = $row[31]; // option_6
		$new_row[33] = $row[32]; // option_7
		$new_row[34] = $row[33]; // option_8
		$new_row[35] = $row[34]; // option_9
		$new_row[36] = $row[35]; // properties
		$new_row[37] = $row[36]; // content_id
	}

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


function content_tests_assoc_convert($row, $course_id, $table_id_map, $version) {
	$new_row[0] = $table_id_map['content'][$row[0]];
	$new_row[1] = $table_id_map['tests'][$row[1]];
	return $new_row;
}

function content_prerequisites_convert($row, $course_id, $table_id_map, $version) {
	$new_row[0] = $table_id_map['content'][$row[0]];
	$new_row[1] = $row[1];
	$new_row[2] = $table_id_map['tests'][$row[2]];
	return $new_row;
}
?>