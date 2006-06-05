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

//is this section used on this page?
if (isset($_GET['auto']) && ($_GET['auto'] == 'disable')) {

	$parts = parse_url($_base_href);

	setcookie('ATLogin', '', time()-172800, $parts['path'], $parts['host'], 0);
	setcookie('ATPass',  '', time()-172800, $parts['path'], $parts['host'], 0);
	
	$msg->addFeedback('AUTO_DISABLED');
	header('Location: index.php');
	exit;
} else if (isset($_GET['auto']) && ($_GET['auto'] == 'enable')) {
	$parts = parse_url($_base_href);

	$sql	= "SELECT PASSWORD(password) AS pass FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]";
	$result = mysql_query($sql, $db);
	$row	= mysql_fetch_array($result);

	setcookie('ATLogin', $_SESSION['login'], time()+172800, $parts['path'], $parts['host'], 0);
	setcookie('ATPass',  $row['pass'], time()+172800, $parts['path'], $parts['host'], 0);

	$msg->addFeedback('AUTO_ENABLED');
	header('Location: index.php');
	exit;
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


$savant->assign('courses', $courses);

$savant->display('users/index.tmpl.php');
?>