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

$current_path = AT_CONTENT_DIR.$_SESSION['course_id'].'/';

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

if (isset($_POST['overwritenewfile'])) {

	$filename = preg_replace("{[^a-zA-Z0-9_]}","_", trim($_POST['filename']));
	$filename = $filename.'.'.$_POST['extension'];

	if (($f = @fopen($current_path.$pathext.$filename,'w')) && @fwrite($f,$_POST['body_text']) != false && @fclose($f)){
		$msg->addFeedback('FILE_OVERWRITE');
	} else {
		$msg->addError('CANNOT_OVERWRITE_FILE');
	}
		unset($_POST['newfile']);
}

if(isset($_POST['savenewfile'])) {

	if (isset($_POST['filename']) && ($_POST['filename'] != "")) {
		$filename     = preg_replace("{[^a-zA-Z0-9_]}","_", trim($_POST['filename']));
		$filename     = $filename.'.'.$_POST['extension'];
		$pathext      = $_POST['pathext'];
		$current_path = AT_CONTENT_DIR.$_SESSION['course_id'].'/';

		if (!@file_exists($current_path.$pathext.$filename)) {
			$content = str_replace("\r\n", "\n", $_POST['body_text']);

			if (($f = fopen($current_path.$pathext.$filename, 'w')) && (@fwrite($f, $content)!== false)  && (@fclose($f))) {
				$msg->addFeedback(array('FILE_SAVED', $filename));
				header('Location: index.php?pathext='.urlencode($_POST['pathext']).SEP.'popup='.$_POST['popup']);
				exit;
			} else {
				$msg->addError('FILE_NOT_SAVED');
			}
		}
		else if (strpos($pathext, '..') !== false) {
			$msg->addError('UNKNOWN');
			header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup);
			exit;
		}	
		
		else {
			require($_header_file);
			$pathext = $_POST['pathext']; 
			$popup   = $_POST['popup'];

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

			$msg->printWarnings(array('FILE_EXISTS', $filename));
			echo '<form name="form1" action="'.$_SERVER['PHP_SELF'].'" method="post">'."\n";
			echo '<input type="hidden" name="pathext" value="'.$pathext.'" />'."\n";
			echo '<input type="hidden" name="popup" value="'.$_POST['popup'].'" />'."\n";
			echo '<input type="hidden" name="filename" value="'.$filename.'" />'."\n";
			echo '<input type="hidden" name="body_text" value="'.$_POST['body_text'].'" />'."\n";
			echo '<p align="center">';
			echo '<input type="submit" name="overwritenewfile" value="'._AT('overwrite').'" class="button"/> - ';
			echo '<input type="submit" name="cancel" value="'._AT('cancel').'" class="button"/></p>'."\n";
			echo '<p>';
			echo '</form>';
			$_POST['newfile'] = "new";
			require($_footer_file);
			exit;
		}
	} else {
		$msg->addError('NEED_FILENAME');
	}
}

require($_header_file);
$pathext = $_GET['pathext']; 
$popup   = $_GET['popup'];

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

$msg->printWarnings();
$msg->printErrors();
$msg->printFeedbacks();

?>
	<br />
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
		<input type="hidden" name="pathext" value="<?php echo $_GET['pathext'] ?>" />

		<table cellspacing="1" cellpadding="0" width="90%" border="0" class="bodyline" align="center" summary="">
			<tr><th class="cyan"><?php echo _AT('file_manager_new'); ?></th></tr>
			<tr>
				<td class="row1" colspan="2">
					<strong><label for="ctitle"><?php echo _AT('file_name');  ?>:</strong>
					<input type="text" name="filename" size="40" class="formfield" <?php if (isset($_POST['filename'])) echo 'value="'.$_POST['filename'].'"'?> /></label>
					<strong><?php echo _AT('type');  ?>:</strong>
					<label><input type="radio" name="extension" value="html" checked="checked"/><?php echo _AT('html'); ?></label>
					<label><input type="radio" name="extension" value="txt"  /><?php echo _AT('text'); ?></label>
				</td>
			</tr>
			<tr>
				<td colspan="2" valign="top" align="left" class="row1">
				<table cellspacing="0" cellpadding="0" width="100%" border="0" summary="">
				<tr><td class="row1" align="center">	
				<textarea name="body_text" id="body_text" rows="25" class="formfield" style="width: 98%;"><?php echo ContentManager::cleanOutput($_POST['body_text']); ?></textarea>
				</td></tr></table>
				</td>
			</tr>
			<tr><td height="1" class="row2" colspan="2"></td></tr>
			<tr>
				<td colspan="2" valign="top" align="center" class="row1">
					<input type="hidden" name="popup" value="<?php echo $popup; ?>" />
					<input type="submit" name="savenewfile" value="<?php echo _AT('save'); ?> [alt-s]" class="button" accesskey="s" />
					<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" class="button" />
				</td>
			</tr>

			</table>

		</form>
<?php
require($_footer_file);
?>
