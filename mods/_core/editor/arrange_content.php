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
// $Id: arrange_content.php 10142 2010-08-17 19:17:26Z hwong $

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_CONTENT);

if (isset($_POST['move']) && isset($_POST['moved_cid'])) {
	$arr = explode('_', key($_POST['move']), 2);
	$new_pid = $arr[0];
	$new_ordering = $arr[1];
	
	$contentManager->moveContent($_POST['moved_cid'], $new_pid, $new_ordering);
	header('Location: '.AT_BASE_HREF.'mods/_core/editor/arrange_content.php');
	exit;
}
	
if (!defined('AT_INCLUDE_PATH')) { exit; }

$savant->assign('languageManager', $languageManager);

$savant->display('editor/arrange_content.tmpl.php');

?>
