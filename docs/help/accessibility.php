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

echo '<h2><img src="images/icons/default/square-large-help.gif" width="42" height="38" class="menuimage" border="0" alt="" /><a href="help/index.php?g=11">'._AT('help').'</a></h2>';

echo '<h3>';
	if($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2){
		echo '<img src="'.$_base_path.'images/icons/default/access-large.gif" width="46" height="37" hspace="3"  class="menuimage16" vspace="3" border="0" alt="" />';
	}

echo _AT('atutor_accessibility').'</h3>';

echo _AT('atutor_accessibility_text');

require(AT_INCLUDE_PATH.'footer.inc.php');
?>