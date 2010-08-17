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
require (AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/tinymce.inc.php');

authenticate(AT_PRIV_ANNOUNCEMENTS);
/*
if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
	$content_base_href = 'get.php/';
} else {
	$content_base_href = 'content/' . $_SESSION['course_id'] . '/';
} */

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'mods/_standard/announcements/index.php');
	exit;
} 

if ((!$_POST['setvisual'] && $_POST['settext']) || !$_GET['setvisual']){
	$onload = 'document.form.title.focus();';
}

if (isset($_POST['add_news'])&& isset($_POST['submit'])) {
	$_POST['formatting'] = intval($_POST['formatting']);
	$_POST['title'] = trim($_POST['title']);
	$_POST['body_text'] = trim($_POST['body_text']);
	
	$missing_fields = array();

	if (!$_POST['body_text']) {
		$missing_fields[] = _AT('body');
	}

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	if (!$msg->containsErrors() && (!isset($_POST['setvisual']) || isset($_POST['submit']))) {

		$_POST['formatting']  = intval($_POST['formatting']);
		$_POST['title']  = $addslashes($_POST['title']);
		$_POST['body_text']  = $addslashes($_POST['body_text']);

		//The following checks if title length exceed 100, defined by DB structure
		$_POST['title'] = validate_length($_POST['title'], 100);

		$sql	= "INSERT INTO ".TABLE_PREFIX."news VALUES (NULL, $_SESSION[course_id], $_SESSION[member_id], NOW(), $_POST[formatting], '$_POST[title]', '$_POST[body_text]')";
		mysql_query($sql, $db);
	
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

		/* update announcement RSS: */
		if (file_exists(AT_CONTENT_DIR . 'feeds/' . $_SESSION['course_id'] . '/RSS1.0.xml')) {
			@unlink(AT_CONTENT_DIR . 'feeds/' . $_SESSION['course_id'] . '/RSS1.0.xml');
		}
		if (file_exists(AT_CONTENT_DIR . 'feeds/' . $_SESSION['course_id'] . '/RSS2.0.xml')) {
			@unlink(AT_CONTENT_DIR . 'feeds/' . $_SESSION['course_id'] . '/RSS2.0.xml');
		}

		header('Location: '.AT_BASE_HREF.'mods/_standard/announcements/index.php');
		exit;
	}
}

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

require(AT_INCLUDE_PATH.'header.inc.php');

if (($_POST['setvisual'] && !$_POST['settext']) || $_GET['setvisual']) {
	load_editor();
}
$msg->printErrors();

?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
	<input type="hidden" name="add_news" value="true" />
	<input type="submit" name="submit" style="display:none;"/>
	<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('add_announcement'); ?></legend>
		<div class="row">
			<label for="title"><?php echo _AT('title'); ?></label><br />
			<input type="text" name="title" size="40" id="title" value="<?php echo $_POST['title']; ?>" />
		</div>

		<div class="row">
			<?php echo _AT('formatting'); ?><br />
			<input type="radio" name="formatting" value="0" id="text" <?php if ($_POST['formatting'] == 0) { echo 'checked="checked"'; } ?> onclick="javascript: document.form.setvisual.disabled=true;" <?php if ($_POST['setvisual'] && !$_POST['settext']) { echo 'disabled="disabled"'; } ?> />

			<label for="text"><?php echo _AT('plain_text'); ?></label>
			<input type="radio" name="formatting" value="1" id="html" <?php if ($_POST['formatting'] == 1 || $_POST['setvisual']) { echo 'checked="checked"'; } ?> onclick="javascript: document.form.setvisual.disabled=false;"/>

			<label for="html"><?php echo _AT('html'); ?></label>
			<?php   //Button for enabling/disabling visual editor
				if (($_POST['setvisual'] && !$_POST['settext']) || $_GET['setvisual']){
					echo '<input type="hidden" name="setvisual" value="'.$_POST['setvisual'].'" />';
					echo '<input type="submit" name="settext" value="'._AT('switch_text').'" class="button"/>';
				} else {
					echo '<input type="submit" name="setvisual" value="'._AT('switch_visual').'"  ';
					if ($_POST['formatting']==0) { echo 'disabled="disabled"'; }
					echo ' class="button" />';
				}
			?>
		</div>

		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="body_text"><?php echo _AT('body'); ?></label><br />
			<textarea name="body_text" cols="55" rows="15" id="body_text"><?php echo $_POST['body_text']; ?></textarea>
		</div>
		
		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s"  class="button"/>
			<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?> "  class="button"/>
		</div>
	</fieldset>
	</div>
	</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>