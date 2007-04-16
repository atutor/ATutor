<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg, Heidi Hazelton	*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
$_user_location	= 'public';

require(AT_INCLUDE_PATH.'vitals.inc.php');

$at_md5 = md5($_SERVER['SERVER_ADDR'] . DB_NAME);

/* make sure AT and AC are using the same server and database */
if (isset($_GET['m']) && ($at_md5 == $_GET['m'])) {
	$results = VERSION."\n".TABLE_PREFIX;
} else {
	$results = 0;
}

echo $results;
exit;

?>