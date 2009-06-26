<?php
/****************************************************************/
/* ATutor                                                       */
/****************************************************************/
/* Copyright (c) 2002-2008										*/
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca                                             */
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: tac_edit.php 8319 2009-03-03 16:38:19Z hwong $
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/tinymce.inc.php');
admin_authenticate(AT_ADMIN_TERMS_AND_CONDITIONS);

//handle save
if (isset($_POST['submit'])){
	$_POST['enable_terms_and_conditions'] = intval($_POST['enable_terms_and_conditions']);
	$_POST['formatting'] = intval($_POST['formatting']);
	$_POST['body_text'] = trim($addslashes($_POST['body_text']));
	$_POST['tac_link'] = (trim($addslashes($_POST['tac_link']))=='')?AT_BASE_HREF.'about.php':trim($addslashes($_POST['tac_link']));
	if ($_POST['body_text']!=''){
		$sql = 'REPLACE INTO '.TABLE_PREFIX."config VALUES ('tac_link', '$_POST[tac_link]')";
		mysql_query($sql, $db);

		$sql = 'REPLACE INTO '.TABLE_PREFIX."config VALUES ('tac_body', '$_POST[body_text]')";
		mysql_query($sql, $db);

		$sql = 'REPLACE INTO '.TABLE_PREFIX."config VALUES ('enable_terms_and_conditions', $_POST[enable_terms_and_conditions])";
		mysql_query($sql, $db);

		$msg->addFeedback('TAC_SAVED');	
	}	
	header('Location: tac_edit.php');
	exit;
}

//get config preferences
$_POST['body_text'] = htmlentities($_config['tac_body']);
$_POST['tac_link'] = htmlentities($_config['tac_link']);

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

//template goes here
include(AT_INCLUDE_PATH.'header.inc.php');
if (($_POST['setvisual'] && !$_POST['settext']) || $_GET['setvisual']) {
	load_editor();
}
?>
<div class="input-form">
<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>" name="form" >
<div>
	<div class="row">
		<?php echo _AT('enable_terms_and_conditions'); ?> <br />
		<input type="radio" name="enable_terms_and_conditions" value="1" id="terms_and_conditions_y" <?php if($_config['enable_terms_and_conditions']) { echo 'checked="checked"'; }?>  /><label for="terms_and_conditions_y"><?php echo _AT('enable'); ?></label> <input type="radio" name="enable_terms_and_conditions" value="0" id="terms_and_conditions_n" <?php if(!$_config['enable_terms_and_conditions']) { echo 'checked="checked"'; }?>  /><label for="terms_and_conditions_n"><?php echo _AT('disable'); ?></label>
	</div>
	<div class="row">
		<label for="tac_link"><?php echo _AT('tac_link'); ?></label><br/>
		<input id="tac_link" type="text" name="tac_link" value="<?php echo $_POST['tac_link']; ?>" size="80"/>
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
				echo '<input type="submit" name="settext" value="'._AT('switch_text').'" />';
			} else {
				echo '<input type="submit" name="setvisual" value="'._AT('switch_visual').'"  ';
				if ($_POST['formatting']==0) { echo 'disabled="disabled"'; }
				echo '/>';
			}
		?>
	</div>

	<div class="row">
		<label for='body_text'><?php echo _AT('terms_and_conditions');?></label><br/>
		<textarea name="body_text" cols="55" rows="15" id="body_text"><?php echo $_POST['body_text']; ?></textarea>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="reset" name="reset" value="<?php echo _AT('reset'); ?> " />
	</div>
</div>
</form>
</div>
<?php include(AT_INCLUDE_PATH.'footer.inc.php'); ?>