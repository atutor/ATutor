<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca                                             */
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.                */
/****************************************************************/
// $Id$

$_user_location	= 'public';
define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

$sql = "DELETE FROM ".TABLE_PREFIX."users_online WHERE member_id=$_SESSION[member_id]";
@mysql_query($sql, $db);

// Unset these Session keys at the time of logout.
$unset_session = array('login',
                       'valid_user',
                       'member_id',
                       'is_admin',
                       'course_id',
                       'prefs',
                       'dd_question_ids',
                       'flash',
                       'userAgent',
                       'IPaddress',
                       'OBSOLETE',
                       'EXPIRES',
                       'redirect_to',
                       'token');
foreach ($unset_session as $session_name) {
    unset($_SESSION[$session_name]);
}
$msg->addFeedback('LOGOUT');
header('Location: login.php');
exit;

?>