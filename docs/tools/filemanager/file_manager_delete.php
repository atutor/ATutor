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

$page = 'file_manager_delete';

define('AT_INCLUDE_PATH', '../../include/');
$_ignore_page = true; /* used for the close the page option */
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');
if (!$_GET['f']) {
	$_SESSION['done'] = 0;
}
session_write_close();
$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('file_manager');
$_section[1][1] = 'tools/filemanager/index.php';
$_section[2][0] = _AT('file_manager_delete');
$_section[2][1] = 'tools/filemanager/file_manager_delete.php';

authenticate(AT_PRIV_FILES);

$_header_file = 'header.inc.php';
$_footer_file = 'footer.inc.php';

$current_path = AT_CONTENT_DIR . $_SESSION['course_id'].'/';

if (isset($_POST['cancel'])) {
	header('Location: index.php?pathext='.urlencode($_POST['pathext']));
	exit;
}
$start_at = 3;

if ($_POST['pathext'] != '') {
	$pathext = urldecode($_POST['pathext']);
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

if (isset($_POST['yes'])) {
	/* delete files and directories */
	/* delete the file  */
	if (isset($_POST['listoffiles']))  {
		
		$checkbox = explode(',',$_POST['listoffiles']);
		$count = count($checkbox);
		
		for ($i=0; $i<$count; $i++) {
			$filename=$checkbox[$i];
			@unlink($current_path.$pathext.$filename);
			
		}
	}
	/* delete directory */
	if (isset($_POST['listofdirs'])) {
				
		$checkbox = explode(',',$_POST['listofdirs']);
		$count = count($checkbox);
		
		for ($i=0; $i<$count; $i++) {
			$filename=$checkbox[$i];
				
			if (strpos($filename, '..') !== false) {
				$errors[] = AT_ERROR_UNKNOWN;
				print_errors($errors);
				require(AT_INCLUDE_PATH.$_footer_file);
				exit;
			}

			if (!is_dir($current_path.$pathext.$filename)) {
				$errors[]=AT_ERROR_DIR_NOT_DELETED;
				print_errors($errors);
				require(AT_INCLUDE_PATH.$_footer_file);
				exit;
			}

			$result = clr_dir($current_path.$pathext.$filename);
			if (!$result) {
				$errors[]   = AT_ERROR_DIR_NO_PERMISSION;
				print_errors($errors);
				require(AT_INCLUDE_PATH.$_footer_file);
				exit;
			} 
		
		}
	}
	if (isset($_POST['listoffiles']) && isset($_POST['listofdirs'])) {
		header('Location: index.php?f='.AT_FEEDBACK_FILE_DELETED.SEP.'f='.AT_FEEDBACK_DIR_DELETED.SEP.'pathext='.urlencode($pathext));
	}else if (isset($_POST['listoffiles'])) {
		header('Location: index.php?f='.AT_FEEDBACK_FILE_DELETED.SEP.'pathext='.urlencode($pathext));
	} else if (isset($_POST['listofdirs'])) {
		header('Location: index.php?f='.AT_FEEDBACK_DIR_DELETED.SEP.'pathext='.urlencode($pathext));
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
	echo _AT('file_manager_delete');
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


/* check that at least one checkbox checked */		
		// $_POST['check'][0] = filename;
if (isset($_POST['deletefiles'])) {
	if (!is_array($_POST['check'])) {
		// error: you must select a file/dir to delete
		$errors[] = AT_ERROR_NO_FILE_SELECT;		
	} else {
		/* confirm delete */
		/* find the files and directories to be deleted */
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
			
		// save $_POST['check'] into a hidden post variable
		echo '<form name="form1" action="'.$_SERVER['PHP_SELF'].'" method="post">'."\n";

		 
		echo '<input type="hidden" name="pathext" value="'.$pathext.'" />'."\n";
		if (isset($files)) {
			$list_of_files = implode(',', $files);
			echo '<input type="hidden" name="listoffiles" value="'.$list_of_files.'" />'."\n"; 
			$warnings[]=array(AT_WARNING_CONFIRM_FILE_DELETE, $list_of_files);
		}
		if (isset($dirs)) {
			$list_of_dirs = implode(',', $dirs);
			echo '<input type="hidden" name="listofdirs" value="'.$list_of_dirs.'" />'."\n";
			$warnings[]=array(AT_WARNING_CONFIRM_DIR_DELETE, $list_of_dirs);
		}

		print_warnings($warnings);
		echo '<input type="submit" name="yes" value="'._AT('yes_delete').'" /><input type="submit" name="cancel" value="'._AT('no_cancel').'"/>'."\n";
		echo '</form>';
		require(AT_INCLUDE_PATH.$_footer_file);
		exit;
	}

}

require(AT_INCLUDE_PATH.'html/feedback.inc.php');
echo '<form name="form1" action="'.$_SERVER['PHP_SELF'].'" method="post">'."\n";
echo '<input type="hidden" name="pathext" value="'.$pathext.'" />';
echo '<input type="submit" name="cancel" value="'._AT('return_file_manager').'" class="button" /></form>';
require(AT_INCLUDE_PATH.$_footer_file);
?>
