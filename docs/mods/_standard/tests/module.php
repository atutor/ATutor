<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'moduleproxy'))) { exit(__FILE__ . ' is not a ModuleProxy'); }

define('AT_PRIV_TESTS', $this->getPrivilege());

// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'tools/my_tests.php';

$_module_pages['tools/tests/index.php']['title_var'] = 'tests';
$_module_pages['tools/tests/index.php']['parent']    = 'tools/index.php';
$_module_pages['tools/tests/index.php']['guide']     = 'instructor/?p=15.0.tests_surveys.php';
$_module_pages['tools/tests/index.php']['children']  = array('tools/tests/create_test.php', 'tools/tests/question_db.php', 'tools/tests/question_cats.php');

$_module_pages['tools/tests/create_test.php']['title_var'] = 'create_test';
$_module_pages['tools/tests/create_test.php']['parent']    = 'tools/tests/index.php';
$_module_pages['tools/tests/create_test.php']['guide']     = 'instructor/?p=15.1.creating_tests_surveys.php';

$_module_pages['tools/tests/question_db.php']['title_var'] = 'question_database';
$_module_pages['tools/tests/question_db.php']['parent']    = 'tools/tests/index.php';
$_module_pages['tools/tests/question_db.php']['guide']     = 'instructor/?p=15.2.question_database.php';

	$_module_pages['tools/tests/create_question_multi.php']['title_var'] = 'create_question_multi';
	$_module_pages['tools/tests/create_question_multi.php']['parent']    = 'tools/tests/question_db.php';


$_module_pages['tools/tests/question_cats.php']['title_var'] = 'question_categories';
$_module_pages['tools/tests/question_cats.php']['parent']    = 'tools/tests/index.php';
$_module_pages['tools/tests/question_cats.php']['children']  = array('tools/tests/question_cats_manage.php');
$_module_pages['tools/tests/question_cats.php']['guide']     = 'instructor/?p=15.3.question_categories.php';

$_module_pages['tools/tests/question_cats_manage.php']['title_var'] = 'create_category';
$_module_pages['tools/tests/question_cats_manage.php']['parent']    = 'tools/tests/question_cats.php';

$_module_pages['tools/tests/question_cats_delete.php']['title_var'] = 'delete_category';
$_module_pages['tools/tests/question_cats_delete.php']['parent']    = 'tools/tests/question_cats.php';

$_module_pages['tools/tests/edit_test.php']['title_var'] = 'edit_test';
$_module_pages['tools/tests/edit_test.php']['parent']    = 'tools/tests/index.php';

$_module_pages['tools/tests/preview.php']['title_var'] = 'preview_questions';
$_module_pages['tools/tests/preview.php']['parent']    = 'tools/tests/index.php';
$_module_pages['tools/tests/preview.php']['guide']     = 'instructor/?p=15.5.preview.php';

$_module_pages['tools/tests/preview_question.php']['title_var'] = 'preview';
$_module_pages['tools/tests/preview_question.php']['parent']    = 'tools/tests/question_db.php';

$_module_pages['tools/tests/results.php']['title_var'] = 'submissions';
$_module_pages['tools/tests/results.php']['parent']    = 'tools/tests/index.php';

$_module_pages['tools/tests/results_all.php']['guide'] = 'instructor/?p=15.5.student_submissions.php';

//$_module_pages['tools/tests/results_all_quest.php']['title_var']  =  _AT('question')." "._AT('statistics');
//$_module_pages['tools/tests/results_all_quest.php']['parent'] = 'tools/tests/index.php';
$_module_pages['tools/tests/results_all_quest.php']['guide']     = 'instructor/?p=15.6.test_statistics.php';

$_module_pages['tools/tests/delete_test.php']['title_var'] = 'delete_test';
$_module_pages['tools/tests/delete_test.php']['parent']    = 'tools/tests/index.php';

$_module_pages['tools/view_results.php']['title_var'] = 'view_results';
$_module_pages['tools/view_results.php']['parent']    = 'tools/my_tests.php';

	// test questions
	$_module_pages['tools/tests/create_question_tf.php']['title_var'] = 'create_new_question';
	$_module_pages['tools/tests/create_question_tf.php']['parent']    = 'tools/tests/question_db.php';
	
	$_module_pages['tools/tests/create_question_multi.php']['title_var'] = 'create_new_question';
	$_module_pages['tools/tests/create_question_multi.php']['parent']    = 'tools/tests/question_db.php';

	$_module_pages['tools/tests/create_question_long.php']['title_var'] = 'create_new_question';
	$_module_pages['tools/tests/create_question_long.php']['parent']    = 'tools/tests/question_db.php';

	$_module_pages['tools/tests/create_question_likert.php']['title_var'] = 'create_new_question';
	$_module_pages['tools/tests/create_question_likert.php']['parent']    = 'tools/tests/question_db.php';

	$_module_pages['tools/tests/edit_question_tf.php']['title_var'] = 'edit_question';
	$_module_pages['tools/tests/edit_question_tf.php']['parent']    = 'tools/tests/question_db.php';
	
	$_module_pages['tools/tests/edit_question_multi.php']['title_var'] = 'edit_question';
	$_module_pages['tools/tests/edit_question_multi.php']['parent']    = 'tools/tests/question_db.php';

	$_module_pages['tools/tests/edit_question_long.php']['title_var'] = 'edit_question';
	$_module_pages['tools/tests/edit_question_long.php']['parent']    = 'tools/tests/question_db.php';

	$_module_pages['tools/tests/edit_question_likert.php']['title_var'] = 'edit_question';
	$_module_pages['tools/tests/edit_question_likert.php']['parent']    = 'tools/tests/question_db.php';

$_module_pages['tools/take_test.php']['title_var'] = 'take_test';
$_module_pages['tools/take_test.php']['parent']    = 'tools/my_tests.php';

//student page
$_module_pages['tools/my_tests.php']['title_var'] = 'my_tests';
$_module_pages['tools/my_tests.php']['img']       = 'images/home-tests.gif';
?>