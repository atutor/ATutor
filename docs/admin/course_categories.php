<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/

$page = 'categories';
$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
if ($_SESSION['course_id'] > -1) { exit; }

require(AT_INCLUDE_PATH.'lib/admin_categories.inc.php');
require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

if (isset($_POST['form_submit']) && !isset($_POST['delete']) && !isset($_POST['cancel'])) {
	/* insert or update a category */
	$cat_id			= intval($_POST['cat_id']);
	$cat_parent_id  = intval($_POST['cat_parent_id']);
	$cat_name       = trim($_POST['cat_name']);

	if ($cat_id == 0) {
		$cat_name  = $addslashes($cat_name);
		$cat_theme = $addslashes($_POST['cat_theme']);

		if ($_POST['theme_parent']) {
			$sql	= "SELECT theme FROM ".TABLE_PREFIX."course_cats WHERE cat_id=$cat_parent_id";
			$result = mysql_query($sql, $db);
			if ($row = mysql_fetch_assoc($result)) {
				$cat_theme = $row['theme'];
			}
		}

		$sql = "INSERT INTO ".TABLE_PREFIX."course_cats VALUES (0, '$cat_name', $cat_parent_id, '$cat_theme')";
		$result = mysql_query($sql, $db);
		$cat_id = mysql_insert_id($db);
		$msg->addFeedback('CAT_ADDED');
	} else {
		$cat_name = $addslashes($_POST['cat_name']);
		$cat_theme = $addslashes($_POST['cat_theme']);

		if ($_POST['theme_parent']) {
			// get the theme of the parent category.

			$sql	= "SELECT theme FROM ".TABLE_PREFIX."course_cats WHERE cat_id=$cat_parent_id";
			$result = mysql_query($sql, $db);
			if ($row = mysql_fetch_assoc($result)) {
				$cat_theme = $row['theme'];
			}
		}
		if ($_POST['theme_children']) {
			// apply this theme to all the sub-categories recursively.
			$children = recursive_get_subcategories($cat_id);
			$children = implode(',', $children);

			if ($children) {
				$sql = "UPDATE ".TABLE_PREFIX."course_cats SET theme='$cat_theme' WHERE cat_id IN ($children)";
				$result = mysql_query($sql, $db);
			}
		}

		$sql = "UPDATE ".TABLE_PREFIX."course_cats SET cat_parent=$cat_parent_id, cat_name='$cat_name', theme='$cat_theme' WHERE cat_id=$cat_id";
		$result = mysql_query($sql, $db);
		$msg->addFeedback('CAT_UPDATE_SUCCESSFUL');
	}

	header('Location: course_categories.php?cat_id='.$cat_id);
	exit;
} else if (isset($_POST['delete'])) {
	/* want to delete a cat, next step: confirmation */
	$cat_id	= intval($_POST['cat_id']);
} else if (isset($_GET['d'])) {
	/* delete has been confirmed, delete this category */
	$cat_id	= intval($_GET['cat_id']);
	if (!is_array($categories[$cat_id]['children'])) {
		$sql = "DELETE FROM ".TABLE_PREFIX."course_cats WHERE cat_id=$cat_id";
		$result = mysql_query($sql, $db);

		$sql = "UPDATE ".TABLE_PREFIX."courses SET cat_id=0 WHERE cat_id=$cat_id";
		$result = mysql_query($sql, $db);

		$msg->addFeedback('CAT_DELETED');
		header('Location: course_categories.php');
		exit;
	}
} else if (isset($_POST['cancel'])) {
	unset($_REQUEST);
}

/* get all the categories: */
/* $categories[category_id] = array(cat_name, cat_parent, num_courses, [array(children)]) */
$categories = get_categories();

/* get the number of courses in each category */
/* special case: uncategorized courses get stored in $num_uncategorized */
$num_uncategorized = assign_categories_course_count($categories);


if (isset($_GET['cat_id'])) {
	$cat_id = intval($_GET['cat_id']);
}
if (isset($_GET['pcat_id'])) {
	$pcat_id = intval($_GET['pcat_id']);
}

require(AT_INCLUDE_PATH.'header.inc.php'); 
echo '<h3>'._AT('cats_course_categories').'</h3>';

/*
if (isset($_GET['f'])) { 
	$f = intval($_GET['f']);
	if ($f <= 0) {
		/* it's probably an array 
		$f = unserialize(urldecode($_GET['f']));
	}
	print_feedback($f);
}
if (isset($errors)) { print_errors($errors); }
if(isset($warnings)){ print_warnings($warnings); }
*/
$msg->printAll();

echo '<a href="'.$_SERVER['PHP_SELF'].'">'._AT('cats_add_categories').'</a><br /><br />';
?>
<table cellspacing="0" cellpadding="0" border="0" summary="" align="center" width="100%">
<tr>
	<td valign="top"><?php

			echo '<p><small>'._AT('select_category_to_edit').'</small></p>';

			/* print the list of nested categories */
			/* @See: include/lib/admin_categories */
			if (is_array($categories)) {
				print_categories($categories, 0);
			} else {
				$msg->printInfos('NO_CATEGORIES');
			}
			if ($num_uncategorized > 0) {
				echo '<br /><p><small>'._AT('uncategorized_courses').' '.$num_uncategorized.'.</small></p>';
			}
	?></td>
	<td valign="top"><?php 
			/* print the category editor */
			require(AT_INCLUDE_PATH.'html/cat_editor.inc.php');
		?></td>
</tr>
</table>

<?php
require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>