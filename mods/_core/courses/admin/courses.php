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
admin_authenticate(AT_ADMIN_PRIV_COURSES);

if (isset($_GET['view'], $_GET['id'])) {
	header('Location:instructor_login.php?course='.$_GET['id']);
	exit;
} else if (isset($_GET['edit'], $_GET['id'])) {

	header('Location:  ../../properties/admin/edit_course.php?course='.$_GET['id']);
	exit;
} else if (isset($_GET['backups'], $_GET['id'])) {
	header('Location: ../../backups/admin/index.php?course='.$_GET['id']);
	exit;
} else if (isset($_GET['delete'], $_GET['id'])) {
	header('Location: ../../properties/admin/delete_course.php?course='.$_GET['id']);
	exit;
}  else if (isset($_GET['delete']) || isset($_GET['backups']) || isset($_GET['edit']) || isset($_GET['view'])) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

$page_string = '';

if ($_GET['reset_filter']) {
	unset($_GET);
}

$orders = array('asc' => 'desc', 'desc' => 'asc');
$cols   = array('title' => 1, 'login' => 1, 'access' => 1, 'created_date' => 1, 'cat_name' => 1);
$_access = array('public', 'protected', 'private');

if (isset($_GET['asc'])) {
	$order = 'asc';
	$col   = isset($cols[$_GET['asc']]) ? $_GET['asc'] : 'title';
} else if (isset($_GET['desc'])) {
	$order = 'desc';
	$col   = isset($cols[$_GET['desc']]) ? $_GET['desc'] : 'title';
} else {
	// no order set
	$order = 'asc';
	$col   = 'title';
}

if (isset($_GET['access']) && ($_GET['access'] != '') && isset($_access[$_GET['access']])) {
	$access = 'C.access = \'' . $_access[$_GET['access']].'\'';
	$page_string .= SEP.'access='.$_GET['access'];
} else {
	$access = '1';
}

if ($_GET['search']) {
	$page_string .= SEP.'search='.urlencode($_GET['search']);
	$search = $addslashes($_GET['search']);
	$search = str_replace(array('%','_'), array('\%', '\_'), $search);
	$search = '%'.$search.'%';
	$search = "((C.title LIKE '$search') OR (C.description LIKE '$search'))";
} else {
	$search = '1';
}

// get number of courses on the system
$sql	= "SELECT COUNT(*) AS cnt FROM %scourses C WHERE 1 AND $access AND $search";
$row = queryDB($sql, array(TABLE_PREFIX), TRUE);
$num_results = $row['cnt'];

$results_per_page = 100;
$num_pages = max(ceil($num_results / $results_per_page), 1);
$page = intval($_GET['p']);
if (!$page) {
	$page = 1;
}	
$count  = (($page-1) * $results_per_page) + 1;
$offset = ($page-1)*$results_per_page;

${'highlight_'.$col} = ' style="background-color: #fff;"';

$sql    = "SELECT COUNT(*) AS cnt, approved, course_id FROM %scourse_enrollment WHERE approved='y' OR approved='a' GROUP BY course_id, approved";
$rows_enrollment = queryDB($sql, array(TABLE_PREFIX));

foreach($rows_enrollment as $row){
	if ($row['approved'] == 'y') {
		$row['cnt']--; // remove the instructor
	}
	$enrolled[$row['course_id']][$row['approved']] = $row['cnt'];
}

$sql	= "SELECT C.*, M.login, T.cat_name FROM %smembers M INNER JOIN %scourses C USING (member_id) LEFT JOIN %scourse_cats T USING (cat_id) WHERE 1 AND $access AND $search ORDER BY $col $order LIMIT $offset, $results_per_page";
$rows_course_list = queryDB($sql, array(TABLE_PREFIX,TABLE_PREFIX,TABLE_PREFIX));
$num_rows = count($rows_course_list);

$savant->assign('results_per_page', $results_per_page);
$savant->assign('page', $page);
$savant->assign('page_string', $page_string);
$savant->assign('enrolled', $enrolled);
$savant->assign('num_rows', $num_rows);
$savant->assign('rows_course_list', $rows_course_list);
$savant->assign('orders', $orders);
$savant->assign('order', $order);
$savant->assign('num_results', $num_results);
$savant->display('admin/courses/courses.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>