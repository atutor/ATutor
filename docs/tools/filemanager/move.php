<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');

authenticate(AT_PRIV_FILES);

$current_path = AT_CONTENT_DIR.$_SESSION['course_id'].'\\';

if (($_REQUEST['popup'] == TRUE) || ($_REQUEST['framed'] == TRUE)) {
	$_header_file = AT_INCLUDE_PATH.'fm_header.php';
	$_footer_file = AT_INCLUDE_PATH.'fm_footer.php';
} else {
	$_header_file = AT_INCLUDE_PATH.'header.inc.php';
	$_footer_file = AT_INCLUDE_PATH.'footer.inc.php';
}
$popup = $_REQUEST['popup'];
$framed = $_REQUEST['framed'];

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup']);
	exit;
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_REQUEST['framed'].SEP.'popup='.$_REQUEST['popup']);
	exit;
}

if (isset($_POST['submit_yes'])) {
	$dest = $_POST['dest'] .'/';
	$pathext = $_POST['pathext'] .'/';

	if (isset($_POST['listofdirs'])) {

		$_dirs = explode(',',$_POST['listofdirs']);
		$count = count($_dirs);
		
		for ($i = 0; $i < $count; $i++) {
			$source = $_dirs[$i];
			$real_source = realpath($current_path . $pathext . $source);
			$real_dest = realpath($current_path . $dest);

			if (!file_exists($real_source) || (substr($real_source, 0, strlen($current_path)) != $current_path)) {
				// error: File does not exist
				$msg->addError('DIR_NOT_EXIST');
				header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup);
				exit;
			}
			else if (!is_dir($real_dest) || (substr($real_dest, 0, strlen($current_path)) != $current_path)) {
				// error: File does not exist
				$msg->addError('UNKNOWN');
				header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup);
				exit;
			}
			else if (strpos($source, '..') !== false) {
				$msg->addError('UNKNOWN');
				header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup);
				exit;
			}	
			else {
				@rename($current_path.$pathext.$source, $current_path.$dest.$source);
			}
		}
		$msg->addFeedback('DIRS_MOVED');
	}
	if (isset($_POST['listoffiles'])) {

		$_files = explode(',',$_POST['listoffiles']);
		$count = count($_files);

		for ($i = 0; $i < $count; $i++) {
			$source = $_files[$i];
			$real_source = realpath($current_path . $pathext . $source);
			$real_dest = realpath($current_path . $dest);

			if (!file_exists($real_source) || (substr($real_source, 0, strlen($current_path)) != $current_path)) {
				// error: File does not exist
				$msg->addError('FILE_NOT_EXIST');
				header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup);
				exit;
			}
			else if (!is_dir($real_dest) || (substr($real_dest, 0, strlen($current_path)) != $current_path)) {
				// error: File does not exist
				$msg->addError('UNKNOWN');
				header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup);
				exit;
			}
			else if (strpos($source, '..') !== false) {
				$msg->addError('UNKNOWN');
				header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup);
				exit;
			}	
			else {
				@rename($current_path.$pathext.$source, $current_path.$dest.$source);
			}
		}
		$msg->addFeedback('MOVED_FILES');
	}
	header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup']);
	exit;
}

