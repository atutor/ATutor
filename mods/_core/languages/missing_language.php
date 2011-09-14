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
//if ($_SESSION['course_id'] > -1) { exit; }

if (isset($_POST['submit'])) {
	unset($_POST['g']);
	unset($_POST['submit']);
	$langEditor->updateTerms($_POST);
}

$params = array();
if ($_POST['filter_new']) {
	$params['new'] = true;
}
if ($_POST['filter_update']) {
	$params['update'] = true;
}
$langEditor->setFilter($params);


$langEditor->printTerms($_GET['terms']);


?>