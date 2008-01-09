<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require (AT_INCLUDE_PATH.'lib/links.inc.php');

if (!manage_links()) {
	$msg->addError('ACCESS_DENIED');
	header('Location: '.AT_BASE_HREF.'links/index.php');
	exit;
}

if (isset($_POST['submit'])) {
	$cat_parent_id  = intval($_POST['cat_parent_id']);
	$cat_name       = trim($_POST['cat_name']);
	$cat_name		= $addslashes($cat_name);

	if ($cat_name == '') {
		$msg->addError(array('EMPTY_FIELDS', _AT('title')));
	}

	if (!$msg->containsErrors()) {

		if (!empty($cat_parent_id)) {
			$cat_parent_id = explode('-', $_POST['cat_parent_id']);
			$parent_id = intval($cat_parent_id[0]);
			$owner_type = intval($cat_parent_id[1]);
			$owner_id = intval($cat_parent_id[2]);

			if (!links_authenticate($owner_type, $owner_id)) {
				$msg->addError('ACCESS_DENIED');
				header('Location: '.AT_BASE_HREF.'index.php');
				exit;
			}
		} else {
			$owner_type = LINK_CAT_COURSE;
			$owner_id = $_SESSION['course_id'];
			$parent_id = 0;
		}

		//Check length of the post, if it's exceeded 100 as defined in the db. 
		if ($strlen($cat_name) > 100){
			$cat_name = $substr($cat_name, 0, 100);
		}

		$sql = "INSERT INTO ".TABLE_PREFIX."links_categories VALUES (NULL, $owner_type, $owner_id, '$cat_name', $parent_id)";
		$result = mysql_query($sql, $db);

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		
		header('Location: categories.php');
		exit;
	}
} else if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: categories.php');
	exit;
}


/* $categories[category_id] = array(cat_name, cat_parent, num_courses, [array(children)]) */
$categories = get_link_categories(true);

$onload = 'document.form.category_name.focus();';

require(AT_INCLUDE_PATH.'header.inc.php'); 
$msg->printAll();

?>

<form action ="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">

<div class="input-form">
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="category_name"><?php echo _AT('title'); ?></label><br />
		<input type="text" id="category_name" name="cat_name" value="<?php echo stripslashes(htmlspecialchars($categories[$cat_id]['cat_name'])); ?>" />
	</div>

	<div class="row">
		<label for="category_parent"><?php echo _AT('cats_parent_category'); ?></label><br />
		<?php if ($categories): ?>
			<select name="cat_parent_id" id="category_parent">
			<?php
				if ($pcat_id) {
					$current_cat_id = $pcat_id;
					$exclude = false; /* don't exclude the children */
				} else {
					$current_cat_id = $cat_id;
					$exclude = true; /* exclude the children */
				}
				
				$auth = manage_links();
				if ($auth == LINK_CAT_AUTH_ALL) {
					echo '<option value="0"></option>';
				}

				select_link_categories($categories, 0, $current_cat_id, $exclude, 0, TRUE); 
			?>
			</select>
		<?php else: 
			echo _AT('cats_no_categories');
		endif; ?>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"  />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>