<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/


$_include_path = '../include/';
require($_include_path.'vitals.inc.php');
$_section[0][0] = _AT('help');
$_section[0][1] = 'help/';
$_section[1][0] = _AT('atutor_accessibility');

require($_include_path.'header.inc.php');

echo '<h2><img src="images/icons/default/square-large-help.gif" width="42" height="38" class="menuimage" border="0" alt="" /><a href="help/?g=11">'._AT('help').'</a></h2>';

echo '<h3>';
	if($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2){
		echo '<img src="'.$_base_path.'images/icons/default/access-large.gif" width="46" height="37" hspace="3"  class="menuimage16" vspace="3" border="0" alt="" />';
	}

echo _AT('atutor_accessibility').'</h3>';

echo _AT('atutor_accessibility_text');

require($_include_path.'footer.inc.php');
?>
