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


/**
 * given an owner_type and owner_id
 * returns false if user cannot manage link of owner_type
 * returns true if they can
 */
function links_authenticate($owner_type, $owner_id) {
	if (empty($owner_type) || empty($owner_id)) {
		return false;
	}

	//if admin or TA w/ right privs, can manage all links
	if (authenticate(AT_PRIV_GROUPS+AT_PRIV_LINKS, true)) {
		return true;
	}

	if ($owner_type == LINK_CAT_GROUP) {
		//check if member of group
		if ($_SESSION['valid_user'] === true && isset($_SESSION['groups'])) {			
			$row = queryDB('SELECT * FROM %sgroups_members WHERE group_id=%d AND member_id=%d', array(TABLE_PREFIX, $owner_id, $_SESSION['member_id']), true);
			if (!empty($row)) {
				return true;
			}
		} 
	} 

	return false;
}

/* return true if user is able to manage group or course links */
function manage_links() {
	if (authenticate(AT_PRIV_GROUPS, true) && authenticate(AT_PRIV_LINKS, true)) { //course and group links
		return LINK_CAT_AUTH_ALL;
	} else if (authenticate(AT_PRIV_GROUPS, true)) { //all group links
		return LINK_CAT_AUTH_GROUP;
	} else if (authenticate(AT_PRIV_LINKS, true)) { //course links
		return LINK_CAT_AUTH_COURSE;
	} else if (!empty($_SESSION['groups'])) { //particular group links
		//find a group that uses links
		foreach ($_SESSION['groups'] as $group_id) {
			$row = queryDB('SELECT modules FROM %sgroups WHERE group_id=%d', array(TABLE_PREFIX, $group_id), true);
			$mods = explode('|', $row['modules']);

			if (in_array("_standard/links", $mods)) {
				return LINK_CAT_AUTH_GROUP;
			}
		}
		return FALSE;
	}
	return LINK_CAT_AUTH_NONE;
}


