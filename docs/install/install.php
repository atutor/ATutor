<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay, Joel Kronenberg, Heidi Hazelton	*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
define('AT_INCLUDE_PATH', 'include/');

if (!$new_version = $_POST['new_version']) {
	$new_version = $_POST['step2']['new_version'];
}

$step = intval($_POST['step']);

if ($step == 0) {
	$step = 1;
}

require(AT_INCLUDE_PATH.'common.inc.php');

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
if ($step == 4) {	
	require(AT_INCLUDE_PATH.'step4.php');
}

/* directory permissions and generating the config.inc.php file */
if ($step == 5) {	
	require(AT_INCLUDE_PATH.'step5.php');
}

/* done! */
if ($step == 6) {	
	require(AT_INCLUDE_PATH.'step6.php');
}

require(AT_INCLUDE_PATH.'footer.php');
?>