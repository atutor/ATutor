<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
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

authenticate(AT_PRIV_TEST_CREATE);


$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/index.php';
$_section[1][0] = _AT('test_manager');
$_section[1][1] = 'tools/tests/index.php';
$_section[2][0] = _AT('question_database');
$_section[2][1] = 'tools/tests/question_db.php';
$_section[3][0] = _AT('cats_categories');
$_section[3][1] = 'tools/tests/question_cats.php';
$_section[4][0] = _AT('cats_category');

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: question_cats.php');
	exit;
} else if (isset($_POST['submit'])) {

	$_POST['title'] = trim($_POST['title']);

	if (!empty($_POST['title']) && !isset($_POST['catid'])) {
		$_POST['title'] = $addslashes($_POST['title']);
		$sql	= "INSERT INTO ".TABLE_PREFIX."tests_questions_categories VALUES (0, $_SESSION[course_id], '$_POST[title]')";
		$result = mysql_query($sql, $db);
		$msg->addFeedback('CAT_ADDED');
		header('Location: question_cats.php');
		exit;
	} else if (!empty($_POST['title']) && isset($_POST['catid']))  {
		$_POST['title'] = $addslashes($_POST['title']);
		$sql	= "REPLACE INTO ".TABLE_PREFIX."tests_questions_categories VALUES ($_POST[catid], $_SESSION[course_id], '$_POST[title]')";
		$result = mysql_query($sql, $db);
		$msg->addFeedback('CAT_UPDATE_SUCCESSFUL');
		header('Location: question_cats.php');
		exit;
	} else {
		$msg->addError('CAT_NO_NAME');
	}
}

if (isset($_GET['catid'])) {
	$sql = "SELECT title FROM ".TABLE_PREFIX."tests_questions_categories WHERE category_id=$_GET[catid]";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);

	$_POST['title'] = $row['title'];
}

require(AT_INCLUDE_PATH.'header.inc.php');

$msg->printErrors();

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<?php 
if (isset($_REQUEST['catid'])) {
	echo '<input type="hidden" value="'.$_REQUEST['catid'].'" name="catid" />';
}
?>
<div class="input-form">
	<div class="row">
		<label for="cat"><?php echo _AT('title'); ?></label><br />
		<input type="text" name="title" id="cat" value="<?php echo $_POST['title']; ?>" />
	</div>

	<div class="row buttons">
		<input type="submit" value="<?php echo _AT('save'); ?>"   name="submit" accesskey="s" />
		<input type="submit" value="<?php echo _AT('cancel'); ?>" name="cancel" />
	</div>

</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>