//if manage, then it's getting categories for only those that should see them
//if list, then filter out the uneditable group cats from the manage list (otherwise it's a dropdown of cats)
function get_link_categories($manage=false, $list=false) {
	global $_base_path;
	$categories = array();
	$course_id = $_SESSION['course_id'];

	/* get all the categories: */
	/* $categories[category_id] = array(cat_name, cat_parent, num_courses, [array(children)]) */

	if ($_SESSION['groups']) {
		$groups = implode(',', $_SESSION['groups']);
	} else {
		// not in any groups
		$groups = 0;
	}

	$sql = '';
	$sqlParams = array();
	//if suggest a link page
	if ($_SERVER['PHP_SELF'] == $_base_path.'mods/links/add.php') {
		$sql = 'SELECT * FROM %slinks_categories WHERE (owner_id=%d AND owner_type=%d) ORDER BY parent_id, name';
		array_push($sqlParams, TABLE_PREFIX, $course_id, LINK_CAT_COURSE);
	} else if ($manage) {
		$sql = 'SELECT * FROM %slinks_categories WHERE ';
		array_push($sqlParams, TABLE_PREFIX);
		if ( authenticate(AT_PRIV_GROUPS, true) && authenticate(AT_PRIV_COURSE, true) ) {
			if ($list) {
				$sql .= '(owner_id=%d AND owner_type=%d) OR (owner_id IN (%s) AND owner_type=%d AND name<>"")';
				array_push($sqlParams, $course_id, LINK_CAT_COURSE, $groups, LINK_CAT_GROUP);
			} else {
				$sql .= '(owner_id=%d AND owner_type=%d) OR (owner_id IN (%s) AND owner_type=%d)';
				array_push($sqlParams, $course_id, LINK_CAT_COURSE, $groups, LINK_CAT_GROUP);
			}

		} else if ( authenticate(AT_PRIV_LINKS, true) ) {
			$sql .= '(owner_id=%d AND owner_type=%d)';
			array_push($sqlParams, $course_id, LINK_CAT_COURSE);
			if (!empty($groups)) {
				$sql .= ' OR (owner_id IN (%s) AND owner_type=%d)';
				array_push($sqlParams, $groups, LINK_CAT_GROUP);
			}
		} else if ( authenticate(AT_PRIV_GROUPS, true) || !empty($groups) ) { 
			if ($list) {
				$sql .= '(owner_id IN (%s) AND owner_type=%d AND name<>"")';
				array_push($sqlParams, $groups, LINK_CAT_GROUP);
			} else {
				$sql .= '(owner_id IN (%s) AND owner_type=%d)';
				array_push($sqlParams, $groups, LINK_CAT_GROUP);
			}
		} 	
		$sql .= ' ORDER BY parent_id, name';
	} else {
		if (!empty($groups)) {
			$sql = 'SELECT * FROM %slinks_categories WHERE (owner_id=%d AND owner_type=%d) OR (owner_id IN (%s) AND owner_type=%d) ORDER BY parent_id, name';
			array_push($sqlParams, TABLE_PREFIX, $course_id, LINK_CAT_COURSE, $groups, LINK_CAT_GROUP);
		} else {
			$sql = 'SELECT * FROM %slinks_categories WHERE (owner_id=%d AND owner_type=%d) ORDER BY parent_id, name';
			array_push($sqlParams, TABLE_PREFIX, $course_id, LINK_CAT_COURSE);
		}
	}
	$result = queryDB($sql, $sqlParams);

	foreach ($result as $row) {
		//if group, get name
		if (empty($row['name'])) {
			$row['name'] = get_group_name($row['owner_id']);
			$categories[$row['cat_id']]['group'] = 1;
		}

		$categories[$row['cat_id']]['cat_name']    = $row['name'];
		$categories[$row['cat_id']]['cat_parent']  = $row['parent_id'];

		if ($row['parent_id'] > 0) {
			$categories[$row['parent_id']]['children'][] = $row['cat_id'];
		} else {
			$categories[0][] = $row['cat_id'];
		}
	}

	return $categories;
}

function select_link_categories($categories, $cat_id, $current_cat_id, $exclude, $depth=0, $owner=FALSE) {
	if ($cat_id == 0 && is_array($categories[0])) {
		foreach($categories[0] as $child_cat_id) {
			select_link_categories($categories, $child_cat_id, $current_cat_id, $depth, 0, $owner);
		}
	} else {
		$row = queryDB('SELECT name, owner_type, owner_id FROM %slinks_categories WHERE cat_id=%d', array(TABLE_PREFIX, $cat_id), true);

		if ($exclude && ($cat_id == $current_cat_id)) {
			return;
		}

		if ($owner) {
			echo '<option value="'.$cat_id.'-'.$row['owner_type'].'-'.$row['owner_id'].'"';
		} else  {
			echo '<option value="'.$cat_id.'"';
		}
	
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
				select_link_categories($categories, $child_cat_id, $current_cat_id, $exclude, $depth+1, $owner);
			}
		}
	}
}

/**
 Given a $cat_id, return IDs of all children of that ID as a comma seperated 
 string.
 */
function get_child_categories($cat_id, $categories) {
    if (!isset ($categories)) {
        $categories = get_link_categories();
    }
    
    $category = $categories[$cat_id];
    $children_string = "";
    if (is_array($categories[$cat_id]['children'])) {
        foreach ($categories[$cat_id]['children'] as $child) {
            $children_string = $child.",";
        }
    }
    return $children_string;
}

function get_group_name($owner_id) {
	if (!$owner_id) {
		return false;
	}
	$row = queryDB('SELECT title FROM %sgroups WHERE group_id=%d', array(TABLE_PREFIX, $owner_id), true);
	return $row['title'];
}

function get_cat_info($cat_id) {
	return queryDB('SELECT * FROM %slinks_categories WHERE cat_id=%d', array(TABLE_PREFIX, $cat_id), true);
}
?>