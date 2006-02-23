<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006                                      */
/* Written by Greg Gay, Joel Kronenberg & Chris Ridpath         */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_READING_LIST);


// initial values for form
$id = "0";
$title = "";
$author = "";
$date = ""; 
$comments = "";
$page_return = $_GET['page_return'];

// check if user has submitted form
if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');

	if (trim($_POST['page_return']) != ''){
		header('Location: '. $_POST['page_return']);
	}
	else {
		header('Location: index_instructor.php');
	}
	exit;
} else if (isset($_POST['submit'])) {
	if (trim($_POST['title']) == '') {
		$msg->addError('TITLE_EMPTY');
	}
	if (trim($_POST['author']) == '') {
		$msg->addError('RL_AUTHOR_EMPTY');
	}

	if (!$msg->containsErrors()) {
		$_POST['title'] = $addslashes($_POST['title']);
		$_POST['author'] = $addslashes($_POST['author']);
		$_POST['date'] = $addslashes($_POST['date']);
		$_POST['comments'] = $addslashes($_POST['comments']);
		
		$id = intval ($_POST['id']);

		if ($id == '0'){ // creating a new handout resource
			$sql = "INSERT INTO ".TABLE_PREFIX."external_resources VALUES ($id, $_SESSION[course_id],
			".RL_TYPE_HANDOUT.", 
			'$_POST[title]', 
			'$_POST[author]', 
			'', 
			'$_POST[date]', 
			'$_POST[comments]',
			'',
			'')";
			$result = mysql_query($sql,$db);

			// index to new handout resource
			$id_new = mysql_insert_id($db);

			$msg->addFeedback('RL_HANDOUT_ADDED');
		}
		else { // modifying an existing handout resource

			$sql = "UPDATE ".TABLE_PREFIX."external_resources SET title='$_POST[title]', author='$_POST[author]', date='$_POST[date]', comments='$_POST[comments]' WHERE resource_id='$id' AND course_id=$_SESSION[course_id]";

			$result = mysql_query($sql,$db);

			// index to handout resource
			$id_new = $id;

			$msg->addFeedback('RL_RESOURCE_UPDATED');
		}

		if (trim($_POST['page_return']) != ''){
			header('Location: '. $_POST['page_return']. '?existingbook='. $id_new);
		}
		else {
			header('Location: index_instructor.php');
		}
		exit;
	}
	else { // submission contained an error, update form values for redisplay
		$title = $_POST['title'];
		$author = $_POST['author'];
		$date = $_POST['date']; 
		$comments = $_POST['comments'];
		$page_return = $_POST['page_return'];
	}
}

// is user modifying an existing handout resource?
if (isset($_GET['id'])){
	// yes, get resource from database
	$id = intval ($_GET['id']);

	$sql = "SELECT * FROM ".TABLE_PREFIX."external_resources WHERE course_id=$_SESSION[course_id] AND resource_id=$id";
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)){
		$title = $row['title'];
		$author = $row['author'];
		$date = $row['date']; 
		$comments = $row['comments'];
	}
	// change title of page to 'edit handout resource' (default is 'add handout resource')
	$_pages['reading_list/add_resource_handout.php'][title_var] = 'rl_edit_resource_handout';
}

$onload = 'document.form.name.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="id" value="<?php echo $id ?>" />
<input type="hidden" name="page_return" value="<?php echo $page_return ?>" />

<div class="input-form">	
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="title"><?php  echo _AT('title'); ?></label><br />
		<input type="text" name="title" size="35" id="title" value="<?php echo $title ?>" /><br />
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="author"><?php  echo _AT('rl_author'); ?></label><br />
		<input type="text" name="author" size="25" id="author" value="<?php echo $author ?>" /><br />
	</div>

	<div class="row">
		<label for="date"><?php  echo _AT('rl_year_written'); ?></label><br />
		<input type="text" name="date" size="6" id="date" value="<?php echo $date ?>" /><br />
	</div>

	<div class="row">
		<label for="comments"><?php  echo _AT('rl_comment'); ?></label><br />
		<input type="text" name="comments" size="75" id="comments" value="<?php echo $comments ?>" /><br />
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>