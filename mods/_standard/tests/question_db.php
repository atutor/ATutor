<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_standard/tests/classes/testQuestions.class.php');
authenticate(AT_PRIV_TESTS);

if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
	$course_base_href = 'get.php/';
} else {
	$course_base_href = 'content/' . $_SESSION['course_id'] . '/';
}

// converts array entries to ints
function intval_array ( & $value, $key) { $value = (int) $value; }

if ( (isset($_GET['edit']) || isset($_GET['delete']) || isset($_GET['export']) || isset($_GET['preview']) || isset($_GET['add'])) && !isset($_GET['questions'])){
	$msg->addError('NO_ITEM_SELECTED');
} else if (isset($_GET['submit_create'], $_GET['question_type'])) {
	header('Location: '.AT_BASE_HREF.'mods/_standard/tests/create_question_'.$addslashes($_GET['question_type']).'.php');
	exit;
} else if (isset($_GET['edit'])) {
	$id  = current($_GET['questions']);
	$num_selected = count($id);

	if ($num_selected == 1) {
		$ids = explode('|', $id[0], 2);
		$o = TestQuestions::getQuestion($ids[1]);
		if ($name = $o->getPrefix()) {
			header('Location: '.AT_BASE_HREF.'mods/_standard/tests/edit_question_'.$name.'.php?qid='.intval($ids[0]));
			exit;
		} else {
			header('Location: '.AT_BASE_HREF.'mods/_standard/tests/index.php');
			exit;
		}
	} else {
		$msg->addError('SELECT_ONE_ITEM');
	}

} else if (isset($_GET['delete'])) {
	$id  = current($_GET['questions']);
	$ids = array();
	foreach ($_GET['questions'] as $category_questions) {
		$ids = array_merge($ids, $category_questions);
	}

	array_walk($ids, 'intval_array');
	$ids = implode(',',$ids);

	header('Location: '.AT_BASE_HREF.'mods/_standard/tests/delete_question.php?qid='.$ids);
	exit;
} else if (isset($_GET['preview'])) {
	$ids = array();
	foreach ($_GET['questions'] as $category_questions) {
		$ids = array_merge($ids, $category_questions);
	}

	array_walk($ids, 'intval_array');
	$ids = implode(',',$ids);

	header('Location: '.AT_BASE_HREF.'mods/_standard/tests/preview_question.php?qid='.$ids);
	exit;
} else if (isset($_GET['add'])) {
	$id  = current($_GET['questions']);
	$ids = explode('|', $id[0], 2);
} else if (isset($_GET['export'])) {
	$ids = array();
	foreach ($_GET['questions'] as $category_questions) {
		$ids = array_merge($ids, $category_questions);
	}

	array_walk($ids, 'intval_array');

	if ($_GET['qti_export_version']=='2.1'){
		test_question_qti_export_v2p1($ids);
	} else {
		test_question_qti_export($ids);
	}

	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

?>

<div class="input-form">
	<fieldset class="group_form" style="width:45%;float:left;height:18em;min-width:15em;"><legend class="group_form"><?php echo _AT('create_new_question'); ?></legend>
		<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<input type="hidden" name="tid" value="<?php echo $tid; ?>" />
			<label for="question"><?php echo _AT('create_new_question'); ?></label><br />
			<?php $questions = TestQuestions::getQuestionPrefixNames(); ?>
			<select name="question_type" class="dropdown" id="question" size="8">
			<?php foreach ($questions as $type => $name): ?>
				<option value="<?php echo $type; ?>"><?php echo $name; ?></option>
			<?php endforeach; ?>
			</select>
			<div class="row buttons">
			<input type="submit" name="submit_create" value="<?php echo _AT('create'); ?>" />
			</div>
		</form>
	</fieldset>

	<fieldset class="group_form" style="width:45%;float:left;clear:right;height:18em;min-width:18em;"><legend class="group_form"><?php echo _AT('import_question'); ?></legend>
		<form method="post" action="<?php echo 'mods/_standard/tests/question_import.php'; ?>" enctype="multipart/form-data" >	<label for="to_file"><?php echo _AT('upload_question'); ?></label><br />
			<input type="file" name="file" id="to_file" />
			<div class="row buttons">
			<input type="submit" name="submit_import" value="<?php echo _AT('import'); ?>" />
			</div>
		</form>
	</fieldset>


<?php $tid = 0; ?>

<?php require(AT_INCLUDE_PATH.'../mods/_standard/tests/html/tests_questions.inc.php'); ?>
<br style="clear:both;" />
</div>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>