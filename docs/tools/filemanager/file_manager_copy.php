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

/*if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
}

if (isset($_POST['submit_yes'])) {

	// copy directories
	if (isset($_POST['listofdirs'])) {
		$_dirs = explode(',',$_POST['listofdirs']);
		$count = count($_dirs);
		$j=0;
		$k=0;
		for ($i = 0; $i < $count; $i++) {
			$source = $_dirs[$i];
			$result = copys($current_path.$pathext.$source, $current_path.$pathext.$source.'_copy');
			if (!$result) {
				$notcopied[j] = $source;	
				$j++;
			} else {
				$copied[k] = $source;
				$k++;
			}
		}
		if (is_array($notcopied)) {
			$msg->addError(array('DIR_NOT_COPIED',implode(',',$notcopied)));
		}
		if (is_array($copied)) {
			$msg->addFeedback(array('DIRS_COPIED',implode(',',$copied)));
		}

	}
	// copy files
	if (isset($_POST['listoffiles'])) {
		$_files = explode(',',$_POST['listoffiles']);
		$count = count($_files);
		$j=0;
		$k=0;
		for ($i = 0; $i < $count; $i++) {
			$source = $_files[$i];
			// get the name and extensions out of the source
			$file = explode('.',$source);
			$name = $file[0];
			$file[0] = '.';
			$ext = implode('',$file);

			$result = @copy($current_path.$pathext.$source, $current_path.$pathext.$name.'_copy'.$ext);
			if (!$result) {
				$notcopied[j] = $source;	
				$j++;
			} else {
				$copied[k] = $source;
				$k++;
			}
		}
		if (is_array($notcopied)) {
			$msg->addError(array('FILE_NOT_COPIED',implode(',',$notcopied)));
		}
		if (is_array($copied)) {
			$msg->addFeedback(array('FILES_COPIED',implode(',',$copied)));
		}
	}
}
if (isset($_POST['copyfile'])) {


	if (!is_array($_POST['check']) && (!isset($_POST['listoffiles']) && !isset($_POST['listofdirs']))) {
		// error: you must select a file/dir 
		$msg->addError('NO_FILE_SELECT');
	} else {
		echo '<h3>';
		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
			echo '&nbsp;<img src="images/icons/default/file-manager-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
		}
		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
			echo _AT('file_manager_copy')."\n";
		}
		echo '</h3>'."\n";
		// find the files and directories to be copied 
		if (isset($_POST['check'])) {
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
		} else {
			if (isset($_POST['listoffiles'])) 
				$_files = explode(',',$_POST['listoffiles']);
			if (isset($_POST['listofdirs'])) 
				$_dirs = explode(',',$_POST['listofdirs']);
		}

		$hidden_vars['pathext'] = $pathext;
		$hidden_vars['dest']    = $dest;

		if (isset($_files)) {
			$list_of_files = implode(',', $_files);

			$hidden_vars['listoffiles'] = $list_of_files;
			$msg->addConfirm(array('FILE_COPY', $list_of_files), $hidden_vars);
		}
		if (isset($_dirs)) {
			$list_of_dirs = implode(',', $_dirs);
			$hidden_vars['listofdirs'] = $list_of_dirs;
			$msg->addConfirm(array('DIR_COPY', $list_of_dirs), $hidden_vars);
		}
		$msg->printConfirm();
		echo '<hr size="4" width="100%">';
	}	
} 
*/
?>