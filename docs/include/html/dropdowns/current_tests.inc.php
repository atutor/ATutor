<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }
global $db;
global $_base_path;
global $savant;

// This menu module works a little differently than the others. There is no preference setting to hide it.
// It displayed automaticall yif there are current test sactive

require_once(AT_INCLUDE_PATH.'lib/test_result_functions.inc.php');

$savant->assign('tmpl_popup_help', 'CURRENT_TESTS');
$savant->assign('tmpl_access_key', '');


if ($_GET['menu_jump']) {
	$savant->assign('tmpl_menu_url', '<a name="menu_jump4"></a>');	
} else {
	$savant->assign('tmpl_menu_url', '');	
}

$course = intval($_SESSION['course_id']);

//Get list of current tests for this course
	$sql	= "SELECT T.test_id, T.title FROM ".TABLE_PREFIX."tests T WHERE T.course_id=$course AND T.start_date<=NOW() AND T.end_date>= NOW() ORDER BY T.start_date, T.title";
	$result	= mysql_query($sql, $db);
	$num_tests = mysql_num_rows($result);
	$tests = '';
	while (($row = mysql_fetch_assoc($result)) && authenticate_test($row['test_id'])) {
		$tests .= '<li><a href="'.$_base_path.'tools/take_test.php?tid='.$row['test_id'].SEP.'tt='.urlencode($row['title']).'">'.$row['title'].'</a><br /></li>'."\n";
	}
	
// If current tests exist, display a drop down listing them
	if ($tests) { 
	ob_start(); 	
	echo '<tr>';
	echo '<td class="dropdown" align="left">';
	echo '<ul>';
	echo $tests;
	echo '</ul>';
	echo '</td>';
	echo '</tr>';
	$savant->assign('tmpl_dropdown_contents', ob_get_contents());
	ob_end_clean();
	$savant->assign('tmpl_close_url', $_base_path.'tools/my_tests.php');
	$savant->assign('tmpl_dropdown_close', _AT('curren_tests_surveys'));
	$savant->display('dropdown_open.tmpl.php');
		}
?>
