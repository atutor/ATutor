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

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/themes.inc.php');
admin_authenticate(AT_ADMIN_PRIV_CATEGORIES);

require(AT_INCLUDE_PATH.'lib/admin_categories.inc.php');

$cat_id = intval($_REQUEST['cat_id']);

if (isset($_POST['submit'])) {
	$cat_name = $addslashes($_POST['cat_name']);
	$cat_theme = $addslashes($_POST['cat_theme']);
	$cat_parent_id = intval($_POST['cat_parent_id']);

	if ($_POST['theme_parent']) {
		// get the theme of the parent category.

		$sql	= "SELECT theme FROM ".TABLE_PREFIX."course_cats WHERE cat_id=$cat_parent_id";
		$result = mysql_query($sql, $db);
		if ($row = mysql_fetch_assoc($result)) {
			$cat_theme = $row['theme'];
		}
	}
	if ($cat_name == '') {
		$msg->addError('TITLE_EMPTY');
	}

	if (!$msg->containsErrors()) {

		if ($_POST['theme_children']) {
			// apply this theme to all the sub-categories recursively.
			$children = recursive_get_subcategories($cat_id);
			$children = implode(',', $children);

			if ($children) {
				$sql = "UPDATE ".TABLE_PREFIX."course_cats SET theme='$cat_theme' WHERE cat_id IN ($children)";
				$result = mysql_query($sql, $db);

				write_to_log(AT_ADMIN_LOG_UPDATE, 'course_cats', mysql_affected_rows($db), $sql);
			}
		}

		$sql = "UPDATE ".TABLE_PREFIX."course_cats SET cat_parent=$cat_parent_id, cat_name='$cat_name', theme='$cat_theme' WHERE cat_id=$cat_id";
		$result = mysql_query($sql, $db);

		write_to_log(AT_ADMIN_LOG_UPDATE, 'course_cats', mysql_affected_rows($db), $sql);

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

		header('Location: course_categories.php');
		exit;
	}
} else if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: course_categories.php');
	exit;
}

/* get all the categories: */
/* $categories[category_id] = array(cat_name, cat_parent, num_courses, [array(children)]) */
$categories = get_categories();

require(AT_INCLUDE_PATH.'header.inc.php'); 
$msg->printAll();

?>

<form action ="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="cat_id" value="<?php echo $cat_id; ?>" />
<input type="hidden" name="form_submit" value="1" />

<div class="input-form">
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="category_name"><?php echo _AT('title'); ?></label><br />
		<input type="text" id="category_name" name="cat_name" size="30" value="<?php echo htmlspecialchars($categories[$cat_id]['cat_name']); ?>" />
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="category_parent"><?php echo _AT('cats_parent_category'); ?></label><br />
		<select name="cat_parent_id" id="category_parent"><?php

				$current_cat_id = $cat_id;
				$exclude = true; /* exclude the children */
				echo '<option value="0">&nbsp;&nbsp;&nbsp;[ '._AT('cats_none').' ]&nbsp;&nbsp;&nbsp;</option>';
				echo '<option value="0"></option>';

				/* @See: include/lib/admin_categories */
				select_categories($categories, 0, $current_cat_id, $exclude);
			?></select>
	</div>


<?php if (defined('AT_ENABLE_CATEGORY_THEMES') && AT_ENABLE_CATEGORY_THEMES) : ?>
	<div class="row">
		<label for="category_theme"><?php echo _AT('cat_theme'); ?></label><br />
		<select name="cat_theme" id="category_theme"><?php

				echo '<option value="0">&nbsp;&nbsp;&nbsp;[ '._AT('cats_none').' ]&nbsp;&nbsp;&nbsp;</option>';

				$_themes = get_enabled_themes();
				foreach ($_themes as $theme) {
					$theme = trim($theme);
					$theme_dir = get_folder($theme);
					$theme_info = get_themes_info($theme_dir);
					if ($theme_info['dir_name'] == $categories[$cat_id]['theme']) {
						echo '<option value="'.$theme_info['dir_name'].'" selected="selected">'.$theme.'</option>';
					} else {
						echo '<option value="'.$theme_info['dir_name'].'">'.$theme.'</option>';
					}
				}

			?></select>
			<?php if ($cat_id && is_array($categories[$cat_id]['children']) && count($categories[$cat_id]['children'])): ?>
				<br />
				<input type="checkbox" name="theme_children" id="theme_children" value="1" /><label for="theme_children"><?php echo _AT('apply_theme_subcategories'); ?></label>
			<?php endif; ?>
			<?php if ($categories[$cat_id]['cat_parent']): ?>
				<br />
				<input type="checkbox" name="theme_parent" id="theme_parent" value="1" /><label for="theme_parent"><?php echo _AT('use_parent_theme'); ?></label>
			<?php endif; ?>
			<br /><br />
	</div>
<?php endif; ?>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"  />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>