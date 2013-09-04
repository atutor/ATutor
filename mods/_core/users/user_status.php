<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
/* linked from admin/users.php                                  */
/* deletes a user from the system.                              */
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_USERS);

$ids = explode(',', $_REQUEST['ids']);
$status = intval($_REQUEST['status']);

if (isset($_POST['submit_yes'])) {

	foreach ($ids as $id) {
		//make sure not instructor of a course
		$id = intval($id);
		
		$sql	= "SELECT course_id FROM %scourses WHERE member_id=$id";
		$rows_courses = queryDB($sql, array(TABLE_PREFIX, $id));
		
		if(count($rows_courses) == 0){
		
			$sql2 = "UPDATE %smembers SET status=%d, creation_date = creation_date WHERE member_id=%d";
			$result2 = queryDB($sql2,array(TABLE_PREFIX, $status, $id));
		}
	}

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: '.AT_BASE_HREF.'mods/_core/users/users.php');
	exit;

} else if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'mods/_core/users/users.php');
	exit;
}


require(AT_INCLUDE_PATH.'header.inc.php'); 

$names = get_login($ids);
$names_html = '<ul>'.html_get_list($names).'</ul>';
$status_name = get_status_name($status);

$hidden_vars['ids'] = implode(',', array_keys($names));
$hidden_vars['status'] = $status;

$confirm = array('EDIT_STATUS', $status_name, $names_html);
$msg->addConfirm($confirm, $hidden_vars);
$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>