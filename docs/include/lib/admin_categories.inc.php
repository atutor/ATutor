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
if (!defined('AT_INCLUDE_PATH')) { exit; }

/* prints out the given $categories as an HTML list */
/* $categories: categories given, where the key is the cat_id */
/* $cat_id: the current category id */
function print_categories($categories, $cat_id) {
	if ($cat_id == 0) {
		echo '<ul>';
		foreach($categories[0] as $child_cat_id) {
			print_categories($categories, $child_cat_id);
		}
		echo '</ul>';
	} else {
		echo '<li>';
		if ($cat_id == $_REQUEST['cat_id']) {
			echo '<strong>'.$categories[$cat_id]['cat_name'].'</strong>';
		} else if ($cat_id == $_REQUEST['pcat_id']) {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?cat_id='.$cat_id.'"><b>'.$categories[$cat_id]['cat_name'].'</b></a>';
		} else {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?cat_id='.$cat_id.'">'.$categories[$cat_id]['cat_name'].'</a>';
		}
		echo ' <small class="spacer">('.$categories[$cat_id]['num_courses'].' ';
		if ($categories[$cat_id]['num_courses'] == 1) {
			echo _AT('course');
		} else {
			echo _AT('courses');
		}
		
		echo ')</small>';
		if (is_array($categories[$cat_id]['children'])) {
			echo '<ul>';
			foreach($categories[$cat_id]['children'] as $child_cat_id) {
				print_categories($categories, $child_cat_id);
			}
			echo '</ul>';
		}
		echo '</li>';
	}
}

/* generates a <select> of the given $categories */
/* $cat_id: the current cat id to start the traversal */
/* $current_cat_id: the current category id, will be set to "selected" if $exclude is false o/w the parent will be selected */
/* $exclude: whether or not the children of $current_cat_id should be excluded or not. */
/* $depth: just keeps track of how deep the $cat_id is */
function select_categories($categories, $cat_id, $current_cat_id, $exclude, $depth=0) {
	if ($cat_id == 0 && is_array($categories[0])) {
		foreach($categories[0] as $child_cat_id) {
			select_categories($categories, $child_cat_id, $current_cat_id, $exclude);
		}
	} else {
		if ($exclude && ($cat_id == $current_cat_id)) {
			return;
		}
		echo '<option value="'.$cat_id.'"';

		if ($exclude && is_array($categories[$cat_id]['children']) && in_array($current_cat_id, $categories[$cat_id]['children'])) {
			echo ' selected="selected"';
		} else if (!$exclude && ($cat_id == $current_cat_id)) {
			echo ' selected="selected"';
		}
		echo '>';
		echo str_repeat("&nbsp;", $depth*4);
		echo $categories[$cat_id]['cat_name'].'</option>';

		if (is_array($categories[$cat_id]['children'])) {
			foreach($categories[$cat_id]['children'] as $child_cat_id) {
				select_categories($categories, $child_cat_id, $current_cat_id, $exclude, $depth+1);
			}
		}
	}
}

function get_categories() {
	global $db;

	/* get all the categories: */
	/* $categories[category_id] = array(cat_name, cat_parent, num_courses, [array(children)]) */
	$sql = "SELECT * FROM ".TABLE_PREFIX."course_cats ORDER BY cat_parent, cat_name";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$categories[$row['cat_id']]['cat_name']    = $row['cat_name'];
		$categories[$row['cat_id']]['cat_parent']  = $row['cat_parent'];
		$categories[$row['cat_id']]['num_courses'] = 0;
		$categories[$row['cat_id']]['theme']       = $row['theme'];

		if ($row['cat_parent'] >0) {
			$categories[$row['cat_parent']]['children'][] = $row['cat_id'];
		} else {
			$categories[0][] = $row['cat_id'];
		}
	}
	return $categories;
}

/* assigns the 'num_courses' field in the $categories array */
/* returns the number of uncategorized courses */
function assign_categories_course_count(&$categories) {
	global $db;

	$num_uncategorized = 0;

	$sql = "SELECT cat_id, COUNT(*) AS cnt FROM ".TABLE_PREFIX."courses GROUP BY cat_id";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		if ($row['cat_id'] == 0) {
			$num_uncategorized = $row['cnt'];
		} else {
			$categories[$row['cat_id']]['num_courses'] = $row['cnt'];
		}
	}

	return $num_uncategorized;
}

/* applies $theme to all the sub-categories recursively. */
/* returns an array of all the subcategories */
function recursive_get_subcategories($category_parent_id) {
	static $categories;
	if (!isset($categories)) {
		$categories = get_categories();
	}

	$children = array();
	if (is_array($categories[$category_parent_id]['children'])) {
		$children = $categories[$category_parent_id]['children'];
		foreach ($categories[$category_parent_id]['children'] as $category_child_id) {
			if ($category_child_id) {
				$children =  array_merge($children, recursive_get_subcategories($category_child_id));
			}
		}
	}
	return $children;
}

?>