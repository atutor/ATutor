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

authenticate(AT_PRIV_POLLS);

if (isset($_POST['edit'], $_POST['poll'])) {
	header('Location: edit.php?poll_id=' . $_POST['poll']);
	exit;
} else if (isset($_POST['delete'], $_POST['poll'])) { 
	header('Location: delete.php?pid=' . $_POST['poll'] );
	exit;
} else if (!empty($_POST)) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

$orders = array('asc' => 'desc', 'desc' => 'asc');
$cols   = array('question' => 1, 'created_date' => 1, 'total' => 1);

if (isset($_GET['asc'])) {
	$order = 'asc';
	$col   = isset($cols[$_GET['asc']]) ? $_GET['asc'] : 'created_date';
} else if (isset($_GET['desc'])) {
	$order = 'desc';
	$col   = isset($cols[$_GET['desc']]) ? $_GET['desc'] : 'created_date';
} else {
	// no order set
	$order = 'desc';
	$col   = 'created_date';
}

$sql	= "SELECT poll_id, question, created_date, total FROM %spolls WHERE course_id=%d ORDER BY $col $order";
$rows_polls = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));

$savant->assign('rows_polls', $rows_polls);
$savant->assign('col', $col);
$savant->assign('order', $order);
$savant->assign('orders', $orders);
$savant->display('instructor/polls/index.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php');  ?>