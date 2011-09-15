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
authenticate(AT_PRIV_FORUMS);

if (isset($_GET['edit'], $_GET['id'])) {
	header('Location: '.AT_BASE_HREF.'mods/_standard/forums/edit_forum.php?fid='.intval($_GET['id']));
	exit;
} else if (isset($_GET['delete'], $_GET['id'])) {
	header('Location: '.AT_BASE_HREF.'mods/_standard/forums/delete_forum.php?fid='.intval($_GET['id']));
	exit;
} else if (isset($_GET['edit']) || isset($_GET['delete'])) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'../mods/_standard/forums/lib/forums.inc.php');

require(AT_INCLUDE_PATH.'header.inc.php');


$all_forums = get_forums($_SESSION['course_id']);
$savant->assign('all_forums', $all_forums);
$savant->display('instructor/forums/index.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>