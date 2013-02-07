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

$lid = explode('-', $_REQUEST['lid']);
$link_id = intval($lid[0]);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'mods/_standard/links/tools/index.php');
	exit;
} else if (isset($_POST['edit_link']) && isset($_POST['submit'])) {

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
		//Check length of the post, if it's exceeded 64 as defined in the db. 
		$_POST['title'] = validate_length($_POST['title'], 64);
		$_POST['description'] = validate_length($_POST['description'], 250);
//		$name = get_display_name($_SESSION['member_id']);
		$email = '';

		//check if new cat is auth? -- shouldn't be a prob. since cat dropdown is already filtered

		queryDB('UPDATE %slinks SET cat_id=%d, Url="%s", LinkName="%s", Description="%s", Approved=%s WHERE link_id=%d', array(TABLE_PREFIX, $_POST['cat'], $_POST['url'], $_POST['title'], $_POST['description'], $_POST['approved'], $link_id));
	
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

		header('Location: '.AT_BASE_HREF.'mods/_standard/links/tools/index.php');
		exit;
	}
} else {
	$row = queryDB('SELECT * FROM %slinks WHERE link_id=%d', array(TABLE_PREFIX, $link_id), true);
	if (!empty($row)) {

		//auth based on the link's cat
		$cat_row = get_cat_info($row['cat_id']);

		if (!links_authenticate($cat_row['owner_type'], $cat_row['owner_id'])) {
			$msg->addError('ACCESS_DENIED');
			header('Location: '.AT_BASE_HREF.'mods/_standard/links/tools/index.php');
			exit;
		}

		$_POST['title']			= $row['LinkName'];
		$_POST['cat']			= $row['cat_id'];
		$_POST['url']			= $row['Url'];
		$_POST['description']	= $row['Description'];
		$_POST['approved']		= $row['Approved'];
	}
}

$onload = 'document.form.title.focus();';
require(AT_INCLUDE_PATH.'header.inc.php');

$categories = get_link_categories(true);

$msg->printErrors();

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="edit_link" value="true" />
<input type="hidden" name="lid" value="<?php echo $_REQUEST['lid']; ?>" />

<div class="input-form">
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="title"><?php echo _AT('title'); ?></label><br />
		<input type="text" name="title" size="40" id="title" value="<?php echo $_POST['title']; ?>"/>
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="cat"><?php echo _AT('category'); ?></label><br />
		<select name="cat" id="cat">
			<?php select_link_categories($categories, 0, $_POST['cat'], FALSE);	?>
		</select>
	</div>
	
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="url"><?php echo _AT('url'); ?></label><br />
		<input type="text" name="url" size="40" id="url" value="<?php echo $_POST['url']; ?>" />
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="description"><?php echo _AT('description'); ?></label><br />
		<textarea name="description" cols="55" rows="5" id="description" ><?php echo $_POST['description']; ?></textarea>
	</div>

	<div class="row">
		<?php echo _AT('approve'); ?><br />
		<?php
			if ($_POST['approved']) {
				$y = 'checked="checked"';
				$n = '';
			} else {
				$n = 'checked="checked"';
				$y = '';
			}
		?>
		<input type="radio" id="yes" name="approved" value="1" <?php echo $y; ?> /><label for="yes"><?php echo _AT('yes'); ?></label>  <input type="radio" id="no" name="approved" value="0" <?php echo $n; ?> /><label for="no"><?php echo _AT('no'); ?></label>
	</div>
	
	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?> " />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>