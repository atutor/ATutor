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


$new_version = $_POST['new_version'];

$step = intval($_POST['step']);

if ($step == 0) {
	$step = 1;
}

require 'include/common.inc.php';
require 'include/uheader.php';

if ($step == 1) {
	include 'include/ustep1.php';
}

if ($step == 2) {
	include 'include/ustep2.php';
}

/* the file/dir permissions from the installation */
if ($step == 3) {
	include 'include/step5.php';
}

if ($step == 4) {
	include 'include/ustep4.php';
}

if ($step == 5) {
	include 'include/ustep5.php';
}

if ($step == 6) {
	include 'include/step7.php';
}

require 'include/footer.php';
?>