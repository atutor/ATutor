<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

$page = 'tests';
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_TESTS);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: question_cats.php');
	exit;
} else if (isset($_POST['submit'])) {

	$_POST['title'] = trim($_POST['title']);

	if (!empty($_POST['title']) && !isset($_POST['catid'])) {
		$_POST['title'] = $addslashes($_POST['title']);

		$sql	= "INSERT INTO %stests_questions_categories VALUES (NULL, %d, '%s')";
		$result = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'], $_POST['title']));
		
		if($result > 0){
		    $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		}
		header('Location: question_cats.php');
		exit;
	} else if (!empty($_POST['title']) && isset($_POST['catid']))  {
		$_POST['title'] = $addslashes($_POST['title']);

		$sql	= "REPLACE INTO %stests_questions_categories VALUES (%d, %d, '%s')";
		$result = queryDB($sql, array(TABLE_PREFIX, $_POST['catid'], $_SESSION['course_id'], $_POST['title']));
		
        if($result > 0){
		    $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		}
		header('Location: question_cats.php');
		exit;
	} else {
		$msg->addError(array('EMPTY_FIELDS', _AT('title')));
	}
}

if (isset($_GET['catid'])) {
	$sql = "SELECT title FROM %stests_questions_categories WHERE category_id=%d";
	$row = queryDB($sql, array(TABLE_PREFIX, $_GET['catid']), TRUE);

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
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('create_category'); ?></legend>
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="cat"><?php echo _AT('title'); ?></label><br />
		<input type="text" name="title" id="cat" value="<?php echo htmlspecialchars($_POST['title']); ?>" />
	</div>

	<div class="row buttons">
		<input type="submit" value="<?php echo _AT('save'); ?>"   name="submit" accesskey="s" />
		<input type="submit" value="<?php echo _AT('cancel'); ?>" name="cancel" />
	</div>
	</fieldset>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>