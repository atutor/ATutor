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

$current_path = AT_CONTENT_DIR.$_SESSION['course_id'].'/';

if (($_REQUEST['popup'] == TRUE) || ($_REQUEST['framed'] == TRUE)) {
	$_header_file = AT_INCLUDE_PATH.'fm_header.php';
	$_footer_file = AT_INCLUDE_PATH.'fm_footer.php';
} else {
	$_header_file = AT_INCLUDE_PATH.'header.inc.php';
	$_footer_file = AT_INCLUDE_PATH.'footer.inc.php';
}
$popup  = $_REQUEST['popup'];
$framed = $_REQUEST['framed'];


if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
	$content_base_href = 'get.php/';
} else {
	$content_base_href = 'content/' . $_SESSION['course_id'] . '/';
}

if (($_POST['setvisual'] && !$_POST['settext']) || $_GET['setvisual']) {
	$onload = 'initEditor();';
}else {
	$onload = 'document.form.filename.focus();';
}


if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup']);
	exit;
}

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup']);
	exit;
}

if (isset($_POST['submit_yes'])) {
	$filename = preg_replace("{[^a-zA-Z0-9_]}","_", trim($_POST['filename']));
	$pathext  = $_POST['pathext'];

	/* only html or txt extensions allowed */
	if ($_POST['extension'] == 'html') {
		$extension = 'html';
	} else {
		$extension = 'txt';
	}
	
	if (course_realpath($current_path . $pathext . $filename.'.'.$extension) == FALSE) {
		$msg->addError('FILE_NOT_SAVED');
		/* take user to home page to avoid unspecified error warning */
		header('Location: index.php?pathext='.SEP.'framed='.$framed.SEP.'popup='.$popup);
		exit;
	}

	if (($f = @fopen($current_path.$pathext.$filename.'.'.$extension,'w')) && @fwrite($f, stripslashes($_POST['body_text'])) !== FALSE && @fclose($f)){
		$msg->addFeedback('FILE_OVERWRITE');
	} else {
		$msg->addError('CANNOT_OVERWRITE_FILE');
	}
	unset($_POST['newfile']);
	header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup);
	exit;
}

if (isset($_POST['savenewfile'])) {

	if (isset($_POST['filename']) && ($_POST['filename'] != "")) {
		$filename     = preg_replace("{[^a-zA-Z0-9_]}","_", trim($_POST['filename']));
		$pathext      = $_POST['pathext'];
		$current_path = AT_CONTENT_DIR.$_SESSION['course_id'].'/';

		/* only html or txt extensions allowed */
		if ($_POST['extension'] == 'html') {
			$extension = 'html';
		} else {
			$extension = 'txt';
		}

		if (!@file_exists($current_path.$pathext.$filename.'.'.$extension)) {
			$content = str_replace("\r\n", "\n", $_POST['body_text']);
			
			if (course_realpath($current_path . $pathext . $filename.'.'.$extension) == FALSE) {
				$msg->addError('FILE_NOT_SAVED');
				/* take user to home page to avoid unspecified error warning */
				header('Location: index.php?pathext='.SEP.'framed='.$framed.SEP.'popup='.$popup);
				exit;
			}

			if (($f = fopen($current_path.$pathext.$filename.'.'.$extension, 'w')) && (@fwrite($f, stripslashes($content)) !== false)  && (@fclose($f))) {
				$msg->addFeedback(array('FILE_SAVED', $filename.'.'.$extension));
				header('Location: index.php?pathext='.urlencode($_POST['pathext']).SEP.'popup='.$_POST['popup']);
				exit;
			} else {
				$msg->addError('FILE_NOT_SAVED');
				header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup);
				exit;
			}
		}
		else {
			require($_header_file);
			$pathext = $_POST['pathext']; 
			$popup   = $_POST['popup'];

			if ($popup == TRUE) {
				echo '<div align="right"><a href="javascript:window.close()">' . _AT('close_file_manager') . '</a></div>';
			}
			$_POST['newfile'] = "new";

			$hidden_vars['pathext']   = $pathext;
			$hidden_vars['filename']  = $filename;
			$hidden_vars['extension'] = $extension;
			$hidden_vars['body_text'] = $_POST['body_text'];

			$hidden_vars['popup']  = $popup;
			$hidden_vars['framed'] = $framed;

			$msg->addConfirm(array('FILE_EXISTS', $filename.'.'.$extension), $hidden_vars);
			$msg->printConfirm();

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
	
require(AT_INCLUDE_PATH.'html/editor_tabs/file.inc.php');
$msg->printAll();
if (!$_POST['extension']) {
	$_POST['extension'] = 'txt';
}

?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
		<input type="hidden" name="pathext" value="<?php echo $_REQUEST['pathext'] ?>" />
		<input type="hidden" name="popup" value="<?php echo $popup; ?>" />

		<div class="input-form">
			<div class="row">
				<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="ctitle"><?php echo _AT('file_name');  ?></label><br />
				<input type="text" name="filename" id="ctitle" size="40" <?php if (isset($_POST['filename'])) echo 'value="'.$_POST['filename'].'"'?> />
			</div>

			<div class="row">
				<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><?php echo _AT('type'); ?><br />
				<input type="radio" name="extension" value="txt" id="text" <?php if ($_POST['extension'] == 'txt') { echo 'checked="checked"'; } ?> onclick="javascript: document.form.setvisual.disabled=true;" <?php if ($_POST['setvisual'] && !$_POST['settext']) { echo 'disabled="disabled"'; } ?> />
				<label for="text"><?php echo _AT('text'); ?></label>

				<input type="radio" name="extension" value="html" id="html" <?php if ($_POST['extension'] == 'html' || $_POST['setvisual']) { echo 'checked="checked"'; } ?> onclick="javascript: document.form.setvisual.disabled=false;"/>
				<label for="html"><?php echo _AT('html'); ?></label>

				<?php   //Button for enabling/disabling visual editor
					if (($_POST['setvisual'] && !$_POST['settext']) || $_GET['setvisual']){
						echo '<input type="hidden" name="setvisual" value="'.$_POST['setvisual'].'" />';
						echo '<input type="submit" name="settext" value="'._AT('switch_text').'" />';
					} else {
						echo '<input type="submit" name="setvisual" value="'._AT('switch_visual').'"  ';
						if ($_POST['extension']== 'txt') { echo 'disabled="disabled"'; }
						echo ' />';
					}
				?>
			</div>

			<div class="row">
				<label for="body_text"><?php echo _AT('body');  ?></label><br />
				<textarea name="body_text" id="body_text" rows="25"><?php echo ContentManager::cleanOutput($_POST['body_text']); ?></textarea>
			</div>

			<div class="row buttons">
				<input type="submit" name="savenewfile" value="<?php echo _AT('save'); ?>" accesskey="s" />
				<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"  />		
			</div>

			</div>
		</form>

<?php require($_footer_file); ?>