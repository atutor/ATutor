<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: get_acheck.php 2291 2004-11-16 19:35:41Z joel $

/* call it:
 * ATUTOR_PATH/get_rss.php?course=COURSE_ID;type=[FORUMS|NEWS];version=RSS_VERSION


	a much nicer way to call the feed would be:
	get_rss.php?COURSE_ID-TYPE-VERSION

	COURSE_ID: integer value of the course (non-zero)
	TYPE: integer where 1 is FORUMS, and 2 is NEWS, 0 is reserved
	VERSION: 

 */

$_user_location	= 'public';

define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH . '/vitals.inc.php');

if (!isset($_GET['course'], $_GET['type'], $_GET['version'])) {
	header('HTTP/1.1 404 Not Found');
	exit;
}

$file = AT_CONTENT_DIR . 'feeds/' . $_GET['course'] . '/' . $_GET['type'] . '.' . $_GET['version'] . '.xml';
// feeds/223/forums.1.xml

//check that this file is within the content directory & exists

$real = @realpath($file);

if ($real && (substr($real, 0, strlen(AT_CONTENT_DIR)) == AT_CONTENT_DIR)) {
 	header('Content-Type: text/xml');
	header('Content-Length: ' . filesize($real));
	echo file_get_contents($real);
	exit;
} else {
	header('HTTP/1.1 404 Not Found');
	exit;
}

?>