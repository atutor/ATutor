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

$page = 'file_manager_copy';

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
$_section[2][0] = _AT('file_manager_copy');
$_section[2][1] = 'tools/filemanager/file_manager_copy.php';

authenticate(AT_PRIV_FILES);

if ($_GET['frame'] == 1) {
	$_header_file = 'html/frameset/header.inc.php';
	$_footer_file = 'html/frameset/footer.inc.php';
} else {
	$_header_file = 'header.inc.php';
	$_footer_file = 'footer.inc.php';
}

$current_path = AT_CONTENT_DIR . $_SESSION['course_id'].'/';

if (isset($_POST['cancel'])) {
	header('Location: index.php');
	exit;
}
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

require(AT_INCLUDE_PATH.$_header_file);

if ($_GET['frame']) {
	echo '<table width="100%" cellpadding="0" cellspacing="0"><tr><td class="cat2"></td></tr></table>'."\n";
	echo '<div align="center"><small>(<a href="close_frame.php" target="_top">'._AT('close_frame').'</a>)</small></div>'."\n";
}


if($_GET['frame'] == 1){
	echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<img src="images/icons/default/square-large-tools.gif" border="0" vspace="2"  class="menuimageh2" width="42" height="40" alt="" />';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo ' <a href="tools/" class="hide" target="content">'._AT('tools').'</a>';
	}
	echo '</h2>'."\n";
}else{
	echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<img src="images/icons/default/square-large-tools.gif" border="0" vspace="2"  class="menuimageh2" width="42" height="40" alt="" />';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo ' <a href="tools/" class="hide" >'._AT('tools').'</a>';
	}
	echo '</h2>'."\n";
}


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
	echo _AT('file_manager_copy_file');
}
echo '</h4>'."\n";

/* link for frame and listing of path to current directory */
if ($_GET['frame'] != 1) {
	echo '<p><a href="frame.php?p='.urlencode($_my_uri).'">'._AT('open_frame').'</a>.</p>'."\n";
	echo '<p>'._AT('current_path').' ';
}
echo '<small>';
echo '<a href="tools/filemanager/index.php?frame='.$_GET['frame'].'">'._AT('home').'</a> / ';
if ($pathext != '') {
	$bits = explode('/', $pathext);
	$bits_path = $bits[0];
	for ($i=0; $i<count($bits)-2; $i++) {
		if ($bits_path != $bits[0]) {
			$bits_path .= '/'.$bits[$i];
		}
		echo '<a href="tools/filemanager/index.php?back=1'. SEP .'pathext='. $bits_path .'/'. $bits[$i+1] .'/'.SEP.'frame='.$_GET[frame].'">'.$bits[$i].'</a>'."\n";
		echo ' / ';
	}
	echo $bits[count($bits)-2];
}
echo '</small>'."\n";



if (isset($_POST['copyfile'])) {
	if (!is_array($_POST['checkbox'])) {
		// error: you must select a file/dir
		$errors[] = AT_ERROR_NO_FILE_SELECT;
	} else {
		/* find the files and directories to be copied */
		$count = count($_POST['checkbox']);
		$countd = 0;
		$countf = 0;
		for ($i=0; $i<$count; $i++) {
			if (is_dir($current_path.$pathext.$_POST['checkbox'][$i])) {
				$dirs[$countd] = $_POST['checkbox'][$i];
				$countd++;
			} else {
				$files[$countf] = $_POST['checkbox'][$i];
				$countf++;
			}
		}
		echo '<form name="form1" action="'.$_SERVER['PHP_SELF'].'?frame='.$_GET['frame'].'" method="post">'."\n";
		echo '<input type="hidden" name="pathext" value="'.$pathext.'" />'."\n";
		if (isset($files)) {
			$list_of_files = implode(',', $files);
			echo '<input type="hidden" name="listoffiles" value="'.$list_of_files.'" />'."\n"; 
			$warnings[]=array(AT_WARNING_CONFIRM_FILE_COPY, $list_of_files);
		}
		if (isset($dirs)) {
			$list_of_dirs = implode(',', $dirs);
			echo '<input type="hidden" name="listofdirs" value="'.$list_of_dirs.'" />'."\n";
			$warnings[]=array(AT_WARNING_CONFIRM_DIR_COPY, $list_of_dirs);
		}
		print_warnings($warnings);
		echo '<input type="submit" name="copy_action" value="'._AT('copy').'" /><input type="submit" name="cancel" value="'._AT('cancel').'"/></p>'."\n";
		echo '</form>';
		require(AT_INCLUDE_PATH.$_footer_file);
		exit;
	}

		
} else if (isset($_POST['copy_action'])) {
	$dest = $pathext;
	if (!is_dir($current_path.$dest)) {
		$errors[] = AT_ERROR_DIR_NOT_EXIST;
	} else {
		if (isset($_POST['listofdirs'])) {
			$dirs = explode(',',$_POST['listofdirs']);
			$count = count($dirs);
			
			for ($i = 0; $i < $count; $i++) {
				$source = $dirs[$i];
				$result = copys($current_path.$pathext.$source, $current_path.$dest.'/copy_of_'.$source);
				if (!$result) {
					$errors[] = AT_ERROR_COPY_DIR;
					break;
				}

			}
			if ($result)
				$feedback[] = array(AT_FEEDBACK_COPIED_DIRS,$_POST['listofdirs'],$dest);
		}
		if (isset($_POST['listoffiles'])) {
			$files = explode(',',$_POST['listoffiles']);
			$count = count($files);

			for ($i = 0; $i < $count; $i++) {
				$source = $files[$i];
				$result = @copy($current_path.$pathext.$source, $current_path.$dest.'/copy_of_'.$source);
				if (!$result) {
					$errors[] = AT_ERROR_COPY_FILE; 
					break;
				}
			}
			if ($result)
				$feedback[] = array(AT_FEEDBACK_COPIED_FILES,$_POST['listoffiles'],$dest);
		}
	}
}




require(AT_INCLUDE_PATH.'html/feedback.inc.php');
echo '<form name="form1" action="'.$_SERVER['PHP_SELF'].'?frame='.$_GET['frame'].'" method="post">'."\n";
echo '<input type="submit" name="cancel" value="Return to File Manager" /></form>';

require(AT_INCLUDE_PATH.$_footer_file);
?>
