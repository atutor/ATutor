<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

// if this module is to be made available to students on the Home or Main Navigation
$_modules[] = 'tools/my_tests.php';

$_pages['tools/tests/index.php']['title_var'] = 'tests';
$_pages['tools/tests/index.php']['privilege'] = AT_PRIV_TEST_CREATE + AT_PRIV_TEST_MARK;
$_pages['tools/tests/index.php']['parent']    = 'tools/index.php';
$_pages['tools/tests/index.php']['guide']     = 'instructor/?p=15.0.tests_surveys.php';
$_pages['tools/tests/index.php']['children']  = array('tools/tests/create_test.php', 'tools/tests/question_db.php', 'tools/tests/question_cats.php');

$_pages['tools/tests/create_test.php']['title_var']  = 'create_test';
$_pages['tools/tests/create_test.php']['parent'] = 'tools/tests/index.php';
$_pages['tools/tests/create_test.php']['guide']     = 'instructor/?p=15.1.creating_tests_surveys.php';

$_pages['tools/tests/question_db.php']['title_var']  = 'question_database';
$_pages['tools/tests/question_db.php']['parent'] = 'tools/tests/index.php';
$_pages['tools/tests/question_db.php']['guide']     = 'instructor/?p=15.2.question_database.php';

	$_pages['tools/tests/create_question_multi.php']['title_var']  = 'create_question_multi';
	$_pages['tools/tests/create_question_multi.php']['parent'] = 'tools/tests/question_db.php';


$_pages['tools/tests/question_cats.php']['title_var']  = 'question_categories';
$_pages['tools/tests/question_cats.php']['parent'] = 'tools/tests/index.php';
$_pages['tools/tests/question_cats.php']['children'] = array('tools/tests/question_cats_manage.php');
$_pages['tools/tests/question_cats.php']['guide']     = 'instructor/?p=15.3.question_categories.php';

$_pages['tools/tests/question_cats_manage.php']['title_var']  = 'create_category';
$_pages['tools/tests/question_cats_manage.php']['parent'] = 'tools/tests/question_cats.php';

$_pages['tools/tests/question_cats_delete.php']['title_var']  = 'delete_category';
$_pages['tools/tests/question_cats_delete.php']['parent'] = 'tools/tests/question_cats.php';

$_pages['tools/tests/edit_test.php']['title_var']  = 'edit_test';
$_pages['tools/tests/edit_test.php']['parent'] = 'tools/tests/index.php';

$_pages['tools/tests/preview.php']['title_var']  = 'preview_questions';
$_pages['tools/tests/preview.php']['parent'] = 'tools/tests/index.php';
$_pages['tools/tests/preview.php']['guide']     = 'instructor/?p=15.5.preview.php';

$_pages['tools/tests/preview_question.php']['title_var']  = 'preview';
$_pages['tools/tests/preview_question.php']['parent'] = 'tools/tests/question_db.php';

$_pages['tools/tests/results.php']['title_var']  = 'submissions';
$_pages['tools/tests/results.php']['parent'] = 'tools/tests/index.php';

$_pages['tools/tests/results_all.php']['guide']     = 'instructor/?p=15.5.student_submissions.php';

//$_pages['tools/tests/results_all_quest.php']['title_var']  =  _AT('question')." "._AT('statistics');
//$_pages['tools/tests/results_all_quest.php']['parent'] = 'tools/tests/index.php';
$_pages['tools/tests/results_all_quest.php']['guide']     = 'instructor/?p=15.6.test_statistics.php';

$_pages['tools/tests/delete_test.php']['title_var']  = 'delete_test';
$_pages['tools/tests/delete_test.php']['parent'] = 'tools/tests/index.php';

$_pages['tools/view_results.php']['title_var']  = 'view_results';
$_pages['tools/view_results.php']['parent'] = 'tools/my_tests.php';

	// test questions
	$_pages['tools/tests/create_question_tf.php']['title_var']  = 'create_new_question';
	$_pages['tools/tests/create_question_tf.php']['parent'] = 'tools/tests/question_db.php';
	
	$_pages['tools/tests/create_question_multi.php']['title_var']  = 'create_new_question';
	$_pages['tools/tests/create_question_multi.php']['parent'] = 'tools/tests/question_db.php';

	$_pages['tools/tests/create_question_long.php']['title_var']  = 'create_new_question';
	$_pages['tools/tests/create_question_long.php']['parent'] = 'tools/tests/question_db.php';

	$_pages['tools/tests/create_question_likert.php']['title_var']  = 'create_new_question';
	$_pages['tools/tests/create_question_likert.php']['parent'] = 'tools/tests/question_db.php';

	$_pages['tools/tests/edit_question_tf.php']['title_var']  = 'edit_question';
	$_pages['tools/tests/edit_question_tf.php']['parent'] = 'tools/tests/question_db.php';
	
	$_pages['tools/tests/edit_question_multi.php']['title_var']  = 'edit_question';
	$_pages['tools/tests/edit_question_multi.php']['parent'] = 'tools/tests/question_db.php';

	$_pages['tools/tests/edit_question_long.php']['title_var']  = 'edit_question';
	$_pages['tools/tests/edit_question_long.php']['parent'] = 'tools/tests/question_db.php';

	$_pages['tools/tests/edit_question_likert.php']['title_var']  = 'edit_question';
	$_pages['tools/tests/edit_question_likert.php']['parent'] = 'tools/tests/question_db.php';

$_pages['tools/take_test.php']['title_var']  = 'take_test';
$_pages['tools/take_test.php']['parent'] = 'tools/my_tests.php';

?>