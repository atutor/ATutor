<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
}

if (isset($_POST['submit_yes']) && $_POST['action'] == 'delete') {
	/* delete files and directories */
	/* delete the file  */
	if (isset($_POST['listoffiles']))  {

		$checkbox = explode(',',$_POST['listoffiles']);
		$count = count($checkbox);
		$result=true;
		for ($i=0; $i<$count; $i++) {
			$filename=$checkbox[$i];
			if (!(@unlink($current_path.$pathext.$filename))) {
				$msg->addError('FILE_NOT_DELETED');
				$result=false;
				break;
			}
			
		}
		if ($result) 
			$msg->addFeedback('FILE_DELETED');
		
	}
	/* delete directory */
	if (isset($_POST['listofdirs'])) {
				
		$checkbox = explode(',',$_POST['listofdirs']);
		$count = count($checkbox);
		$result=true;
		for ($i=0; $i<$count; $i++) {
			$filename=$checkbox[$i];
				
			if (strpos($filename, '..') !== false) {
				$msg->addError('UNKNOWN');
				$result=false;
				break;
			} else if (!is_dir($current_path.$pathext.$filename)) {
				$msg->addError(array('DIR_NOT_DELETED',$filename));
				$result=false;
				break;
			} else if (!($result = clr_dir($current_path.$pathext.$filename))) { 
				$msg->addError('DIR_NO_PERMISSION');
				$reault=false;
				break;
			} 
		}
		if ($result)
			$msg->addFeedback('DIR_DELETED');
	}
}

if ((isset($_POST['action']) && $_POST['action_list'] == 'delete') ||
	(isset($_POST['action_down']) && $_POST['action_list_down'] == 'delete' )) {
	if (!is_array($_POST['check'])) {
		$msg->addError('NO_FILE_SELECT');
	} else {
		echo '<h3>';
		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
			echo '&nbsp;<img src="images/icons/default/file-manager-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
		}
		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
			echo _AT('file_manager_delete')."\n";
		}
		echo '</h3>'."\n";
		// confirm delete 
		// find the files and directories to be deleted 
		$count = count($_POST['check']);
		$countd = 0;
		$countf = 0;
		for ($i=0; $i<$count; $i++) {
			if (is_dir($current_path.$pathext.$_POST['check'][$i])) {
				$_dirs[$countd] = $_POST['check'][$i];
				$countd++;
			} else {
				$_files[$countf] = $_POST['check'][$i];
				$countf++;
			}
		}
				
		$hidden_vars['pathext'] = $pathext;
		$hidden_vars['action']  = 'delete';

		if (isset($_files)) {
			$list_of_files = implode(',', $_files);
			$hidden_vars['listoffiles'] = $list_of_files;
			$msg->addConfirm(array('FILE_DELETE', $list_of_files), $hidden_vars);
		}
		if (isset($_dirs)) {
			$list_of_dirs = implode(',', $_dirs);
			$hidden_vars['listofdirs'] = $list_of_dirs;
			$msg->addConfirm(array('DIR_DELETE',$list_of_dirs), $hidden_vars);
		}

		$msg->printConfirm();
		echo '<hr size="4" width="100%">';		
	}
}

?>