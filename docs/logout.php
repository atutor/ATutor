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

$section = 'users';
$_public	= true;
$_include_path = 'include/';
require ('include/vitals.inc.php');

$sql = "DELETE FROM ".TABLE_PREFIX."users_online WHERE member_id=$_SESSION[member_id]";
@mysql_query($sql, $db);

session_destroy(); 
session_unset();


require($_include_path.'basic_html/header.php');
print_feedback(AT_FEEDBACK_LOGOUT);
echo _AT('logged_out');
require($_include_path.'basic_html/footer.php'); 

?>