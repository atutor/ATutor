<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id: create_category.php 3362 2005-02-18 15:31:12Z joel $

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require (AT_INCLUDE_PATH.'lib/links.inc.php');

if (isset($_POST['submit'])) {
	$cat_parent_id  = intval($_POST['cat_parent_id']);
	$cat_name       = trim($_POST['cat_name']);
	$cat_name		= $addslashes($cat_name);

	$sql = "INSERT INTO ".TABLE_PREFIX."resource_categories VALUES (0, $_SESSION[course_id], '$cat_name', $cat_parent_id)";
	$result = mysql_query($sql, $db);

	$msg->addFeedback('CAT_ADDED');
	
	header('Location: categories.php');
	exit;

} else if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: categories.php');
	exit;
}

/* $categories[category_id] = array(cat_name, cat_parent, num_courses, [array(children)]) */
$categories = get_link_categories();

require(AT_INCLUDE_PATH.'header.inc.php'); 
$msg->printAll();

?>

<form action ="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">

<div class="input-form">
	<div class="row">
		<label for="category_name"><?php echo _AT('cats_category_name'); ?></label><br />
		<input type="text" id="category_name" name="cat_name" value="<?php echo stripslashes(htmlspecialchars($categories[$cat_id]['cat_name'])); ?>" />
	</div>

	<div class="row">
		<label for="category_parent"><?php echo _AT('cats_parent_category'); ?></label><br />
		<select name="cat_parent_id" id="category_parent"><?php

				if ($pcat_id) {
					$current_cat_id = $pcat_id;
					$exclude = false; /* don't exclude the children */
				} else {
					$current_cat_id = $cat_id;
					$exclude = true; /* exclude the children */
				}

				echo '<option value="0"></option>';

				/* @See: include/lib/admin_categories */
				select_link_categories($categories, 0, $current_cat_id, $exclude);
			?></select>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"  />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>