if (isset($_POST['dir_chosen'])) {
	$hidden_vars['framed']  = $_POST['framed'];
	$hidden_vars['popup']   = $_POST['popup'];
	$hidden_vars['pathext'] = $_POST['pathext'];
	$hidden_vars['dest']    = $_POST['dir_name'];

	if (isset($_POST['files'])) {
		$list_of_files = implode(',', $_POST['files']);
		$hidden_vars['listoffiles'] = $list_of_files;
		$msg->addConfirm(array('FILE_MOVE', $list_of_files, $_POST['dir_name']), $hidden_vars);
	}
	if (isset($_POST['dirs'])) {
		$list_of_dirs = implode(',', $_POST['dirs']);
		$hidden_vars['listoffiles'] = $list_of_dirs;
		$msg->addConfirm(array('DIR_MOVE', $list_of_dirs, $_POST['dir_name']), $hidden_vars);
	}
	require($_header_file);
	if ($framed == TRUE) {
		echo '<h3>'._AT('file_manager').'</h3>';
	}
	else {
		if ($popup == TRUE) {
			echo '<div align="right"><a href="javascript:window.close()">' . _AT('close_file_manager') . '</a></div>';
		}
		
		echo '<h2>';
		
		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
			echo '<img src="images/icons/default/square-large-tools.gif" border="0" vspace="2"  class="menuimageh2" width="42" height="40" alt="" />';
		}

		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
			if ($popup == TRUE)
				echo ' '._AT('tools')."\n";
			else 
				echo ' <a href="tools/" class="hide" >'._AT('tools').'</a>'."\n";
		}

		echo '</h2>'."\n";

		echo '<h3>';
		
		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {	
			echo '&nbsp;<img src="images/icons/default/file-manager-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
		}
		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
			echo '<a href="tools/filemanager/index.php?popup=' . $popup . SEP . 'framed=' . $framed .'">' . _AT('file_manager') . '</a>' . "\n";		}
		echo '</h3>'."\n";
	}
	$msg->printConfirm();
	require($_footer_file);
} 
else {
	require($_header_file);
	if ($framed == TRUE) {
		echo '<h3>'._AT('file_manager').'</h3>';
	}
	else {
		if ($popup == TRUE) {
			echo '<div align="right"><a href="javascript:window.close()">' . _AT('close_file_manager') . '</a></div>';
		}
		
		echo '<h2>';
		
		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
			echo '<img src="images/icons/default/square-large-tools.gif" border="0" vspace="2"  class="menuimageh2" width="42" height="40" alt="" />';
		}

		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
			if ($popup == TRUE)
				echo ' '._AT('tools')."\n";
			else 
				echo ' <a href="tools/" class="hide" >'._AT('tools').'</a>'."\n";
		}

		echo '</h2>'."\n";

		echo '<h3>';
		
		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {	
			echo '&nbsp;<img src="images/icons/default/file-manager-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
		}
		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
			echo '<a href="tools/filemanager/index.php?popup=' . $popup . SEP . 'framed=' . $framed .'">' . _AT('file_manager') . '</a>' . "\n";
		}
		echo '</h3>'."\n";
	}

	$tree = AT_CONTENT_DIR.$_SESSION['course_id'].'/';
	$file    = $_GET['file'];
	$pathext = $_GET['pathext']; 
	$popup   = $_GET['popup'];
	$framed  = $_GET['framed'];

	/* find the files and directories to be copied */
	$total_list = explode(',', $_GET['list']);

	$count = count($total_list);
	$countd = 0;
	$countf = 0;
	for ($i=0; $i<$count; $i++) {
		if (is_dir($current_path.$pathext.$total_list[$i])) {
			$_dirs[$countd] = $total_list[$i];
			$hidden_dirs  .= '<input type="hidden" name="dirs['.$countd.']"   value="'.$_dirs[$countd].'" />';
			$countd++;
		} else {
			$_files[$countf] = $total_list[$i];
			$hidden_files .= '<input type="hidden" name="files['.$countf.']" value="'.$_files[$countf].'" />';
			$countf++;
		}
	}

	echo '<br />';
	echo '<br />';
	//display directory tree to user
	echo '<form name="move_form" method="post" action="'.$_SERVER['PHP_SELF'].'">';
	echo '<table width=90% align="center" cellspacing="1" border="0" cellpadding="0">';
	echo '<tr><th class="cyan">' . _AT('file_manager_move') . '</th></tr>';
	echo '<tr><td class="row2" height="1"> </td></tr>';
	echo '<tr><td class="row1">' . _AT('select_directory') . '</td></tr>';
	echo '<tr><td class="row2" height="1"> </td></tr>';
	echo '<tr><td class="row1"><strong><small>';
	echo '<ul><li class="folders"><label><input type="radio" name="dir_name" value=""';
	if ($pathext == '') {
		echo ' checked="checked"';
		$here = ' ' . _AT('current_location');

	}
	echo '/>Home ' .$here.'</label>';

	echo display_tree($current_path, '', $pathext);
	echo '</li></ul></small></strong></td></tr>';

	echo '<tr><td class="row2" height="1"> </td></tr>';
	echo '<tr><td class="row2" height="1"> </td></tr>';
	echo '<tr><td class="row1" align = "center">';
	echo '<input type="submit" name="dir_chosen" value="'._AT('move') . ' [alt-s]" class="button" accesskey="s" /> | ';
	echo '<input type="submit" name="cancel"     value="'._AT('cancel') . '" class="button" />';
	echo '</td></tr>';
	echo '<tr><td class="row2" height="1"> </td></tr>';
	echo '<input type="hidden" name="pathext" value="' . $pathext.'" />';
	echo '<input type="hidden" name="framed" value="'.$framed.'" />';
	echo '<input type="hidden" name="popup" value="'.$popup.'" />';
	echo $hidden_dirs;
	echo $hidden_files; 
	echo '</table></form>';

	require($_footer_file);
}
?>