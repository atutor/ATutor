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

if (isset($_POST['copy_action'])) {
	$dest = $_POST['dest'];
	
	if (!is_dir($current_path.$dest)) {
		$msg->addError(array('DIR_NOT_EXIST',$_POST['new_dir']));
		$_POST['copyfilesub']='copy';
	} else {
		// copy directories
		if (isset($_POST['listofdirs'])) {
			$dirs = explode(',',$_POST['listofdirs']);
			$count = count($dirs);
			$j=0;
			$k=0;
			for ($i = 0; $i < $count; $i++) {
				$source = $dirs[$i];
				$result = copys($current_path.$pathext.$source, $current_path.$dest.$source);
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
			$files = explode(',',$_POST['listoffiles']);
			$count = count($files);
			$j=0;
			$k=0;
			for ($i = 0; $i < $count; $i++) {
				$source = $files[$i];
				$result = @copy($current_path.$pathext.$source, $dest.$source);
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
}

if (isset($_POST['copyfilesub'])) {
	if (!is_array($_POST['check']) && (!isset($_POST['listoffiles']) && !isset($_POST['listofdirs']))) {
		// error: you must select a file/dir 
		$msg->addError('NO_FILE_SELECT');
	} else {
		// find the files and directories to be copied 
		if (isset($_POST['check'])) {
			$count = count($_POST['check']);
			$countd = 0;
			$countf = 0;
			for ($i=0; $i<$count; $i++) {
				if (is_dir($current_path.$pathext.$_POST['check'][$i])) {
					$dirs[$countd] = $_POST['check'][$i];
					$countd++;
				} else {
					$files[$countf] = $_POST['check'][$i];
					$countf++;
				}
			}
		} else {
			if (isset($_POST['listoffiles'])) 
				$files = explode(',',$_POST['listoffiles']);
			if (isset($_POST['listofdirs'])) 
				$dirs = explode(',',$_POST['listofdirs']);
		}
		if ($_POST['dir_list_top'] == $_POST['dir_list_bottom']) {
			$dest = $_POST['dir_list_top'];
		} else if (($_POST['dir_list_top'] != "")&& ($_POST['dir_list_bottom'] == "")) {
			$dest =  $_POST['dir_list_top'];
		} else if (($_POST['dir_list_bottom'] != "") && ($_POST['dir_list_top'] == "")) {
			$dest =  $_POST['dir_list_bottom'];
		} else {
			$dest = $_POST['dir_list_top'];
		}
		echo '<form name="form1" action="'.$_SERVER['PHP_SELF'].'?pathext='.urlencode($pathext).'" method="post">'."\n";
		echo '<input type="hidden" name="pathext" value="'.$pathext.'" />'."\n";
		echo '<input type="hidden" name="dest" value="'.$dest.'" />'."\n";
		if (isset($files)) {
			$list_of_files = implode(',', $files);
			echo '<input type="hidden" name="listoffiles" value="'.$list_of_files.'" />'."\n"; 
			$msg->addWarning(array('CONFIRM_FILE_COPY', $list_of_files));
		}
		if (isset($dirs)) {
			$list_of_dirs = implode(',', $dirs);
			echo '<input type="hidden" name="listofdirs" value="'.$list_of_dirs.'" />'."\n";
			$msg->addWarning(array('CONFIRM_DIR_COPY', $list_of_dirs));
		}
		$msg->printWarnings();
		echo '<input type="submit" name="copy_action" value="'._AT('copy').'" />';
		echo '<input type="submit" name="cancel" value="'._AT('cancel').'"/></p>'."\n";
		echo '</form>';

	}	
} 

?>
