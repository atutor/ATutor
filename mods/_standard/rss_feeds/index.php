<?php	
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH . 'vitals.inc.php');

admin_authenticate(AT_ADMIN_PRIV_RSS);


if ((isset($_GET['preview']) || isset($_GET['edit']) || isset($_GET['delete'])) && !isset($_GET['fid'])) {
	$msg->addError('NO_ITEM_SELECTED');
} else if (isset($_GET['edit'])) {
	header("Location:edit_feed.php?fid=".intval($_GET['fid']));
	exit;
} else if (isset($_GET['delete'])) {
	header("Location:delete_feed.php?fid=".intval($_GET['fid']));
	exit;
} else if (isset($_GET['preview'])) {
	header("Location:preview.php?fid=".intval($_GET['fid']));
	exit;
}

require (AT_INCLUDE_PATH.'header.inc.php');

$sql	= "SELECT * FROM ".TABLE_PREFIX."feeds ORDER BY feed_id";
$result = mysql_query($sql, $db);

$savant->assign('result', $result);
$savant->display('admin/system_preferences/index.tmpl.php');
require (AT_INCLUDE_PATH.'footer.inc.php'); ?>