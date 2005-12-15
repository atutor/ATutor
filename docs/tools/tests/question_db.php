<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

$page = 'tests';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_TESTS);

if ( (isset($_GET['edit']) || isset($_GET['delete']) || isset($_GET['preview']) || isset($_GET['add'])) && !isset($_GET['id'])){
	$msg->addError('NO_ITEM_SELECTED');
} else if (isset($_GET['submit_create'])) {
	header('Location: '.$_base_href.'tools/tests/create_question_'.$addslashes($_GET['question_type']).'.php');
	exit;
} else if (isset($_GET['edit'])) {
	$ids = explode('|', $_GET['id'], 2);
	switch ($ids[1]) {
		case 1:
			$name = 'multi';
			break;

		case 2:
			$name = 'tf';
			break;

		case 3:
			$name = 'long';
			break;

		case 4:
			$name = 'likert';
		break;

		default:
			header('Location: '.$_base_href.'tools/tests/index.php');
			exit;
		break;
	}

	header('Location: '.$_base_href.'tools/tests/edit_question_'.$addslashes($name).'.php?qid='.intval($ids[0]));
	exit;
} else if (isset($_GET['delete'])) {
	$ids = explode('|', $_GET['id'], 2);
	header('Location: '.$_base_href.'tools/tests/delete_question.php?qid='.intval($ids[0]));
	exit;
} else if (isset($_GET['preview'])) {
	$ids = explode('|', $_GET['id'], 2);
	header('Location: '.$_base_href.'tools/tests/preview_question.php?qid='.intval($ids[0]));
	exit;
} else if (isset($_GET['add'])) {
	$ids = explode('|', $_GET['id'], 2);

	//tools/tests/add_test_questions_confirm.php
}

require(AT_INCLUDE_PATH.'header.inc.php');

?>

<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="tid" value="<?php echo $tid; ?>" />

<div class="input-form" style="width: 30%">
	<div class="row">
		<label for="question"><?php echo _AT('create_new_question'); ?></label><br />
		<select name="question_type" class="dropdown" id="question">
			<option value="multi"><?php echo _AT('test_mc'); ?></option>
			<option value="tf"><?php echo _AT('test_tf'); ?></option>
			<option value="long"><?php echo _AT('test_open'); ?></option>
			<option value="likert"><?php echo _AT('test_lk'); ?></option>
		</select>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit_create" value="<?php echo _AT('create'); ?>" />
	</div>
</div>
</form>

<?php $tid = 0; ?>

<?php require(AT_INCLUDE_PATH.'html/tests_questions.inc.php'); ?>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>