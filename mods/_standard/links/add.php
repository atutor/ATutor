<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2010                                                  */
/* Inclusive Design Institute                                               */
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

if (!$_SESSION['enroll']) {
	$msg->addInfo('NOT_ENROLLED');
	require(AT_INCLUDE_PATH.'header.inc.php');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

require (AT_INCLUDE_PATH.'../mods/_standard/links/lib/links.inc.php');

if (!manage_links()) {
	$_pages['mods/_standard/links/index.php']['children']  = array('mods/_standard/links/add.php');
}

if (!isset($_POST['url'])) {
	$_POST['url'] = "http://";
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'mods/_standard/links/index.php');
	exit;
} else if (isset($_POST['add_link']) && isset($_POST['submit'])) {
	$missing_fields = array();
	if ($_POST['cat'] == 0 || $_POST['cat'] == '') {
		$missing_fields[] = _AT('category');
	}
	if (trim($_POST['title']) == '') {
		$missing_fields[] = _AT('title');
	}
	if (trim($_POST['url']) == '' || $_POST['url'] == 'http://') {
		$missing_fields[] = _AT('url');
	}
	if (trim($_POST['description']) == '') {
		$missing_fields[] = _AT('description');
	}

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}


	if (!$msg->containsErrors() && isset($_POST['submit'])) {

		$_POST['cat'] = intval($_POST['cat']);
		$_POST['title']  = $addslashes($_POST['title']);
		$_POST['url'] == $addslashes($_POST['url']);
		$_POST['description']  = $addslashes($_POST['description']);

		$name = get_display_name($_SESSION['member_id']);
		$email = '';

		$approved = 0; //not approved for student submissions

		$sql	= "INSERT INTO ".TABLE_PREFIX."links VALUES (NULL, $_POST[cat], '$_POST[url]', '$_POST[title]', '$_POST[description]', $approved, '$name', '$email', NOW(), 0)";
		mysql_query($sql, $db);
	
		$msg->addFeedback('LINK_ADDED');

		header('Location: '.AT_BASE_HREF.'mods/_standard/links/index.php');
		exit;
	} else {
		$_POST['title']  = stripslashes($_POST['title']);
		$_POST['url'] == stripslashes($_POST['url']);
		$_POST['description']  = stripslashes($_POST['description']);
	}
}
$onload = 'document.form.title.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');

$categories = get_link_categories();

if (empty($categories)) {
	$msg->addInfo('NO_LINK_CATEGORIES');
	$msg->printInfos();

} else {
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="add_link" value="true" />

<div class="input-form">
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="title"><?php echo _AT('title'); ?></label><br />
		<input type="text" name="title" size="40" id="title" value="<?php echo AT_print($_POST['title'], 'input.text'); ?>"/>
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="cat"><?php echo _AT('category'); ?></label><br />
		<select name="cat" id="cat"><?php
			if ($pcat_id) {
				$current_cat_id = $pcat_id;
				$exclude = false; /* don't exclude the children */
			} else {
				$current_cat_id = $cat_id;
				$exclude = true; /* exclude the children */
			}
			select_link_categories($categories, 0, $_POST['cat'], FALSE);
			?>
		</select>
	</div>
	
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="url"><?php echo _AT('url'); ?></label><br />
		<input type="text" name="url" size="40" id="url" value="<?php echo AT_print($_POST['url'], 'input.text'); ?>" />
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="description"><?php echo _AT('description'); ?></label><br />
		<textarea name="description" cols="55" rows="2" id="description" ><?php echo AT_print($_POST['description'], 'input.text'); ?></textarea>
	</div>
	
	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('submit'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?> " />
	</div>
</div>
</form>

<?php 
}	
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>