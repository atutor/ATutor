<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
/* linked from admin/users.php                                  */
/* deletes a user from the system.                              */
/****************************************************************/
// $Id: admin_delete.php 6644 2006-11-01 17:24:25Z joel $

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_USERS);

$ids = explode(',', $_REQUEST['ids']);
$status = intval($_REQUEST['status']);

if (isset($_POST['submit_yes'])) {

	foreach ($ids as $id) {
		$sql = "UPDATE ".TABLE_PREFIX."members SET status=".$status." WHERE member_id=".intval($id);
		$result = mysql_query($sql,$db);
	}

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: '.$_base_href.'admin/users.php');
	exit;

} else if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.$_base_href.'admin/users.php');
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