<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca                                             */
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.                */
/****************************************************************/

$_user_location	= 'public';
define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

$sql = "DELETE FROM ".TABLE_PREFIX."users_online WHERE member_id=$_SESSION[member_id]";
@mysql_query($sql, $db);

session_destroy(); 
session_unset();

require(AT_INCLUDE_PATH.'header_footer/header.inc.php');
$savant->display('include/basic_html/public_nav.tmpl.php');

print_feedback(AT_FEEDBACK_LOGOUT);
require(AT_INCLUDE_PATH.'header_footer/footer.inc.php');

?>