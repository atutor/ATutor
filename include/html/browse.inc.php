<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }
require(AT_INCLUDE_PATH.'../mods/_core/cats_categories/lib/admin_categories.inc.php');

$cats	= array();
$cats[0] = _AT('cats_uncategorized');

$sql = "SELECT cat_id, cat_name FROM %scourse_cats";
$rows_cats = queryDB($sql, array(TABLE_PREFIX));

foreach($rows_cats as $row){
	$cats[$row['cat_id']] = $row['cat_name'];
}

if (isset($_GET['reset_filter'])) { unset($_GET); }

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

$sql	= "SELECT * FROM %scourses WHERE access %s AND cat_id %s AND %s AND hide=0 ORDER BY title";
$rows_courses = queryDB($sql, array(TABLE_PREFIX, $sql_access, $sql_category, $sql_search));
$num_results = count($rows_courses);

// add "enroll me" link if the user is not the course owner and is not enrolled
foreach($rows_courses as $row ){
	if (isset($_SESSION['member_id']) &&  $_SESSION['member_id'] > 0) {
		$sql	= "SELECT * FROM %scourse_enrollment WHERE member_id=%d AND course_id=%d";
		$rows_enrollment = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id'], $row['course_id']));
		if ($row['access'] == 'private') {
			$enroll_link = '<a href="'.$_base_path.'users/private_enroll.php?course='.$row['course_id'].'">'. _AT('enroll_me').'</a>';
		} else {
			$enroll_link = '<a href="'.$_base_path.'enroll.php?course='.$row['course_id'].'">'. _AT('enroll_me').'</a>';
		}
		
		if (count($rows_enrollment) == 0 && $_SESSION['member_id'] <> $row['member_id']) {
			$row['enroll_link'] = $enroll_link;
		} else if ($row['access'] == 'private') {
			$enrollment_row = $rows_enrollment;
			if ($enrollment_row['approved'] == 'n') $row['enroll_link'] = $enroll_link;
		}
	}
	$course_rows= [];
	$courses_rows[] = $row;
	for($i = 0; $i < count($courses_rows); $i++ ){
	    $courses_rows[$i]['title']  = stripslashes(html_entity_decode($courses_rows[$i]['title']));
	    $courses_rows[$i]['banner']  = stripslashes($courses_rows[$i]['banner']);
	    $courses_rows[$i]['description']  = stripslashes(html_entity_decode($courses_rows[$i]['description']));
	}
}

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

$savant->assign('cats', $cats);
$savant->assign('num_results', $num_results);
$savant->assign('has_categories', $has_categories);
$savant->assign('categories_select', $categories_select);
$savant->assign('checked_include_all', $checked_include_all);
if(isset($checked_include_one)){
    $savant->assign('checked_include_one', $checked_include_one);
} else{
    $savant->assign('checked_include_one', '');
}
$savant->assign('courses_rows', $courses_rows);

$savant->display('users/browse.tmpl.php');

?>
