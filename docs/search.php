<?php
/************************************************************************/
/* ATutor														        */
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg & Boon-Hau Teh */
/* Adaptive Technology Resource Centre / University of Toronto          */
/* http://atutor.ca												        */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.				        */
/************************************************************************/
$page	 = 'search_courses';
$_user_location = 'public';

define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

require(AT_INCLUDE_PATH.'html/feedback.inc.php');


$_SECTION[0][0] = _AT('home');
$_SECTION[0][1] = '/index.php';
$_SECTION[1][0] = _AT('course_search');

require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h3>'._AT('search_courses').'</h3>';

require(AT_INCLUDE_PATH.'html/search.inc.php');

require(AT_INCLUDE_PATH.'footer.inc.php');