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
	define('AT_INCLUDE_PATH', '../include/');
	require (AT_INCLUDE_PATH.'vitals.inc.php');

//$course_base_href = 'get.php/';
$content_base_href = 'get.php/';

require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

authenticate(AT_PRIV_ANNOUNCEMENTS);

	if (isset($_POST['cancel'])) {
		$msg->addFeedback('CANCELLED');
		header('Location: ../index.php');
		exit;
	}

	//used for visual editor
	if (($_POST['setvisual'] && !$_POST['settext']) || $_GET['setvisual']){
		$onload = 'onload="initEditor();"';
	}else {
		$onload = ' onload="document.form.title.focus();"';
	}


	if (isset($_POST['add_news'])&& isset($_POST['submit'])) {
		$_POST['formatting'] = intval($_POST['formatting']);
		
		if (($_POST['title'] == '') && ($_POST['body_text'] == '') && !isset($_POST['setvisual'])) {
			$msg->addError('ANN_BOTH_EMPTY');
		}
		if (!$msg->containsErrors() && (!isset($_POST['setvisual']) || isset($_POST['submit']))) {

			$_POST['formatting']  = $addslashes($_POST['formatting']);
			$_POST['title']  = $addslashes($_POST['title']);
			$_POST['body_text']  = $addslashes($_POST['body_text']);

			$sql	= "INSERT INTO ".TABLE_PREFIX."news VALUES (0, $_SESSION[course_id], $_SESSION[member_id], NOW(), $_POST[formatting], '$_POST[title]', '$_POST[body_text]')";
			
			mysql_query($sql, $db);
			if(file_exists(AT_CONTENT_DIR."feeds/".$_SESSION[course_id]."/announce_feed.RSS2.0.xml")||
				file_exists(AT_CONTENT_DIR."feeds/".$_SESSION[course_id]."/announce_feed.RSS1.0.xml")){
				require_once('../tools/feeds/announce_feed.php');
			}
			
			$msg->addFeedback('NEWS_ADDED');
		
			header('Location: ../index.php');
			exit;
		}
	}

	$_section[0][0] = _AT('add_announcement');

	require(AT_INCLUDE_PATH.'header.inc.php');
	require(AT_INCLUDE_PATH.'html/editor_tabs/news.inc.php');
	$msg->printErrors();

?>
<h2><?php echo _AT('add_announcement'); ?></h2>


	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
		<input type="hidden" name="add_news" value="true" />
		<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
		<tr>
			<th colspan="2" class="cyan"><img src="<?php echo $_base_href; ?>images/pen2.gif" border="0" class="menuimage12" alt="<?php echo _AT('editor_on'); ?>" title="<?php echo _AT('editor_on'); ?>" height="14" width="16" /><?php echo _AT('add_announcement'); ?></th>
		</tr>
		<tr>
			<td class="row1" align="right"><?php print_popup_help('ANNOUNCEMENT'); ?><b><label for="title"><?php echo _AT('title'); ?>:</label></b></td>
			<td class="row1"><input type="text" name="title" class="formfield" size="40" id="title" /></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td align="right" class="row1">
			<?php print_popup_help('FORMATTING'); ?>
			<b><?php echo _AT('formatting'); ?>:</b></td>
			<td class="row1">
			<input type="radio" name="formatting" value="0" id="text" <?php if ($_POST['formatting'] == 0) { echo 'checked="checked"'; } ?> onclick="javascript: document.form.setvisual.disabled=true;" <?php if ($_POST['setvisual'] && !$_POST['settext']) { echo 'disabled="disabled"'; } ?> />
			<label for="text"><?php echo _AT('plain_text'); ?></label>
			<input type="radio" name="formatting" value="1" id="html" <?php if ($_POST['formatting'] == 1 || $_POST['setvisual']) { echo 'checked="checked"'; } ?> onclick="javascript: document.form.setvisual.disabled=false;"/>
			<label for="html"><?php echo _AT('html'); ?></label>
			<?php   //Button for enabling/disabling visual editor
			if (($_POST['setvisual'] && !$_POST['settext']) || $_GET['setvisual']){
				echo '<input type="hidden" name="setvisual" value="'.$_POST['setvisual'].'" />';
				echo '<input type="submit" name="settext" value="'._AT('switch_text').'" class="button" />';
			} else {
				echo '<input type="submit" name="setvisual" value="'._AT('switch_visual').'" class="button" ';
				if ($_POST['formatting']==0) { echo 'disabled="disabled"'; }
				echo '/>';
			}
			?></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td class="row1" valign="top" align="right">
		
			<b><label for="body_text"><?php echo _AT('body'); ?>:</label></b></td>
			<td class="row1"><textarea name="body_text" cols="55" rows="15" class="formfield" id="body_text"></textarea></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td class="row1" colspan="2" align="center"><br /><a name="jumpcodes"></a><input type="submit" name="submit" value="<?php echo _AT('add_announcement'); ?> Alt-s" class="button" accesskey="s" /> - <input type="submit" name="cancel" class="button" value="<?php echo _AT('cancel'); ?> " /></td>
		</tr>
		</table>
	</form>

<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>
