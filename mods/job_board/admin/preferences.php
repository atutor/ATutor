<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2009											   */
/* Adaptive Technology Resource Centre / Inclusive Design Institute	   */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

admin_authenticate(AT_ADMIN_PRIV_JOB_BOARD); 

//handle submit
if(isset($_POST['submit'])){
	$posting_approval = intval($_POST['jb_posting_approval']);
	if ($posting_approval > 1){
		$posting_approval = 1;
	} 
	$sql = 'REPLACE INTO '.TABLE_PREFIX."config SET value='$posting_approval', name='jb_posting_approval'";
	$result = mysql_query($sql, $db);
	if ($reuslt===false){
		$msg->addError('DB_NOT_UPDATED');
	} else {
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: preferences.php');
		exit;
	}
}

include(AT_INCLUDE_PATH.'header.inc.php');
$savant->display('admin/jb_preferences.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php'); 
?>
