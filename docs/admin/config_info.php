<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

$page = 'server_configuration';
$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if ($_SESSION['course_id'] > -1) { exit; }

require(AT_INCLUDE_PATH.'header.inc.php'); 

echo '<p>ATutor '._AT('version').': <strong>'.VERSION.'</strong> - <a href="http://atutor.ca/check_atutor_version.php?v='.urlencode(VERSION).'">'._AT('check_latest_version').'</a></p>';

echo '<h3>'._AT('fix_content_ordering').'</h3>';
echo '<p>'._AT('fix_content_ordering_text').'</p>';

require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>