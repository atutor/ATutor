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

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');

authenticate(AT_PRIV_FILES);

$current_path = AT_CONTENT_DIR.$_SESSION['course_id'].'\\';

if (($_GET['popup'] == TRUE) || ($_GET['framed'] == TRUE)) {
	$_header_file = AT_INCLUDE_PATH.'fm_header.php';
	$_footer_file = AT_INCLUDE_PATH.'fm_footer.php';
} else {
	$_header_file = AT_INCLUDE_PATH.'header.inc.php';
	$_footer_file = AT_INCLUDE_PATH.'footer.inc.php';
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup']);
	exit;
}

if (isset($_POST['save'])) {
	$content = str_replace("\r\n", "\n", $_POST['body_text']);
	$file = $_POST['file'];
	if (($f = @fopen($current_path.$pathext.$file, 'w')) && @fwrite($f, $content) !== false && @fclose($f)) {
		$msg->addFeedback('FILE_SAVED');
		header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup']);
		exit;		
	} else {
		$msg->addError('FILE_NOT_SAVED');
		header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup']);
		exit;
	}
}

	$file    = $_GET['file'];
	$pathext = $_GET['pathext']; 
	$popup   = $_GET['popup'];
	$framed  = $_GET['framed'];

	$filedata = stat($current_path.$pathext.$file);
	$path_parts = pathinfo($current_path.$pathext.$file);
	$ext = strtolower($path_parts['extension']);

	// open file to edit
	$real = realpath($current_path . $pathext . $file);

	if (!file_exists($real) || (substr($real, 0, strlen($current_path)) != $current_path)) {
		// error: File does not exist
		$msg->addError('FILE_NOT_EXIST');
		header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup);
		exit;
	} else if (is_dir($current_path.$pathext.$file)) {
		// error: cannot edit folder
		$msg->addError('BAD_FILE_TYPE');
		header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup);
		exit;
	} else if (!file_exists($current_path.$pathext.$file)) {
		// error: File does not exist
		$msg->addError('FILE_NOT_EXIST');
		header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup);
		exit;
	} else if (!is_readable($current_path.$pathext.$file)) {
		// error: File cannot open file
		$msg->addError(array('CANNOT_OPEN_FILE', $file));
		header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup);
		exit;
	} else if (in_array($ext, $editable_file_types)) {
		$_POST['body_text'] = file_get_contents($current_path.$pathext.$file);
	} else {
		//error: bad file type
		$msg->addError('BAD_FILE_TYPE');
		header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup);
		exit;
	}

	require($_header_file);
	if ($framed == TRUE) {
		echo '<h3>'._AT('file_manager').'</h3>';
	} else {
		if ($popup == TRUE) {
			echo '<div align="right"><a href="javascript:window.close()">' . _AT('close_file_manager') . '</a></div>';
		}
		echo '<h2>';
			
		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
			echo '<img src="images/icons/default/square-large-tools.gif" border="0" vspace="2"  class="menuimageh2" width="42" height="40" alt="" />';
		}

		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
			if ($popup == TRUE) {
				echo ' '._AT('tools');
			} else {
				echo ' <a href="tools/" class="hide" >'._AT('tools').'</a>';
			}
		}
		echo '</h2>';
		echo '<h3>';
		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {	
			echo '&nbsp;<img src="images/icons/default/file-manager-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
		}
		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
			echo '<a href="tools/filemanager/index.php?popup=' . $popup . SEP . 'framed=' . $framed .'">' . _AT('file_manager') . '</a>';
		}
		echo '</h3>';
	}
	echo '<p align="center"><strong>'.$file.'</strong></p>';
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
	<input type="hidden" name="pathext" value="<?php echo $pathext; ?>" />
	<input type="hidden" name="framed" value="<?php echo $framed; ?>" />
	<input type="hidden" name="popup" value="<?php echo $popup; ?>" />
	<input type="hidden" name="file" value="<?php echo $file; ?>" />

	<table cellspacing="1" cellpadding="0" width="98%" border="0" class="bodyline" summary="">
	<tr><th class="cyan"><?php echo _AT('file_manager_edit_file'); ?></th></tr>
	<tr>
		<td colspan="2" valign="top" align="left" class="row1">
		<table cellspacing="0" cellpadding="0" width="100%" border="0" summary="">
			<tr>
				<td class="row1"><textarea  name="body_text" id="body_text" rows="25" class="formfield" style="width: 100%;"><?php echo ContentManager::cleanOutput($_POST['body_text']); ?></textarea></td>
			</tr>
		</table></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td colspan="2" valign="top" align="center" class="row1"><input type="submit" name="save" value="<?php echo _AT('save'); ?> [alt-s]" class="button" accesskey="s" />
						<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" class="button" /></td>
	</tr>
	</table>

	</form>
<?php require($_footer_file); ?>