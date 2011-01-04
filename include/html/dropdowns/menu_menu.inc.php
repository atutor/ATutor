<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: menu_menu.inc.php 10311 2010-10-08 18:03:04Z greg $

if (!defined('AT_INCLUDE_PATH')) { exit; }
global $_base_path;
global $savant;
global $contentManager;

ob_start();

echo '<div style="white-space:nowrap;">';

echo '<a href="'.$_base_path.'index.php">'._AT('course_home').'</a><br />';

/* @See classes/ContentManager.class.php	*/
$contentManager->printMainMenu();

echo '</div>';

$savant->assign('dropdown_contents', ob_get_contents());
ob_end_clean();
$savant->assign('title', _AT('content_navigation'));
$savant->display('include/box.tmpl.php');
?>