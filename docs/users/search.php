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
// $Id: search.php 1388 2004-08-18 15:43:12Z joel $

$page	 = 'search';
$_user_location = 'users';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/search.inc.php');

$_section[0][0] = _AT('search_courses');

require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h2>'._AT('search').'</h2>';

require(AT_INCLUDE_PATH.'html/search.inc.php');

require(AT_INCLUDE_PATH.'footer.inc.php');
?>