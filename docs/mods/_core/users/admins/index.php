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
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

if (isset($_GET['delete'], $_GET['login'])) {
	header('Location: delete.php?login='.$_GET['login']);
	exit;
} else if (isset($_GET['view_log'], $_GET['login'])) {
	header('Location: log.php?login='.$_GET['login']);
	exit;
} else if (isset($_GET['password'], $_GET['login'])) {
	header('Location: password.php?login='.$_GET['login']);
	exit;
} else if (isset($_GET['edit'], $_GET['login'])) {
	header('Location: edit.php?login='.$_GET['login']);
	exit;
} else if ((isset($_GET['edit']) || isset($_GET['delete']) || isset($_GET['view_log']))) {
	$msg->addError('NO_ITEM_SELECTED');
}

$id = $_GET['id'];
$L = $_GET['L'];
require(AT_INCLUDE_PATH.'header.inc.php'); 


$orders = array('asc' => 'desc', 'desc' => 'asc');
$cols   = array('login' => 1, 'real_name' => 1, 'email' => 1, 'last_login' => 1);

if (isset($_GET['asc'])) {
	$order = 'asc';
	$col   = isset($cols[$_GET['asc']]) ? $_GET['asc'] : 'login';
} else if (isset($_GET['desc'])) {
	$order = 'desc';
	$col   = isset($cols[$_GET['desc']]) ? $_GET['desc'] : 'login';
} else {
	// no order set
	$order = 'asc';
	$col   = 'login';
}


	$offset = ($page-1)*$results_per_page;

	$sql	= "SELECT * FROM ".TABLE_PREFIX."admins ORDER BY $col $order";
	$result = mysql_query($sql, $db);

$savant->assign('result', $result);
$savant->display('admin/users/index.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>