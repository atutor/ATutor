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

if (!$new_version = $_POST['new_version']) {
	$new_version = $_POST['step2']['new_version'];
}

$step = intval($_POST['step']);

if ($step == 0) {
	$step = 1;
}

require 'include/common.inc.php';

require 'include/header.php';

/* agree to terms of use */
if ($step == 1) {
	include 'include/step1.php';
}

//make sure config file is writeable, database preferences, write out config file
if ($step == 2) {
	include 'include/step2.php';
}

/* preferences */
if ($step == 3) {	
	include 'include/step3.php';
}

if ($step == 4) {	
	include 'include/step4.php';
}

/* directory permissions and generating the config.inc.php file */
if ($step == 5) {	
	include 'include/step5.php';
}

if ($step == 6) {	
	include 'include/step7.php';
}

/*
if ($step == 7) {	
	include 'include/step7.php';
}
*/

require 'include/footer.php';

?>