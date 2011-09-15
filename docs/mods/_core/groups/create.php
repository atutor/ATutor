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
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_GROUPS);

if (isset($_GET['submit'], $_GET['create']) && ($_GET['create'] == 'automatic')) {
	header('Location: create_automatic.php');
	exit;
} else if (isset($_GET['submit'], $_GET['create']) && ($_GET['create'] == 'manual')) {
	header('Location: create_manual.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$savant->display('instructor/groups/create.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>