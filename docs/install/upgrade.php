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
require(AT_INCLUDE_PATH.'uheader.php');

if ($step == 1) {
	require(AT_INCLUDE_PATH.'ustep1.php');
}

if ($step == 2) {
	require(AT_INCLUDE_PATH.'ustep2.php');
}

/* the file/dir permissions from the installation */
if ($step == 3) {
	require(AT_INCLUDE_PATH.'step5.php');
}

if ($step == 4) {
	require(AT_INCLUDE_PATH.'ustep4.php');
}

/* write the config.inc.php file with any new options */
if ($step == 5) {
	require(AT_INCLUDE_PATH.'step3.php');
}

if ($step == 6) {
	require(AT_INCLUDE_PATH.'step7.php');
}

require(AT_INCLUDE_PATH.'footer.php');
?>