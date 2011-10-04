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

if (isset($_POST['edit'], $_POST['category'])) {
	header('Location: question_cats_manage.php?catid='.$_POST['category']);
	exit;
} else if (isset($_POST['delete'], $_POST['category'])) {
	header('Location: question_cats_delete.php?catid='.$_POST['category']);
	exit;
} else if (!empty($_POST)) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php');

$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions_categories WHERE course_id=$_SESSION[course_id] ORDER BY title";
$result	= mysql_query($sql, $db);

if ($row = mysql_fetch_assoc($result)) {
?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
	<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('question_categories'); ?></legend>
<?php	do { ?>
			<div class="row">
				<input type="radio" id="cat_<?php echo $row['category_id']; ?>" name="category" value="<?php echo $row['category_id']; ?>" />	<label for="cat_<?php echo $row['category_id']; ?>"><?php echo AT_print($row['title'], 'tests_questions_categories.title'); ?></label>
			</div>
<?php 
		} while ($row = mysql_fetch_assoc($result));
?>

		<div class="row buttons">
			<input type="submit" value="<?php echo _AT('edit'); ?>"   name="edit" />
			<input type="submit" value="<?php echo _AT('delete'); ?>" name="delete" />
		</div>
	</div>
	</form>
<?php

	} else {
	echo '<div class="input-form">';
		echo '<p>&nbsp;'._AT('cats_no_categories').'</p>';
	echo'</div>';
	}
?>


<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>