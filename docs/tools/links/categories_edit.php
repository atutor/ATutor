<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
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
require(AT_INCLUDE_PATH.'lib/links.inc.php');

if (!manage_links()) {
	$msg->addError('ACCESS_DENIED');
	header('Location: '.$_base_href.'links/index.php');
	exit;
}

$cat_id = intval($_REQUEST['cat_id']);

if (isset($_POST['submit'])) {

	//check if cat name is empty
	if ($_POST['cat_name'] == '') {
		$msg->addError('TITLE_EMPTY');
	}

	if (!$msg->containsErrors()) {
		//authorized cat parent?
		$lid = explode('-', $_POST['cat_parent_id']);
		$parent_id = intval($lid[0]);
		$owner_type = intval($lid[1]);
		$owner_id = intval($lid[2]);

		if (!links_authenticate($owner_type, $owner_id)) {
			$msg->addError('ACCESS_DENIED');
			header('Location: '.$_base_href.'tools/links/categories.php');
			exit;
		}

		$cat_name = $addslashes($_POST['cat_name']);

		$sql = "UPDATE ".TABLE_PREFIX."links_categories SET parent_id=$parent_id, name='$cat_name', owner_type=$owner_type, owner_id=$owner_id WHERE cat_id=".$cat_id;

		$result = mysql_query($sql, $db);
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

		header('Location: categories.php');
		exit;
	}
} else if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: categories.php');
	exit;
} else {
	$row = get_cat_info($cat_id);

	//authorized to edit this cat?
	if (!links_authenticate($row['owner_type'], $row['owner_id'])) {
		$msg->addError('ACCESS_DENIED');
		header('Location: '.$_base_href.'tools/links/categories.php');
		exit;
	}
}

/* get all the categories: */
/* $categories[category_id] = array(cat_name, cat_parent, num_courses, [array(children)]) */
$categories = get_link_categories(true);

$onload = 'document.form.category_name.focus();';

require(AT_INCLUDE_PATH.'header.inc.php'); 
?>
<form action ="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="cat_id" value="<?php echo $cat_id; ?>" />
<input type="hidden" name="form_submit" value="1" />

<div class="input-form">
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="category_name"><?php echo _AT('cats_category_name'); ?></label><br />
		<input type="text" id="category_name" name="cat_name" value="<?php echo stripslashes(htmlspecialchars($categories[$cat_id]['cat_name'])); ?>" />
	</div>

	<div class="row">
		<label for="category_parent"><?php echo _AT('cats_parent_category'); ?></label><br />
		<select name="cat_parent_id" id="category_parent"><?php
				$current_cat_id = $categories[$cat_id]['cat_parent'];
				$exclude = true; /* exclude the children */
				
				//remove the current cat_id and it's sub cats from list, don't want to print them out.
				foreach ($categories[$current_cat_id]['children'] as $id=>$child) {
					if ($child == $cat_id) {
						unset($categories[$current_cat_id]['children'][$id]);
					}
				}
				unset($categories[$cat_id]);

				$auth = manage_links();
				if ($auth == LINK_CAT_AUTH_ALL) {
					echo '<option value="0-'.LINK_CAT_COURSE.'-'.$_SESSION['course_id'].'">&nbsp;&nbsp;&nbsp;[ '._AT('cats_none').' ]&nbsp;&nbsp;&nbsp;</option>';
					echo '<option value="0-'.LINK_CAT_COURSE.'-'.$_SESSION['course_id'].'"></option>';
				}
				select_link_categories($categories, 0, $current_cat_id, $exclude, 0, TRUE);
			?></select>
	</div>
	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"  />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>