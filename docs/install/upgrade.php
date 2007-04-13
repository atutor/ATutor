<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg, Heidi Hazelton	*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');

if (isset($_POST['new_version'])) {
	$new_version = $_POST['new_version'];
}

if (isset($_POST['step'])) {
	$step = intval($_POST['step']);
}

if (!isset($step) || ($step == 0)) {
	$step = 1;
}

require('include/common.inc.php');

if (($step == 2) && isset($_POST['override']) && ($_POST['override'] == 0)) {
	header('Location: index.php');
	exit;
}

require('include/upgrade_header.php');

if ($step == 1) {
	// in:  select directory
	// out: confirm verions
	require('include/ustep1.php');
}
if ($step == 2) {
	// in:  update database
	// out: -
	require('include/ustep2.php');
}
if ($step == 3) {
	// in:  display version specific notices
	// out: update database with new options
	require('include/ustep3.php');
}
if ($step == 4) {
	// in:  determine where the old content dir is and if it has to be copied
	// out: try to create the directory and set permissions
	require('include/step5.php');
}
if ($step == 5) {
	// in:  copy the config file
	// out: -
	require('include/ustep5.php');
}
if ($step == 6) {
	// in:  copy the content if needed
	// out: -
	require('include/ustep4.php');
}
/* anonymous data collection */
if ($step == 7) {	
	require('include/step7.php');
}

if ($step == 8) {
	require('include/ustep6.php');
}

require('include/footer.php');
?>