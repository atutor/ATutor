<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

$page = 'tools';
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_ANNOUNCEMENTS);

if (isset($_GET['edit'], $_GET['aid'])) {
	header('Location: '.AT_BASE_HREF.'mods/_standard/announcements/edit_news.php?aid='.intval($_GET['aid']));
	exit;
} else if (isset($_GET['delete'], $_GET['aid'])) {
	header('Location: '.AT_BASE_HREF.'mods/_standard/announcements/delete_news.php?aid='.intval($_GET['aid']));
	exit;
} else if ((isset($_GET['edit']) || isset($_GET['delete']))) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php');

$orders = array('asc' => 'desc', 'desc' => 'asc');
$cols   = array('title' => 1, 'date' => 1);

if (isset($_GET['asc'])) {
	$order = 'asc';
	$col   = isset($cols[$_GET['asc']]) ? $_GET['asc'] : 'date';
} else if (isset($_GET['desc'])) {
	$order = 'desc';
	$col   = isset($cols[$_GET['desc']]) ? $_GET['desc'] : 'date';
} else {
	// no order set
	$order = 'desc';
	$col   = 'date';
}

$sql	= "SELECT news_id, title, date FROM ".TABLE_PREFIX."news WHERE course_id=$_SESSION[course_id] ORDER BY $col $order";
$result = mysql_query($sql, $db);

$savant->assign('result', $result);
$savant->assign('col', $col);
$savant->assign('order', $order);
$savant->assign('orders', $orders);
$savant->display('instructor/announcements/index.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>