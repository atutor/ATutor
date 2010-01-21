<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_TESTS', $this->getPrivilege());

//modules sub-content
$this->_list['my_tests'] = array('title_var'=>'my_tests','file'=>'mods/_standard/tests/sublinks.php');

// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'mods/_standard/tests/my_tests.php';

$this->_pages['mods/_standard/tests/index.php']['title_var'] = 'tests';
$this->_pages['mods/_standard/tests/index.php']['parent']    = 'tools/index.php';
$this->_pages['mods/_standard/tests/index.php']['guide']     = 'instructor/?p=tests_surveys.php';
$this->_pages['mods/_standard/tests/index.php']['children']  = array('mods/_standard/tests/create_test.php', 'mods/_standard/tests/question_db.php', 'mods/_standard/tests/question_cats.php');

$this->_pages['mods/_standard/tests/create_test.php']['title_var'] = 'create_test';
$this->_pages['mods/_standard/tests/create_test.php']['parent']    = 'mods/_standard/tests/index.php';
$this->_pages['mods/_standard/tests/create_test.php']['guide']     = 'instructor/?p=creating_tests_surveys.php';

$this->_pages['mods/_standard/tests/import_test.php']['title_var'] = 'import_test';
$this->_pages['mods/_standard/tests/import_test.php']['parent']    = 'mods/_standard/tests/index.php';

$this->_pages['mods/_standard/tests/question_import.php']['title_var'] = 'question_test';
$this->_pages['mods/_standard/tests/question_import.php']['parent']    = 'mods/_standard/tests/index.php';

$this->_pages['mods/_standard/tests/question_db.php']['title_var'] = 'question_database';
$this->_pages['mods/_standard/tests/question_db.php']['parent']    = 'mods/_standard/tests/index.php';
$this->_pages['mods/_standard/tests/question_db.php']['guide']     = 'instructor/?p=question_database.php';

	$this->_pages['mods/_standard/tests/create_question_multi.php']['title_var'] = 'create_question_multi';
	$this->_pages['mods/_standard/tests/create_question_multi.php']['parent']    = 'mods/_standard/tests/question_db.php';

$this->_pages['mods/_standard/tests/question_cats.php']['title_var'] = 'question_categories';
$this->_pages['mods/_standard/tests/question_cats.php']['parent']    = 'mods/_standard/tests/index.php';
$this->_pages['mods/_standard/tests/question_cats.php']['children']  = array('mods/_standard/tests/question_cats_manage.php');
$this->_pages['mods/_standard/tests/question_cats.php']['guide']     = 'instructor/?p=question_categories.php';

$this->_pages['mods/_standard/tests/question_cats_manage.php']['title_var'] = 'create_category';
$this->_pages['mods/_standard/tests/question_cats_manage.php']['parent']    = 'mods/_standard/tests/question_cats.php';

$this->_pages['mods/_standard/tests/question_cats_delete.php']['title_var'] = 'delete_category';
$this->_pages['mods/_standard/tests/question_cats_delete.php']['parent']    = 'mods/_standard/tests/question_cats.php';

$this->_pages['mods/_standard/tests/edit_test.php']['title_var'] = 'edit_test';
$this->_pages['mods/_standard/tests/edit_test.php']['parent']    = 'mods/_standard/tests/index.php';
$this->_pages['mods/_standard/tests/edit_test.php']['guide']     = 'instructor/?p=creating_tests_surveys.php';

$this->_pages['mods/_standard/tests/preview.php']['title_var'] = 'preview_questions';
$this->_pages['mods/_standard/tests/preview.php']['parent']    = 'mods/_standard/tests/index.php';
$this->_pages['mods/_standard/tests/preview.php']['guide']     = 'instructor/?p=preview.php';

$this->_pages['mods/_standard/tests/preview_question.php']['title_var'] = 'preview';
$this->_pages['mods/_standard/tests/preview_question.php']['parent']    = 'mods/_standard/tests/question_db.php';

$this->_pages['mods/_standard/tests/results.php']['title_var'] = 'submissions';
$this->_pages['mods/_standard/tests/results.php']['parent']    = 'mods/_standard/tests/index.php';

$this->_pages['mods/_standard/tests/results_all.php']['guide'] = 'instructor/?p=student_submissions.php';

//$this->_pages['mods/_standard/tests/results_all_quest.php']['title_var']  =  _AT('question')." "._AT('statistics');
//$this->_pages['mods/_standard/tests/results_all_quest.php']['parent'] = 'mods/_standard/tests/index.php';
$this->_pages['mods/_standard/tests/results_all_quest.php']['guide']     = 'instructor/?p=test_statistics.php';

$this->_pages['mods/_standard/tests/delete_test.php']['title_var'] = 'delete_test';
$this->_pages['mods/_standard/tests/delete_test.php']['parent']    = 'mods/_standard/tests/index.php';

