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

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

require(AT_INCLUDE_PATH.'header.inc.php');

@readfile(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/tran/'.$_GET['t'].'.html');
echo '</table>';
require(AT_INCLUDE_PATH.'footer.inc.php');
?>