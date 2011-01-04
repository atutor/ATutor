<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2010                                             */
/* Inclusive Design Institute	                                       */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id: preferences.php 10055 2010-06-29 20:30:24Z cindy $
define('AT_INCLUDE_PATH', '../../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

//save config files.
if(isset($_POST['submit'])){
	$max_memory = intval($_POST['pa_max_memory']);
	if ($max_memory <= 0){
		$msg->addError('PA_MEMORY_INPUT_ERROR');
	} else {
		$sql = 'UPDATE '.TABLE_PREFIX."config SET value='$max_memory' WHERE name='pa_max_memory_per_member'";
		$result = mysql_query($sql, $db);
		if ($reuslt===false){
			$msg->addError('PA_MEMORY_SQL_ERROR');
		} else {
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
			header('Location: preferences.php');
			exit;
		}
	}
}

require (AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('max_memory', $_config['pa_max_memory_per_member']);
$savant->display('photos/admin/pa_preferences.tmpl.php');
require (AT_INCLUDE_PATH.'footer.inc.php');
?>
