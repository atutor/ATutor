<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
$page = 'help';
$_user_location	= 'users';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
$_section[0][0] = _AT('help');
$_section[0][1] = 'help/';
$_section[1][0] = _AT('atutor_accessibility');

require(AT_INCLUDE_PATH.'header.inc.php');

echo _AT('atutor_accessibility_text');

require(AT_INCLUDE_PATH.'footer.inc.php');
?>