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

$page = 'file_manager_copysub';

define('AT_INCLUDE_PATH', '../../include/');
$_ignore_page = true; /* used for the close the page option */
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');
require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

if (!$_GET['f']) {
	$_SESSION['done'] = 0;
}
session_write_close();
$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('file_manager');
$_section[1][1] = 'tools/filemanager/index.php';
$_section[2][0] = _AT('file_manager_copy');
$_section[2][1] = 'tools/filemanager/file_manager_copysub.php';

authenticate(AT_PRIV_FILES);

$_header_file = 'header.inc.php';
$_footer_file = 'footer.inc.php';

$current_path = AT_CONTENT_DIR . $_SESSION['course_id'].'/';

$start_at = 3;

if ($_POST['pathext'] != '') {
	$pathext =$_POST['pathext'];
}

if ($pathext != '') {
	$bits = explode('/', $pathext);
	$bits_path = $bits[0];

	for ($i=0; $i<count($bits)-2; $i++) {
		if ($bits_path != $bits[0]) {
			$bits_path .= '/'.$bits[$i];
		}
		$_section[$start_at][0] = $bits[$i];
		$_section[$start_at][1] = 'tools/filemanager/index.php?back=1'.SEP.'pathext='.$bits_path.'/'.$bits[$i+1].'/';

		$start_at++;
	}
	$_section[$start_at][0] = $bits[count($bits)-2];
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php?pathext='.urlencode($_POST['pathext']));
	exit;
}
if (isset($_POST['copy_action'])) {
	$dest = $_POST['new_dir'];
	if (strtolower($dest) == "home") {
		$dest = $current_path;
	} else {
		$dest = $current_path.$dest.'/';
	}

	if (!is_dir($dest)) {
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
				$result = copys($current_path.$pathext.$source, $dest.$source);
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
		header('Location: index.php?pathext='.urlencode($pathext));
	}
	
}
require(AT_INCLUDE_PATH.$_header_file);

echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/square-large-tools.gif" border="0" vspace="2"  class="menuimageh2" width="42" height="40" alt="" />';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo ' <a href="tools/" class="hide" >'._AT('tools').'</a>';
}
echo '</h2>'."\n";

echo '<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '&nbsp;<img src="images/icons/default/file-manager-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo ' <a href="tools/filemanager/" class="hide" >'._AT('file_manager').'</a>';
}
echo '</h3>'."\n";

echo '<h4>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '&nbsp;&nbsp;<img src="images/icons/default/file-manager-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo _AT('file_manager_copy');
}
echo '</h4>'."\n";

/* listing of path to current directory */
echo '<p>'._AT('current_path').' ';
echo '<small>';
echo '<a href="tools/filemanager/index.php">'._AT('home').'</a> / ';
if ($pathext != '') {
	$bits = explode('/', $pathext);
	$bits_path = $bits[0];
	for ($i=0; $i<count($bits)-2; $i++) {
		if ($bits_path != $bits[0]) {
			$bits_path .= '/'.$bits[$i];
		}
		echo '<a href="tools/filemanager/index.php?back=1'. SEP .'pathext='. $bits_path .'/'. $bits[$i+1] .'/">'.$bits[$i].'</a>'."\n";
		echo ' / ';
	}
	echo $bits[count($bits)-2];
}
echo '</small>'."\n";

if (isset($_POST['copyfilesub'])) {
	if (!is_array($_POST['check']) && (!isset($_POST['listoffiles']) && !isset($_POST['listofdirs']))) {
		// error: you must select a file/dir 
		$msg->addError('NO_FILE_SELECT');
	} else {
		/* find the files and directories to be copied */
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

		echo '<form name="form1" action="'.$_SERVER['PHP_SELF'].'" method="post">'."\n";
		echo '<input type="hidden" name="pathext" value="'.$pathext.'" />'."\n";
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
		$msg->printAll();
		echo '<p> Destination Directory ';
		echo '<input type="text" name="new_dir" />';
		echo '<input type="submit" name="copy_action" value="'._AT('copy').'" /><input type="submit" name="cancel" value="'._AT('cancel').'"/></p>'."\n";
		echo '</form>';
		require(AT_INCLUDE_PATH.$_footer_file);
		exit;
	}	
} 

?>
