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

if ($_POST['submit'] == _AT('edit')) {
	if ($_POST['category']) {
		header('Location: question_cats_manage.php?catid='.$_POST['category']);
		exit;
	} else {
		$msg->addError('NO_CAT_SELECTED');
	}

} else if ($_POST['submit'] == _AT('delete')) {
	if (isset($_POST['category'])) {
		//confirm
		header('Location: question_cats_delete.php?catid='.$_POST['category']);
		exit;

	} else {
		$msg->addError('NO_CAT_SELECTED');
	}	
} 

require(AT_INCLUDE_PATH.'header.inc.php');


$msg->addHelp('QUESTION_CATEGORIES');
$msg->printAll();

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<div class="input-form">
<?php 
	$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions_categories WHERE course_id=$_SESSION[course_id] ORDER BY title";
	$result	= mysql_query($sql, $db);

	if ($row = mysql_fetch_assoc($result)) {
		do {
?>
			<div class="row">
				<input type="radio" id="cat_<?php echo $row['category_id']; ?>" name="category" value="<?php echo $row['category_id']; ?>" />
				<label for="cat_<?php echo $row['category_id']; ?>"><?php echo $row['title']; ?></label>
			</div>
<?php 
		} while ($row = mysql_fetch_assoc($result));
?>

		<div class="row buttons">
			<input type="submit" value="<?php echo _AT('edit'); ?>"   name="submit" />
			<input type="submit" value="<?php echo _AT('delete'); ?>" name="submit" />
		</div>
<?php

	} else {
		echo _AT('cats_no_categories');
	}
?>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>