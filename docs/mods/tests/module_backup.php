<?php

$sql = array();

$sql['tests.csv'] = 'SELECT test_id, title, format, start_date, end_date, randomize_order, num_questions, instructions, content_id, result_release, random, difficulty, num_takes, anonymous, out_of FROM '.TABLE_PREFIX.'tests WHERE course_id=? ORDER BY test_id ASC';

$sql['tests_questions.csv'] = 'SELECT question_id, category_id, type, feedback, question, choice_0, choice_1, choice_2, choice_3, choice_4, choice_5, choice_6, choice_7, choice_8, choice_9, answer_0, answer_1, answer_2, answer_3, answer_4, answer_5, answer_6, answer_7, answer_8, answer_9, properties, content_id FROM '.TABLE_PREFIX.'tests_questions WHERE course_id=?';

$sql['tests_questions_categories.csv'] = 'SELECT category_id, title FROM '.TABLE_PREFIX.'tests_questions_categories WHERE course_id=?';

$sql['tests_questions_assoc.csv'] = 'SELECT TQ.test_id, question_id, weight, ordering, required FROM '.TABLE_PREFIX.'tests_questions_assoc TQ, '.TABLE_PREFIX.'tests T WHERE T.course_id=? AND T.test_id=TQ.test_id ORDER BY TQ.test_id';
?>