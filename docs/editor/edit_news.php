<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/tinymce.inc.php');

authenticate(AT_PRIV_ANNOUNCEMENTS);

if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
	$content_base_href = 'get.php/';
} else {
	$content_base_href = 'content/' . $_SESSION['course_id'] . '/';
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'tools/news/index.php');
	exit;
} else if ($_POST['edit_news']) {
	$_POST['title'] = trim($_POST['title']);
	$_POST['body_text']  = trim($_POST['body_text']);
	$_POST['aid']	= intval($_POST['aid']);
	$_POST['formatting']	= intval($_POST['formatting']);

	if (($_POST['title'] == '') && ($_POST['body_text'] == '')) {
		$msg->addErros('ANN_BOTH_EMPTY');
	}

	if (!$msg->containsErrors() && isset($_POST['submit'])) {
		$_POST['title']  = $addslashes($_POST['title']);
		$_POST['body_text']  = $addslashes($_POST['body_text']);
		//Check if the title has exceeded the DB length, 100
		$_POST['title'] = validate_length($_POST['title'], 100);

		$sql = "UPDATE ".TABLE_PREFIX."news SET title='$_POST[title]', body='$_POST[body_text]', formatting=$_POST[formatting], date=date WHERE news_id=$_POST[aid] AND course_id=$_SESSION[course_id]";
		$result = mysql_query($sql,$db);

		/* update announcement RSS: */
		if (file_exists(AT_CONTENT_DIR . 'feeds/' . $_SESSION['course_id'] . '/RSS1.0.xml')) {
			@unlink(AT_CONTENT_DIR . 'feeds/' . $_SESSION['course_id'] . '/RSS1.0.xml');
		}
		if (file_exists(AT_CONTENT_DIR . 'feeds/' . $_SESSION['course_id'] . '/RSS2.0.xml')) {
			@unlink(AT_CONTENT_DIR . 'feeds/' . $_SESSION['course_id'] . '/RSS2.0.xml');
		}

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: '.AT_BASE_HREF.'tools/news/index.php');
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

if ((!$_POST['setvisual'] && $_POST['settext']) || !$_GET['setvisual']){
	$onload = 'document.form.title.focus();';
}

require(AT_INCLUDE_PATH.'header.inc.php');

if (($_POST['setvisual'] && !$_POST['settext']) || $_GET['setvisual']) {
	load_editor();
}

if (isset($_GET['aid'])) {
	$aid = intval($_GET['aid']);
} else {
	$aid = intval($_POST['aid']);
}

if ($aid == 0) {
	$msg->printErrors('ITEM_NOT_FOUND');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$sql = "SELECT * FROM ".TABLE_PREFIX."news WHERE news_id=$aid AND course_id=$_SESSION[course_id]";
$result = mysql_query($sql,$db);
if (!($row = mysql_fetch_array($result))) {
	$msg->printErrors('ITEM_NOT_FOUND');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
$_POST['formatting'] = intval($row['formatting']);

?>


<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="edit_news" value="true">
<input type="hidden" name="aid" value="<?php echo $row['news_id']; ?>">

<div class="input-form">
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="title"><?php echo _AT('title'); ?></label><br />
		<input type="text" name="title" id="title" value="<?php echo htmlspecialchars(stripslashes($row['title'])); ?>" size="40">
	</div>

	<div class="row">
		<?php echo _AT('formatting'); ?><br />
		<input type="radio" name="formatting" value="0" id="text" <?php if ($_POST['formatting'] === 0) { echo 'checked="checked"'; } ?> onclick="javascript: document.form.setvisual.disabled=true;" <?php if ($_POST['setvisual'] && !$_POST['settext']) { echo 'disabled="disabled"'; } ?> /><label for="text"><?php echo _AT('plain_text'); ?></label>,

		<input type="radio" name="formatting" value="1" id="html" <?php if ($_POST['formatting'] == 1 || $_POST['setvisual']) { echo 'checked="checked"'; } ?> onclick="javascript: document.form.setvisual.disabled=false;"  /> <label for="html"><?php echo _AT('html'); ?></label>
		<?php
			if (($_POST['setvisual'] && !$_POST['settext']) || $_GET['setvisual']){
				echo '<input type="hidden" name="setvisual" value="'.$_POST['setvisual'].'" />';
				echo '<input type="submit" name="settext"   value="'._AT('switch_text').'" />';
			} else {
				echo '<input type="submit" name="setvisual" value="'._AT('switch_visual').'" ';
				if ($_POST['formatting']==0) { echo 'disabled="disabled"'; }
				echo '/>';
			} 
		?>
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="body_text"><?php echo _AT('body'); ?></label><br />
		<textarea name="body_text" cols="55" rows="15" id="body_text" wrap="wrap"><?php echo $row['body']; ?></textarea>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?> " />
	</div>


</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>