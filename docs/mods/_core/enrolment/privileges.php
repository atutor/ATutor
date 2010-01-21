<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: privileges.php 7208 2008-01-09 16:07:24Z greg $
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

if (!authenticate(AT_PRIV_ADMIN, true)) {
	$msg->addError('ACCESS_DENIED');
	header('Location: index.php');
	exit;
}

$course_id = $_SESSION['course_id'];

require(AT_INCLUDE_PATH.'../mods/_core/enrolment/html/privileges.inc.php');
exit;
?>