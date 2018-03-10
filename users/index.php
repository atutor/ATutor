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

$page = 'my_courses';
$_user_location	= 'users';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if ($_SESSION['valid_user'] !== true) {
	require(AT_INCLUDE_PATH.'header.inc.php');

	$info = array('INVALID_USER', $_SESSION['course_id']);
	$msg->printInfos($info);
	
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$title = _AT('home'); 

//get courses
$sql = "SELECT E.approved, E.last_cid, C.* FROM %scourse_enrollment E, %scourses C WHERE E.member_id=%d AND E.course_id=C.course_id ORDER BY C.title";
$rows_courses = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $_SESSION['member_id']));

$courses = array();
foreach($rows_courses as $row){
	/* get tests for these courses: */
	$tests['tests'] = array();

	$sql3	= "SELECT test_id, title FROM %stests WHERE course_id=%d AND (TO_DAYS(start_date) <= TO_DAYS(NOW()) AND TO_DAYS(end_date) >= TO_DAYS(NOW())) AND format=1";
	$rows_tests = queryDB($sql3, array(TABLE_PREFIX, $row['course_id']));
	
	foreach($rows_tests as $row3){
		$tests['tests'][] = $row3;
	}

	$courses[] = array_merge($row, (array) $tests);
	for($i = 0; $i < count($courses); $i++ ){
        $courses[$i]['title'] = stripslashes(htmlspecialchars_decode($courses[$i]['title'], ENT_QUOTES));
        $courses[$i]['banner'] = stripslashes($courses[$i]['banner']);
        $courses[$i]['description']  = stripslashes($courses[$i]['description']);
	}
}

function get_category_name($cat_id) {

	$sql	= "SELECT cat_name FROM %scourse_cats WHERE cat_id=%d";
	$row = queryDB($sql, array(TABLE_PREFIX, $cat_id), TRUE);

	if ($row['cat_name'] == '') {
		$row['cat_name'] = _AT('cats_uncategorized');
	} 
	return $row['cat_name'];
}

if ($_SESSION['first_login']) {
    $msg->addInfo(array('FIRST_PREFS', $_base_path.'users/pref_wizard/index.php'));
}

if (!$courses && get_instructor_status())
	$msg->addInfo('NO_COURSES_INST');
elseif (!$courses)
	$msg->addInfo('NO_COURSES');
	

//sort function for all_news
function all_news_cmp($a, $b){
	if ($b['time'] < $a['time']){
		return -1;
	} elseif ($b['time'] > $a['time']){
		return 1;
	} else {
		return 0;
	}
}
$all_news = array();	//all news 

$module_status_bits = AT_MODULE_STATUS_ENABLED;
$module_type_bits = AT_MODULE_TYPE_STANDARD + AT_MODULE_TYPE_EXTRA;

$module_list = $moduleFactory->getModules($module_status_bits, $module_type_bits, $sort = TRUE);

foreach($module_list as $key=>$obj) {
	$news = $obj->getNews();
	while(!empty($news)){
		$current_item = array_pop($news);
		$current_item['course'] = stripslashes($current_item['course']);
		array_push($all_news, $current_item);
	}
}

usort($all_news, 'all_news_cmp');
$savant->assign('all_news', $all_news);
$savant->assign('courses', $courses);

$savant->display('users/index.tmpl.php');
?>