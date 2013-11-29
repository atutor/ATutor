<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008                                      */
/* Written by Greg Gay & Joel Kronenberg & Chris Ridpath        */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_ASSIGNMENTS);
tool_origin();
if (isset($_GET['edit'])) {
	$_GET['assignment'] = intval($_GET['assignment']);
	header('Location: edit_assignment.php?id='. $_GET['assignment']);
	exit;
} else if (isset($_GET['delete'])) {
	$_GET['assignment'] = intval($_GET['assignment']);
	header('Location: delete_assignment.php?id='. $_GET['assignment']);
	exit;
} else if (isset($_GET['submissions'])){
	$_GET['assignment'] = intval($_GET['assignment']);
	//header('Location: '.url_rewrite('mods/_standard/file_storage/index.php?ot='.WORKSPACE_ASSIGNMENT.SEP.'oid='.$_GET['assignment'], AT_PRETTY_URL_IS_HEADER));
	exit;
}
$msg->addInfo('ASSIGNMENT_FS_SUBMISSIONS'); 
require(AT_INCLUDE_PATH.'header.inc.php'); 

// sort order of table
$orders = array('ASC' => 'DESC', 'DESC' => 'ASC');
$cols   = array('title' => 1, 'date_due' => 1);
$sort = 'title';
$order = 'ASC';

if (isset($_GET['sort'])){
	$sort = isset($cols[$_GET['sort']]) ? $_GET['sort'] : 'title';
}
if (isset($_GET['order'])){
	$order = $addslashes($_GET['order']);
	if (($order != 'ASC') && ($order != 'DESC')){
		$order = 'ASC';
	}
}

$sql = "SELECT * FROM %sassignments WHERE course_id=%d ORDER BY $sort $order";
$rows_assignments = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));

foreach($rows_assignments as $row){
    $sql2 = "SELECT title FROM %sgroups_types WHERE type_id=%d AND course_id=%d";
    $rows_type[$row['assignment_id']] = queryDB($sql2, array(TABLE_PREFIX, $row['assign_to'], $_SESSION['course_id']), TRUE);
}

$savant->assign('rows_assignments', $rows_assignments);
$savant->assign('sort', $sort);
$savant->assign('rows_type', $rows_type);
$savant->display('instructor/assignments/index_instructor.tmpl.php');
require (AT_INCLUDE_PATH.'footer.inc.php'); ?>