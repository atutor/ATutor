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

$page = 'file_manager_new';

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
$_section[2][0] = _AT('file_manager_new');
$_section[2][1] = 'tools/filemanager/file_manager_new.php';

authenticate(AT_PRIV_FILES);

if ($_GET['frame'] == 1) {
	$_header_file = 'html/frameset/header.inc.php';
	$_footer_file = 'html/frameset/footer.inc.php';
} else {
	$_header_file = 'header.inc.php';
	$_footer_file = 'footer.inc.php';
}

	$current_path = AT_CONTENT_DIR . $_SESSION['course_id'].'/';

if (isset($_POST['return'])) {
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
	echo _AT('file_manager_new_file');
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

require(AT_INCLUDE_PATH.'html/feedback.inc.php');

if (isset($_POST['overwrite'])) {
	$newfile = fopen($current_pat.$pathext.'/'.$_POST['filename'],"w");
	if (fwrite($newfile,$_POST['body_text'])){
		$feedback[] = AT_FEEDBACK_FILE_OVERWRITE;
	} else {
		$errors[] = AT_ERROR_CANNOT_OVERWRITE_FILE;
	}

	fclose($newfile);
	require(AT_INCLUDE_PATH.'html/feedback.inc.php');
	echo '<form name="form1" action="'.$_SERVER['PHP_SELF'].'?frame='.$_GET['frame'].'" method="post">'."\n";
	echo '<input type="submit" name="return" value="Return to File Manager" /></form>';

	require(AT_INCLUDE_PATH.$_footer_file);
	exit();
	
}

if (isset($_POST['save'])) {
	if (!isset($_POST['filename']) || ($_POST['filename'] == "")) {
		$errors[] = AT_ERROR_NEED_FILENAME;
	} else {
		$file = $_POST['filename'];

		if (@file_exists($current_path.$pathext.'/'.$file)) {
			$warnings[]=array(AT_WARNING_FILE_EXISTS, $file);
			print_warnings($warnings);
			
			echo '<form name="form1" action="'.$_SERVER['PHP_SELF'].'?frame='.$_GET['frame'].'" method="post">'."\n";
			echo '<input type="hidden" name="pathext" value="'.$pathext.'" />'."\n";
			echo '<input type="hidden" name="filename" value="'.$file.'" />'."\n";
			echo '<input type="hidden" name="body_text" value="'.$_POST['body_text'].'" />'."\n";
			echo '<input type="submit" name="overwrite" value="'._AT('overwrite').'" /><input type="submit" name="cancel" value="'._AT('cancel').'"/></p>'."\n";
			echo '</form>';
			
		} else {
			$newfile = fopen($current_path.$pathext.'/'.$file,"w");
			if (fwrite($newfile,$_POST['body_text'])){
				$feedback[] = AT_FEEDBACK_FILE_WRITE;
			} else {
				$errors[] = AT_ERROR_CANNOT_WRITE_FILE;
			}
			fclose($newfile);

			require(AT_INCLUDE_PATH.'html/feedback.inc.php');
			echo '<form name="form1" action="'.$_SERVER['PHP_SELF'].'?frame='.$_GET['frame'].'" method="post">'."\n";
			echo '<input type="submit" name="return" value="Return to File Manager" /></form>';

			require(AT_INCLUDE_PATH.$_footer_file);
			exit();
		}
	}
}

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>?cid=<?php echo $cid; ?>" method="post" name="form" enctype="multipart/form-data">
	<input type="hidden" name="pathext" value="<?php echo $pathext ?>" />
	<table cellspacing="1" cellpadding="0" width="98%" border="0" class="bodyline" summary="">
		<tr>
			<td class="row1" colspan="2"><br /><strong><label for="ctitle"><?php echo _AT('filename');  ?>:</label></strong>
			<input type="text" name="filename" size="40" class="formfield" <?php if (isset($_POST['filename'])) echo 'value="'.$_POST['filename'].'"'?> /></td>
		</tr>
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
				<input type="reset" value="reset"  style="margin-left: 50px" />
				<input type="submit" name="save" value="save"  style="margin-left: 50px" />
			</td>
		</tr>

		</table>

	</form>
<?php

echo '<form name="form1" action="'.$_SERVER['PHP_SELF'].'?frame='.$_GET['frame'].'" method="post">'."\n";
echo '<input type="submit" name="return" value="Return to File Manager" /></form>';

require(AT_INCLUDE_PATH.$_footer_file);
?>
