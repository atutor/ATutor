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

$page = 'file_manager_edit';

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
//session_write_close();
$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('file_manager');
$_section[1][1] = 'tools/filemanager/index.php';
$_section[2][0] = _AT('file_manager_edit_file');
$_section[2][1] = 'tools/filemanager/file_manager_edit.php';

authenticate(AT_PRIV_FILES);

$_header_file = 'header.inc.php';
$_footer_file = 'footer.inc.php';

$current_path = AT_CONTENT_DIR . $_SESSION['course_id'].'/';
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

if (isset($_POST['save'])) {

		$content = str_replace("\r\n", "\n", $_POST['body_text']);
		$file = $_POST['file'];
		if (($f = @fopen($current_path.$pathext.$file, 'w')) && @fwrite($f, $content) !== false && @fclose($f)) {
			$msg->addFeedback('FILE_SAVED');
			
		} else {
			$msg->addError('FILE_NOT_SAVED');
		}
		header('Location: index.php?pathext='.urlencode($_POST['pathext']));
		exit;
}
if (isset($_POST['editfile'])) {
	if (!is_array($_POST['check'])) {
		// error: you must select a file/dir 
		$msg->addError('NO_FILE_SELECT');
		header('Location: index.php?pathext='.urlencode($_POST['pathext']));
		exit;
	} else {
		$file = $_POST['check'];
				
		$count = count($file);
		if ($count > 1) {
			// error: select only one file
			$msg->addError('SELECT_ONE_FILE');
			header('Location: index.php?pathext='.urlencode($_POST['pathext']));
		exit;
		}
		$file = $file[0];
		$filedata = stat($current_path.$pathext.$file);
		$path_parts = pathinfo($current_path.$pathext.$file);
		$ext = $path_parts['extension'];
		
		// open file to edit 
		if (is_dir($current_path.$pathext.$file)) {
			// error: cannot edit folder
			$msg->addError('BAD_FILE_TYPE');
			header('Location: index.php?pathext='.urlencode($_POST['pathext']));
		exit;
		} else if ($ext == 'txt') {
			$_POST['body_text'] = file_get_contents($current_path.$pathext.$file);
		} else if (in_array($ext, array('html', 'htm'))){
			$_POST['body_text'] = file_get_contents($current_path.$pathext.$file);

			$_POST['body_text'] = get_html_body($_POST['body_text']); 
		} else {
			//error: bad file type
			$msg->addError('BAD_FILE_TYPE');
			header('Location: index.php?pathext='.urlencode($_POST['pathext']));
		exit;
		}
	}
} 
$start_at = 3;



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
	echo '&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/icons/default/file-manager-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo _AT('file_manager_edit_file');
}
echo '</h4>'."\n";
echo '<br />';

/* listing of path to current directory */
echo '<p>'._AT('file_name').': ';
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




$msg->printAll();


echo "\n\n".'<p align="center"><strong>'.$file."</strong></p>\n\n";
?>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form" >
<?php	
		echo '<input type="hidden" name="pathext" value="'.$pathext.'" />'."\n";
		echo '<input type="hidden" name="file" value="'.$file.'" />'."\n";
?>
		<table cellspacing="1" cellpadding="0" width="98%" border="0" class="bodyline" summary="">
		<tr>
			<td colspan="2" valign="top" align="left" class="row1">
			<table cellspacing="0" cellpadding="0" width="100%" border="0" summary="">
			<tr><td class="row1">	
			<textarea  name="body_text" id="body_text" rows="25" class="formfield" style="width: 100%;"><?php echo ContentManager::cleanOutput($_POST['body_text']); ?></textarea>
			</td></tr></table>
			</td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td colspan="2" valign="top" align="center" class="row1">
				<input type="reset" value="<?php echo _AT('reset'); ?>" class="button" />
				<input type="submit" name="save" value="<?php echo _AT('save'); ?>" class="button" />
				<input type="submit" name="cancel" value="<?php echo _AT('back'); ?>" class="button" />
			</td>
		</tr>

		</table>

	</form>
<?php

require(AT_INCLUDE_PATH.$_footer_file);


?>
