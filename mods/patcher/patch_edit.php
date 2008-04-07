<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: index_admin.php 7208 2008-03-13 16:07:24Z cindy $

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_PATCHER);

if (!isset($_REQUEST["myown_patch_id"]))
{
	$msg->addError('NO_ITEM_SELECTED');
	exit;
}

$myown_patch_id = $_REQUEST["myown_patch_id"];

// URL called by form action
$url = dirname($_SERVER['PHP_SELF']) . "/patch_creator.php?myown_patch_id=" . $myown_patch_id;

$sql_patches = "SELECT * from ".TABLE_PREFIX."myown_patches m where myown_patch_id=". $myown_patch_id;
$result_patches = mysql_query($sql_patches, $db) or die(mysql_error());
$row_patches = mysql_fetch_assoc($result_patches);

$sql_patch_dependent = "SELECT * from ".TABLE_PREFIX."myown_patches_dependent m where myown_patch_id=". $myown_patch_id;
$result_patch_dependent = mysql_query($sql_patch_dependent, $db) or die(mysql_error());

$sql_patch_files = "SELECT * from ".TABLE_PREFIX."myown_patches_files m where myown_patch_id=". $myown_patch_id;
$result_patch_files = mysql_query($sql_patch_files, $db) or die(mysql_error());

require ('patch_edit_interface.tmpl.php');

?>
