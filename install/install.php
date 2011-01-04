<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: install.php 10055 2010-06-29 20:30:24Z cindy $

define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH.'common.inc.php');

if (!$new_version = $_POST['new_version']) {
	$new_version = $_POST['step2']['new_version'];
}

$step = intval($_POST['step']);

if ($step == 0) {
	$step = 1;
}

if ($_POST['submit'] == 'I Disagree'){
	Header ("Location: index.php");
}

require(AT_INCLUDE_PATH.'header.php');

/* agree to terms of use */
if ($step == 1) {
	require(AT_INCLUDE_PATH.'step1.php');
}

/* db */
if ($step == 2) {
	require(AT_INCLUDE_PATH.'step2.php');
}

/* preferences */
if ($step == 3) {	
	require(AT_INCLUDE_PATH.'step3.php');
}

/* personal account + welcome course */
//if ($step == 4) {	
	//require(AT_INCLUDE_PATH.'step4.php');
//}

/* content directory */
if ($step == 4) {
	require(AT_INCLUDE_PATH.'step5.php');
}

/* directory permissions and generating the config.inc.php file */
if ($step == 5) {	
	require(AT_INCLUDE_PATH.'step6.php');
}

/* anonymous data collection */
if ($step == 6) {	
	require(AT_INCLUDE_PATH.'step7.php');
}

/* done! */
if ($step == 7) {	
	require(AT_INCLUDE_PATH.'step8.php');
}

require(AT_INCLUDE_PATH.'footer.php');
?>