$this->_pages['mods/_standard/tests/view_results.php']['title_var'] = 'view_results';
$this->_pages['mods/_standard/tests/view_results.php']['parent']    = 'mods/_standard/tests/my_tests.php';
$this->_pages['mods/_standard/tests/view_results.php']['children']  = array(); // to create the "back to tests" link

	// test questions
	$this->_pages['mods/_standard/tests/create_question_tf.php']['title_var'] = 'create_new_question';
	$this->_pages['mods/_standard/tests/create_question_tf.php']['parent']    = 'mods/_standard/tests/question_db.php';

	$this->_pages['mods/_standard/tests/create_question_truefalse.php']['title_var'] = 'create_new_question';
	$this->_pages['mods/_standard/tests/create_question_truefalse.php']['parent']    = 'mods/_standard/tests/question_db.php';

	$this->_pages['mods/_standard/tests/create_question_multi.php']['title_var'] = 'create_new_question';
	$this->_pages['mods/_standard/tests/create_question_multi.php']['parent']    = 'mods/_standard/tests/question_db.php';

	$this->_pages['mods/_standard/tests/create_question_multichoice.php']['title_var'] = 'create_new_question';
	$this->_pages['mods/_standard/tests/create_question_multichoice.php']['parent']    = 'mods/_standard/tests/question_db.php';

	$this->_pages['mods/_standard/tests/create_question_multianswer.php']['title_var'] = 'create_new_question';
	$this->_pages['mods/_standard/tests/create_question_multianswer.php']['parent']    = 'mods/_standard/tests/question_db.php';

	$this->_pages['mods/_standard/tests/create_question_long.php']['title_var'] = 'create_new_question';
	$this->_pages['mods/_standard/tests/create_question_long.php']['parent']    = 'mods/_standard/tests/question_db.php';

	$this->_pages['mods/_standard/tests/create_question_likert.php']['title_var'] = 'create_new_question';
	$this->_pages['mods/_standard/tests/create_question_likert.php']['parent']    = 'mods/_standard/tests/question_db.php';

	$this->_pages['mods/_standard/tests/create_question_matching.php']['title_var'] = 'create_new_question';
	$this->_pages['mods/_standard/tests/create_question_matching.php']['parent']    = 'mods/_standard/tests/question_db.php';

	$this->_pages['mods/_standard/tests/create_question_matchingdd.php']['title_var'] = 'create_new_question';
	$this->_pages['mods/_standard/tests/create_question_matchingdd.php']['parent']    = 'mods/_standard/tests/question_db.php';

	$this->_pages['mods/_standard/tests/create_question_ordering.php']['title_var'] = 'create_new_question';
	$this->_pages['mods/_standard/tests/create_question_ordering.php']['parent']    = 'mods/_standard/tests/question_db.php';

	$this->_pages['mods/_standard/tests/edit_question_tf.php']['title_var'] = 'edit_question';
	$this->_pages['mods/_standard/tests/edit_question_tf.php']['parent']    = 'mods/_standard/tests/question_db.php';

	$this->_pages['mods/_standard/tests/edit_question_truefalse.php']['title_var'] = 'edit_question';
	$this->_pages['mods/_standard/tests/edit_question_truefalse.php']['parent']    = 'mods/_standard/tests/question_db.php';

	$this->_pages['mods/_standard/tests/edit_question_multi.php']['title_var'] = 'edit_question';
	$this->_pages['mods/_standard/tests/edit_question_multi.php']['parent']    = 'mods/_standard/tests/question_db.php';

	$this->_pages['mods/_standard/tests/edit_question_multichoice.php']['title_var'] = 'edit_question';
	$this->_pages['mods/_standard/tests/edit_question_multichoice.php']['parent']    = 'mods/_standard/tests/question_db.php';

	$this->_pages['mods/_standard/tests/edit_question_multianswer.php']['title_var'] = 'edit_question';
	$this->_pages['mods/_standard/tests/edit_question_multianswer.php']['parent']    = 'mods/_standard/tests/question_db.php';

	$this->_pages['mods/_standard/tests/edit_question_long.php']['title_var'] = 'edit_question';
	$this->_pages['mods/_standard/tests/edit_question_long.php']['parent']    = 'mods/_standard/tests/question_db.php';

	$this->_pages['mods/_standard/tests/edit_question_likert.php']['title_var'] = 'edit_question';
	$this->_pages['mods/_standard/tests/edit_question_likert.php']['parent']    = 'mods/_standard/tests/question_db.php';

	$this->_pages['mods/_standard/tests/edit_question_matching.php']['title_var'] = 'edit_question';
	$this->_pages['mods/_standard/tests/edit_question_matching.php']['parent']    = 'mods/_standard/tests/question_db.php';

	$this->_pages['mods/_standard/tests/edit_question_matchingdd.php']['title_var'] = 'edit_question';
	$this->_pages['mods/_standard/tests/edit_question_matchingdd.php']['parent']    = 'mods/_standard/tests/question_db.php';

	$this->_pages['mods/_standard/tests/edit_question_ordering.php']['title_var'] = 'edit_question';
	$this->_pages['mods/_standard/tests/edit_question_ordering.php']['parent']    = 'mods/_standard/tests/question_db.php';

	$this->_pages['mods/_standard/tests/delete_question.php']['title_var'] = 'delete';
	$this->_pages['mods/_standard/tests/delete_question.php']['parent'] = 'mods/_standard/tests/question_db.php';

$this->_pages['mods/_standard/tests/take_test.php']['title_var'] = 'take_test';
$this->_pages['mods/_standard/tests/take_test.php']['parent']    = 'mods/_standard/tests/my_tests.php';

$this->_pages['mods/_standard/tests/take_test_q.php']['title_var'] = 'take_test';
$this->_pages['mods/_standard/tests/take_test_q.php']['parent']    = 'mods/_standard/tests/my_tests.php';

$this->_pages['mods/_standard/tests/test_intro.php']['title_var'] = 'take_test';
$this->_pages['mods/_standard/tests/test_intro.php']['parent']    = 'mods/_standard/tests/my_tests.php';

//student page
$this->_pages['mods/_standard/tests/my_tests.php']['title_var'] = 'my_tests';
$this->_pages['mods/_standard/tests/my_tests.php']['img']       = 'images/home-tests.png';
$this->_pages['mods/_standard/tests/my_tests.php']['icon']       = 'images/home-tests_sm.png';
?>