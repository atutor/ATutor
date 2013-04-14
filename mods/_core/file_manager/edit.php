<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require_once(AT_INCLUDE_PATH.'../mods/_core/file_manager/filemanager.inc.php');


if (!authenticate(AT_PRIV_FILES,AT_PRIV_RETURN)) {
	authenticate(AT_PRIV_CONTENT);
}

$current_path = AT_CONTENT_DIR.$_SESSION['course_id'].'/';

$popup  = $_REQUEST['popup'];
$framed = $_REQUEST['framed'];
$file    = $_REQUEST['file'];
$pathext = $_REQUEST['pathext']; 

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup']);
	exit;
}

if (isset($_POST['save'])) {
	$content = str_replace("\r\n", "\n", $stripslashes($_POST['body_text']));
	$file = $_POST['file'];

	if (course_realpath($current_path . $pathext . $file) == FALSE) {
		$msg->addError('FILE_NOT_SAVED');
	} else {
		if (($f = @fopen($current_path.$pathext.$file, 'w')) && (@fwrite($f, $content) !== false) && @fclose($f)) {
			$msg->addFeedback(array('FILE_SAVED', $file));
			header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup']);
			exit;
		} else {
			$msg->addError('FILE_NOT_SAVED');
		}
	}
	header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup']);
	exit;
}


$path_parts = pathinfo($current_path.$pathext.$file);
$ext = strtolower($path_parts['extension']);

// open file to edit
$real = realpath($current_path . $pathext . $file);

if (course_realpath($current_path . $pathext . $file) == FALSE) {
	// error: File does not exist
	$msg->addError('FILE_NOT_EXIST');
	header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup);
	exit;
} else if (is_dir($current_path.$pathext.$file)) {
	// error: cannot edit folder
	$msg->addError('BAD_FILE_TYPE');
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

require(AT_INCLUDE_PATH.'header.inc.php');
require(AT_INCLUDE_PATH.'lib/tinymce.inc.php');

if (!isset($_REQUEST['setvisual']) && !isset($_REQUEST['settext'])) {
	if ($_SESSION['prefs']['PREF_CONTENT_EDITOR'] == 1) {
		$_POST['formatting'] = 1;
		$_REQUEST['settext'] = 0;
		$_REQUEST['setvisual'] = 0;

	} else if ($_SESSION['prefs']['PREF_CONTENT_EDITOR'] == 2) {
		$_POST['formatting'] = 1;
		$_POST['settext'] = 0;
		$_POST['setvisual'] = 1;

	} else { // else if == 0
		$_POST['formatting'] = 0;
		$_REQUEST['settext'] = 0;
		$_REQUEST['setvisual'] = 0;
	}
}
if (($_POST['setvisual'] && !$_POST['settext']) || $_GET['setvisual']) {
	load_editor(false, 'body_text');
}


?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="pathext" value="<?php echo $pathext; ?>" />
<input type="hidden" name="framed"  value="<?php echo $framed; ?>" />
<input type="hidden" name="popup"   value="<?php echo $popup; ?>" />
<input type="hidden" name="file"    value="<?php echo $file; ?>" />
<input type="submit" name="submit" style="display:none;"/>
<div class="input-form">
	<div class="row">
		<h3><?php echo $file; ?></h3>
	</div>
		<div class="row">
		<?php
			if (($_POST['setvisual'] && !$_POST['settext']) || $_GET['setvisual']){
				echo '<input type="hidden" name="setvisual" value="'.$_POST['setvisual'].'" />';
				echo '<input type="submit" name="settext" value="'._AT('switch_text').'" />';
			} else {
				echo '<input type="submit" name="setvisual" value="'._AT('switch_visual').'" />';
			}
		?>
	</div>
	<div class="row">
		<label for="body_text"><?php echo _AT('body'); ?></label><br />
		<textarea  name="body_text" id="body_text" rows="25"><?php echo htmlspecialchars($_POST['body_text']); ?></textarea>
	</div>

	<div class="row buttons">
		<input type="submit" name="save" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>