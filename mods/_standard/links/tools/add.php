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

define('AT_INCLUDE_PATH', '../../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require (AT_INCLUDE_PATH.'../mods/_standard/links/lib/links.inc.php');

if (!manage_links()) {
	$msg->addError('ACCESS_DENIED');
	header('Location: '.AT_BASE_HREF.'mods/_standard/links/index.php');
	exit;
}

if (!isset($_POST['approved'])) {
	$_POST['approved'] = 1;
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'mods/_standard/links/tools/index.php');
	exit;
} 

if (isset($_POST['add_link']) && isset($_POST['submit'])) {

	//check category_id and see if user is allowed..
	$cat = explode('-', $_POST['cat']);
	$cat_id = intval($cat[0]);
	$owner_type = intval($cat[1]);
	$owner_id = intval($cat[2]);

	if (!links_authenticate($owner_type, $owner_id)) {
		$msg->addError('ACCESS_DENIED');
		header('Location: '.AT_BASE_HREF.'mods/_standard/links/index.php');
		exit;
	}

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

		//Check length of the post, if it's exceeded 64 as defined in the db. 
		$_POST['title'] = validate_length($_POST['title'], 64);
		$_POST['description'] = validate_length($_POST['description'], 250);
		
		$name = get_display_name($_SESSION['member_id']);
		$email = '';

		$approved = intval($_POST['approved']);
	
		queryDB('INSERT INTO %slinks VALUES (DEFAULT, %d, "%s", "%s", "%s", %d, "%s", "%s", NOW(), DEFAULT)',
		      array(TABLE_PREFIX, $cat_id, $_POST['url'], $_POST['title'], $_POST['description'], $approved, $name, $email));
	
		$msg->addFeedback('LINK_ADDED');

		header('Location: '.AT_BASE_HREF.'mods/_standard/links/tools/index.php');
		exit;
	}
}

if (!isset($_POST['url'])) {
	$_POST['url'] = 'http://';
}

$categories = get_link_categories(true);

if (empty($categories)) {
	$msg->addError('LINK_CAT_EMPTY');
	header('Location: '.AT_BASE_HREF.'mods/_standard/links/tools/index.php');
	exit;
}

$onload = 'document.form.title.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');

$msg->printErrors();

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="add_link" value="true" />

<div class="input-form">
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="title"><?php echo _AT('title'); ?></label><br />
		<input type="text" name="title" size="40" id="title" value="<?php echo $_POST['title']; ?>"/>
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
			select_link_categories($categories, 0, $_POST['cat'], FALSE, '', TRUE);
			?>
		</select>
	</div>
	
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="url"><?php echo _AT('url'); ?></label><br />
		<input type="text" name="url" size="40" id="url" value="<?php echo AT_print($_POST['url'], 'input.text'); ?>" />
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="description"><?php echo _AT('description'); ?></label><br />
		<textarea name="description" cols="55" rows="5" id="description" style="width:90%;"><?php echo AT_print($_POST['description'], 'input.text'); ?></textarea>
	</div>

	<div class="row">
		<?php echo _AT('approve'); ?><br />
		<?php
			if ($_POST['approved']) {
				$y = 'checked="checked"';
				$n = '';
			} else if (isset ($_POST['approved'])) {
				$n = 'checked="checked"';
				$y = '';
			} else {
				$y = 'checked="checked"';
				$n = '';
			}
		?>
		<input type="radio" id="yes" name="approved" value="1" <?php echo $y; ?>><label for="yes"><?php echo _AT('yes'); ?></label>  <input type="radio" id="no" name="approved" value="0" <?php echo $n; ?>><label for="no"><?php echo _AT('no'); ?></label>
	</div>
	
	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?> " />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>