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


if (isset($_POST['yes'])) {
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

if (isset($_POST['deletefiles'])) {
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
				
		// save $_POST['check'] into a hidden post variable
		echo '<form name="form1" action="'.$_SERVER['PHP_SELF'].'" method="post">'."\n";

		 
		echo '<input type="hidden" name="pathext" value="'.$pathext.'" />'."\n";
		if (isset($_files)) {
			$list_of_files = implode(',', $_files);
			echo '<input type="hidden" name="listoffiles" value="'.$list_of_files.'" />'."\n"; 
			$msg->addWarning(array('CONFIRM_FILE_DELETE', $list_of_files));
		}
		if (isset($_dirs)) {
			$list_of_dirs = implode(',', $_dirs);
			echo '<input type="hidden" name="listofdirs" value="'.$list_of_dirs.'" />'."\n";
			$msg->addWarning(array('CONFIRM_DIR_DELETE', $list_of_dirs));
		}

		$msg->printWarnings();
		echo '<p align="center">';
		echo '<input type="submit" name="yes" value="'._AT('yes_delete').'" class="button"/>';
		echo '- <input type="submit" name="cancel" value="'._AT('no_cancel').'" class="button"/>'."\n";
		echo '</p>';
		echo '</form>';
		echo '<hr size="4" width="100%">';		
	}
}

?>