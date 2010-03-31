<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
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

// Get the course catagories
$sql = "SELECT * FROM ".TABLE_PREFIX."course_cats ORDER BY cat_name";
$result = mysql_query($sql,$db);
if(mysql_num_rows($result) != 0){
	while($row = mysql_fetch_assoc($result)){
		$current_cats[$row['cat_id']] = $row['cat_name'];
		$parent_cats[$row['cat_id']] =  $row['cat_parent'];
		$cat_cats[$row['cat_id']] = $row['cat_id'];
	}
}

//get courses
$sql = "SELECT E.approved, E.last_cid, C.* FROM ".TABLE_PREFIX."course_enrollment E, ".TABLE_PREFIX."courses C WHERE E.member_id=$_SESSION[member_id] AND E.course_id=C.course_id ORDER BY C.title";
$result = mysql_query($sql,$db);

$courses = array();
while ($row = mysql_fetch_assoc($result)) {
	/* get tests for these courses: */
	$tests['tests'] = array();
	$sql3	= "SELECT test_id, title FROM ".TABLE_PREFIX."tests WHERE course_id=$row[course_id] AND (TO_DAYS(start_date) <= TO_DAYS(NOW()) AND TO_DAYS(end_date) >= TO_DAYS(NOW())) AND format=1";
	$result3 = mysql_query($sql3,$db);
	while ($row3 = mysql_fetch_assoc($result3)) {
		$tests['tests'][] = $row3;
	}

	$courses[] = array_merge($row, (array) $tests);
}

function get_category_name($cat_id) {
	global $db;
	$sql	= "SELECT cat_name FROM ".TABLE_PREFIX."course_cats WHERE cat_id=".$cat_id;
	$result = mysql_query($sql,$db);
	$row = mysql_fetch_assoc($result);

	if ($row['cat_name'] == '') {
		$row['cat_name'] = _AT('cats_uncategorized');
	} 
	return $row['cat_name'];
}

//LAW
//$_SESSION['first_login'] = true; //for testing
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

$module_status_bits = AT_MODULE_STATUS_DISABLED | AT_MODULE_STATUS_ENABLED | AT_MODULE_STATUS_MISSING | AT_MODULE_STATUS_PARTIALLY_UNINSTALLED;
$module_type_bits = AT_MODULE_TYPE_STANDARD + AT_MODULE_TYPE_EXTRA;

$module_list = $moduleFactory->getModules($module_status_bits, $module_type_bits, $sort = TRUE);

foreach($module_list as $key=>$obj) {
	$news = $obj->getNews();
	while(!empty($news)){
		$current_item = array_pop($news);
		array_push($all_news, $current_item);
	}
}

usort($all_news, 'all_news_cmp');

$savant->assign('all_news', $all_news);
$savant->assign('courses', $courses);

$savant->display('users/index.tmpl.php');
?>