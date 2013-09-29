<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
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

$sql_patches = "SELECT * from %smyown_patches m where myown_patch_id=%d";
$row_patches = queryDB($sql_patches, array(TABLE_PREFIX, $myown_patch_id), TRUE);

$sql_patch_dependent = "SELECT * from %smyown_patches_dependent m where myown_patch_id=%d";
$rows_patch_dependent = queryDB($sql_patch_dependent, array(TABLE_PREFIX, $myown_patch_id));

$sql_patch_files = "SELECT * from %smyown_patches_files m where myown_patch_id=%d order by myown_patches_files_id";
$rows_patch_files = queryDB($sql_patch_files, array(TABLE_PREFIX, $myown_patch_id));


require ('patch_edit_interface.tmpl.php');

?>
