<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_SOCIAL);

if($_POST['save']){
	$shindig_url = $addslashes($_POST['shindig_url']);
	
	$sql = "REPLACE into %sconfig (name,value) VALUES('shindig_url','%s')";
	$result = queryDB($sql, array(TABLE_PREFIX, $shindig_url));
	
	if($result > 0){
		 $msg->addFeedback('SOCIAL_SETTINGS_SAVED');
	}else{
 		$msg->addError('SOCIAL_SETTINGS_NOT_SAVED');
	}
	header("Location: ".$_SERVER['PHP_SELF']);
	exit;
}

require (AT_INCLUDE_PATH.'header.inc.php');

$savant->display('admin/system_preferences/index_admin.tmpl.php');
require (AT_INCLUDE_PATH.'footer.inc.php'); ?>