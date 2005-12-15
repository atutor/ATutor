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

function get_link_categories() {
	global $db;

	/* get all the categories: */
	/* $categories[category_id] = array(cat_name, cat_parent, num_courses, [array(children)]) */
	$sql = "SELECT * FROM ".TABLE_PREFIX."resource_categories WHERE course_id=$_SESSION[course_id] ORDER BY CatParent, CatName";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$categories[$row['CatID']]['cat_name']    = $row['CatName'];
		$categories[$row['CatID']]['cat_parent']  = $row['CatParent'];

		if ($row['CatParent'] >0) {
			$categories[$row['CatParent']]['children'][] = $row['CatID'];
		} else {
			$categories[0][] = $row['CatID'];
		}
	}
	return $categories;
}

function select_link_categories($categories, $cat_id, $current_cat_id, $exclude, $depth=0) {
	if ($cat_id == 0 && is_array($categories[0])) {
		foreach($categories[0] as $child_cat_id) {
			select_link_categories($categories, $child_cat_id, $current_cat_id, $exclude);
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
				select_link_categories($categories, $child_cat_id, $current_cat_id, $exclude, $depth+1);
			}
		}
	}
}

/**
 Given a $cat_id, return IDs of all children of that ID as a comma seperated 
 string.
 */
function get_child_categories ($cat_id, $categories) {
    if (!isset ($categories)) {
        $categories = get_link_categories();
    }
    
    $category = $categories[$cat_id];
    $children_string = "";
    if (is_array($categories[$cat_id]['children'])){
        foreach ($categories[$cat_id]['children'] as $child) {
            $children_string = $child.",";
        }
    }
    return $children_string;
}

?>