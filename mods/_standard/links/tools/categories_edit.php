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
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_standard/links/lib/links.inc.php');

if (!manage_links()) {
	$msg->addError('ACCESS_DENIED');
	header('Location: '.AT_BASE_HREF.'mods/_standard/links/index.php');
	exit;
}

$cat_id = intval($_REQUEST['cat_id']);

if (isset($_POST['submit'])) {

	//check if cat name is empty
	if ($_POST['cat_name'] == '') {
		$msg->addError(array('EMPTY_FIELDS', _AT('title')));
	}

	if (!$msg->containsErrors()) {
		//authorized cat parent?
		$lid = explode('-', $_POST['cat_parent_id']);
		$parent_id = intval($lid[0]);
		$owner_type = intval($lid[1]);
		$owner_id = intval($lid[2]);

		if (!links_authenticate($owner_type, $owner_id)) {
			$msg->addError('ACCESS_DENIED');
			header('Location: '.AT_BASE_HREF.'mods/_standard/links/tools/categories.php');
			exit;
		}
		
		//Check length of the post, if it's exceeded 100 as defined in the db. 
		$cat_name = validate_length($cat_name, 100);

		queryDB('UPDATE %slinks_categories SET parent_id=%d, name="%s", owner_type=%s, owner_id=%d WHERE cat_id=%d',
		              array(TABLE_PREFIX, $parent_id, $cat_name, $owner_type, $owner_id, $cat_id));
		
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
		header('Location: '.AT_BASE_HREF.'mods/_standard/links/tools/categories.php');
		exit;
	}
}

/* get all the categories: */
/* $categories[category_id] = array(cat_name, cat_parent, num_courses, [array(children)]) */
$categories = get_link_categories(true);

$onload = 'document.form.category_name.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');

$current_cat_id = $categories[$cat_id]['cat_parent'];

?>
<form action ="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="cat_id" value="<?php echo $cat_id; ?>" />
<input type="hidden" name="form_submit" value="1" />

<div class="input-form">
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="category_name"><?php echo _AT('title'); ?></label><br />
		<input type="text" id="category_name" name="cat_name" value="<?php echo htmlspecialchars($categories[$cat_id]['cat_name']); ?>" />
	</div>

	<div class="row">
		<label for="category_parent"><?php echo _AT('cats_parent_category'); ?></label><br />
		<select name="cat_parent_id" id="category_parent"><?php
				$exclude = true; /* exclude the children */
				
				//remove the current cat_id and it's sub cats from list, don't want to print them out.
				if (is_array($categories[$current_cat_id]['children'])) {
					foreach ($categories[$current_cat_id]['children'] as $id=>$child) {
						if ($child == $cat_id) {
							unset($categories[$current_cat_id]['children'][$id]);
						}
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