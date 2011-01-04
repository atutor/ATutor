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
// $Id: new.php 10142 2010-08-17 19:17:26Z hwong $

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require_once(AT_INCLUDE_PATH.'../mods/_core/file_manager/filemanager.inc.php');

if (!authenticate(AT_PRIV_FILES,AT_PRIV_RETURN)) {
	authenticate(AT_PRIV_CONTENT);
}


$current_path = AT_CONTENT_DIR.$_SESSION['course_id'].'/';

$popup  = $_REQUEST['popup'];
$framed = $_REQUEST['framed'];


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
			$head_html = "<html>\n<head>\n<title>".$_POST['filename']."</title>\n<head>\n<body>";
			$foot_html ="\n</body>\n</html>";
		} else {
			$extension = 'txt';
		}

		if (!@file_exists($current_path.$pathext.$filename.'.'.$extension)) {
			$content = str_replace("\r\n", "\n", $head_html.$_POST['body_text'].$foot_html);
			
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
			require(AT_INCLUDE_PATH.'header.inc.php');
			$pathext = $_POST['pathext']; 
			$popup   = $_POST['popup'];

			$_POST['newfile'] = "new";

			$hidden_vars['pathext']   = $pathext;
			$hidden_vars['filename']  = $filename;
			$hidden_vars['extension'] = $extension;
			$hidden_vars['body_text'] = $_POST['body_text'];

			$hidden_vars['popup']  = $popup;
			$hidden_vars['framed'] = $framed;

			$msg->addConfirm(array('FILE_EXISTS', $filename.'.'.$extension), $hidden_vars);
			$msg->printConfirm();

			require(AT_INCLUDE_PATH.'footer.inc.php');
			exit;
		}
	} else {
		$msg->addError(array('EMPTY_FIELDS', _AT('file_name')));
	}
}

$onload="on_load()";

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

// load tinymce library
load_editor(false, false, "none");

$pathext = $_GET['pathext']; 
$popup   = $_GET['popup'];

$msg->printAll();
if (!$_POST['extension']) {
	$_POST['extension'] = 'txt';
	$_POST['formatting'] = 0;
}else if($_POST['extension'] == "html"){
	$_POST['formatting'] = 1;
}

?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
	<input type="hidden" name="pathext" value="<?php echo $_REQUEST['pathext'] ?>" />
	<input type="hidden" name="popup" value="<?php echo $popup; ?>" />

	<div class="input-form">	
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('create_new_file'); ?></legend>
		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="ctitle"><?php echo _AT('file_name');  ?></label><br />
			<input type="text" name="filename" id="ctitle" size="40" <?php if (isset($_POST['filename'])) echo 'value="'.$_POST['filename'].'"'?> />
		</div>

		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><?php echo _AT('type'); ?><br />
			<input type="radio" name="extension" value="txt" id="text" <?php if ($_POST['formatting'] == 0) { echo 'checked="checked"'; } ?> onclick="javascript: document.form.setvisualbutton.disabled=true;" <?php if ($_POST['setvisual'] && !$_POST['settext']) { echo 'disabled="disabled"'; } ?> />
			<label for="text"><?php echo _AT('plain_text'); ?></label>
	
			, <input type="radio" name="extension" value="html" id="html" <?php if ($_POST['formatting'] ==1 || $_POST['setvisual']) { echo 'checked="checked"'; } ?> onclick="javascript: document.form.setvisualbutton.disabled=false;"/>
			<label for="html"><?php echo _AT('html'); ?></label>
	
			<input type="hidden" name="setvisual" value="<?php echo $_POST['setvisual']; ?>" />
			<input type="hidden" name="settext" value="<?php echo $_POST['settext']; ?>" />
			<input type="button" name="setvisualbutton" value="<?php echo _AT('switch_visual'); ?>" onClick="switch_body_editor()" />
		</div>
	
		<div class="row">
			<label for="body_text"><?php echo _AT('body');  ?></label><br />
			<textarea name="body_text" id="body_text" rows="25"><?php echo ContentManager::cleanOutput($_POST['body_text']); ?></textarea>
		</div>
	
		<div class="row buttons">
			<input type="submit" name="savenewfile" value="<?php echo _AT('save'); ?>" accesskey="s" />
			<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"  />		
		</div>
	</fieldset>
	</div>
	</form>

	<script type="text/javascript" language="javascript">
	//<!--
	function on_load()
	{
		if (document.getElementById("text").checked)
			document.form.setvisualbutton.disabled = true;
			
		if (document.form.setvisual.value==1)
		{
			tinyMCE.execCommand('mceAddControl', false, 'body_text');
			document.form.extension[0].disabled = "disabled";
			document.form.setvisualbutton.value = "<?php echo _AT('switch_text'); ?>";
		}
		else
		{
			document.form.setvisualbutton.value = "<?php echo _AT('switch_visual'); ?>";
		}
	}
	
	// switch between text, visual editor for "body text"
	function switch_body_editor()
	{
		if (document.form.setvisualbutton.value=="<?php echo _AT('switch_visual'); ?>")
		{
			tinyMCE.execCommand('mceAddControl', false, 'body_text');
			document.form.setvisual.value=1;
			document.form.settext.value=0;
			document.form.extension[0].disabled = "disabled";
			document.form.setvisualbutton.value = "<?php echo _AT('switch_text'); ?>";
		}
		else
		{
			tinyMCE.execCommand('mceRemoveControl', false, 'body_text');
			document.form.setvisual.value=0;
			document.form.settext.value=1;
			document.form.extension[0].disabled = "";
			document.form.setvisualbutton.value = "<?php echo _AT('switch_visual'); ?>";
		}
	}
	//-->
	</script>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>