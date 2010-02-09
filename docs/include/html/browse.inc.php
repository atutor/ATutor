<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }
require(AT_INCLUDE_PATH.'../mods/_core/cats_categories/lib/admin_categories.inc.php');

$cats	= array();
$cats[0] = _AT('cats_uncategorized');

$sql = "SELECT cat_id, cat_name FROM ".TABLE_PREFIX."course_cats";
$result = mysql_query($sql,$db);
while($row = mysql_fetch_array($result)) {
	$cats[$row['cat_id']] = $row['cat_name'];
}

if ($_GET['reset_filter']) { unset($_GET); }

$page_string = '';

if (isset($_GET['access']) && in_array($_GET['access'], array('public','private','protected'))) {
	$page_string .= SEP.'access='.$_GET['access'];
	$sql_access = "='{$_GET['access']}'";
} else {
	$sql_access     = '<>-1';
	$_GET['access'] = '';
}

if (isset($_GET['category']) && ($_GET['category'] > -1)) {
	$_GET['category'] = intval($_GET['category']);
	$page_string .= SEP.'category='.$_GET['category'];
	$sql_category = '='.$_GET['category'];
} else {
	$sql_category     = '<>-1';
	$_GET['category'] = -1; // all (because 0 = uncategorized)
}

if (isset($_GET['include']) && $_GET['include'] == 'one') {
	$checked_include_one = ' checked="checked"';
	$page_string .= SEP.'include=one';
} else {
	$_GET['include'] = 'all';
	$checked_include_all = ' checked="checked"';
	$page_string .= SEP.'include=all';
}

if (!empty($_GET['search'])) {
	$page_string .= SEP.'search='.urlencode($stripslashes($_GET['search']));
	$search = $addslashes($_GET['search']);
	$search = explode(' ', $search);

	if ($_GET['include'] == 'all') {
		$predicate = 'AND ';
	} else {
		$predicate = 'OR ';
	}

	$sql_search = '';
	foreach ($search as $term) {
		$term = trim($term);
		$term = str_replace(array('%','_'), array('\%', '\_'), $term);
		if ($term) {
			$term = '%'.$term.'%';
			$sql_search .= "((title LIKE '$term') OR (description LIKE '$term')) $predicate";
		}
	}
	$sql_search = '('.substr($sql_search, 0, -strlen($predicate)).')';
} else {
	$sql_search = '1';
}

$sql	= "SELECT COUNT(course_id) AS cnt FROM ".TABLE_PREFIX."courses WHERE access $sql_access AND cat_id $sql_category AND $sql_search AND hide=0";
$result = mysql_query($sql, $db);
$row = mysql_fetch_assoc($result);
$num_results = $row['cnt'];

$sql	= "SELECT * FROM ".TABLE_PREFIX."courses WHERE access $sql_access AND cat_id $sql_category AND $sql_search AND hide=0 ORDER BY title";
$courses_result = mysql_query($sql, $db);

// get the categories <select>, if there are any.
// we need ob_start/ob_clean, because select_categories() outputs directly.
// we do this so that if there are no categories, then the option doesn't appear.
ob_start();
select_categories(get_categories(), 0, $_GET['category'], false);
$categories_select = ob_get_contents();
ob_clean();

$has_categories = false;
if ($categories_select != '<option value="0"></option>') {
	$has_categories = true;
}

$savant->assign('num_results', $num_results);
$savant->assign('has_categories', $has_categories);
$savant->assign('categories_select', $categories_select);
$savant->assign('checked_include_all', $checked_include_all);
$savant->assign('checked_include_one', $checked_include_one);
$savant->assign('courses_result', $courses_result);

$savant->display('users/browse.tmpl.php');

?>
