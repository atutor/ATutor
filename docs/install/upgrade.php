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

$new_version = $_POST['new_version'];

$step = intval($_POST['step']);

if ($step == 0) {
	$step = 1;
}

require(AT_INCLUDE_PATH.'common.inc.php');

if (($step == 2) && isset($_POST['override']) && ($_POST['override'] == 0)) {
	header('Location: index.php');
	exit;
}

require(AT_INCLUDE_PATH.'uheader.php');

if ($step == 1) {
	require(AT_INCLUDE_PATH.'ustep1.php');
}

if ($step == 2) {
	require(AT_INCLUDE_PATH.'ustep2.php');
}

/* write the config.inc.php file with any new options */
if ($step == 3) {
	require(AT_INCLUDE_PATH.'step3.php');
}

//content dir
if ($step == 4) {
	require(AT_INCLUDE_PATH.'step5.php');
}

if ($step == 5) {
	require(AT_INCLUDE_PATH.'step6.php');
}

if ($step == 6) {
	require(AT_INCLUDE_PATH.'ustep4.php');
}

if ($step == 7) {
	require(AT_INCLUDE_PATH.'ustep6.php');
}

require(AT_INCLUDE_PATH.'footer.php');
?>