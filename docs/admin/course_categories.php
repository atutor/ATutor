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
$section = 'users';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/admin_categories.inc.php');

if (!$_SESSION['s_is_super_admin']) { exit; }

if (isset($_POST['submit'])) {
	/* insert or update a category */
	$cat_id			= intval($_POST['cat_id']);
	$cat_parent_id  = intval($_POST['cat_parent_id']);
	$cat_name = trim($_POST['cat_name']);

	if ($cat_id == 0) {
		$sql = "INSERT INTO ".TABLE_PREFIX."course_cats VALUES (0, '$cat_name', $cat_parent_id)";
		$result = mysql_query($sql, $db);
		$cat_id = mysql_insert_id($db);
		$f   = AT_FEEDBACK_CAT_ADDED;
	} else {
		$sql = "UPDATE ".TABLE_PREFIX."course_cats SET cat_parent=$cat_parent_id, cat_name='$cat_name' WHERE cat_id=$cat_id";
		$result = mysql_query($sql, $db);
		$f = AT_FEEDBACK_CAT_UPDATE_SUCCESSFUL;
	}

	header('Location: course_categories.php?cat_id='.$cat_id.SEP.'f='.urlencode_feedback($f));
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

		header('Location: course_categories.php?f='.urlencode_feedback(AT_FEEDBACK_CAT_DELETED));
		exit;
	}
}

/* get all the categories: */
/* $categories[category_id] = array(cat_name, cat_parent, num_courses, [array(children)]) */
$sql = "SELECT * FROM ".TABLE_PREFIX."course_cats ORDER BY cat_parent, cat_name";
$result = mysql_query($sql, $db);
while ($row = mysql_fetch_assoc($result)) {
	$categories[$row['cat_id']]['cat_name']    = $row['cat_name'];
	$categories[$row['cat_id']]['cat_parent']  = $row['cat_parent'];
	$categories[$row['cat_id']]['num_courses'] = 0;

	if ($row['cat_parent'] >0) {
		$categories[$row['cat_parent']]['children'][] = $row['cat_id'];
	} else {
		$categories[0][] = $row['cat_id'];
	}
}

/* get the number of courses in each category */
/* special case: uncategorized courses get stored in $num_uncategorized */
$sql = "SELECT cat_id, COUNT(*) AS cnt FROM ".TABLE_PREFIX."courses GROUP BY cat_id";
$result = mysql_query($sql, $db);
while ($row = mysql_fetch_assoc($result)) {
	if ($row['cat_id'] == 0) {
		$num_uncategorized = $row['cnt'];
	} else {
		$categories[$row['cat_id']]['num_courses'] = $row['cnt'];
	}
}

if (isset($_GET['cat_id'])) {
	$cat_id = intval($_GET['cat_id']);
}
if (isset($_GET['pcat_id'])) {
	$pcat_id = intval($_GET['pcat_id']);
}

require(AT_INCLUDE_PATH.'admin_html/header.inc.php');
echo '<h2>'._AT('cats_course_categories').'</h2>';
echo '<a href="'.$_SERVER['PHP_SELF'].'">'._AT('cats_add_categories').'</a><br /><br />';
?>
<table cellspacing="0" cellpadding="0" border="0" summary="" align="center" width="100%">
<tr>
	<td valign="top"><?php

			echo '<p><small>Select a Category below to edit:</small></p>';

			/* print the list of nested categories */
			/* @See: include/lib/admin_categories */
			if (is_array($categories)) {
				print_categories($categories, 0);
			} else {
				print_infos(AT_INFOS_NO_CATEGORIES);
			}
			if ($num_uncategorized > 0) {
				echo '<br /><p><small>Uncategorized Courses: '.$num_uncategorized.'.</small></p>';
			}
	?></td>
	<td valign="top"><?php 
			/* print the category editor */
			require(AT_INCLUDE_PATH.'html/cat_editor.inc.php');
		?></td>
</tr>
</table>

<?php
require(AT_INCLUDE_PATH.'admin_html/footer.inc.php');
?>