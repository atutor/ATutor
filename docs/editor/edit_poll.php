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

define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_POLLS);

require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

if ($_POST['cancel']) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.$_base_href.'discussions/polls.php');
	exit;
}

if (isset($_GET['poll_id'])) {
	$poll_id = intval($_GET['poll_id']);
} else {
	$poll_id = intval($_POST['poll_id']);
}

if ($_POST['edit_poll']) {
	if (trim($_POST['question']) == '') {
		$msg->addError('POLL_QUESTION_EMPTY');
	}

	if (!$msg->containsErrors()) {
		$_POST['question'] = $addslashes($_POST['question']);

		for ($i=1; $i<= AT_NUM_POLL_CHOICES; $i++) {
			$choices .= "choice$i = '" . $addslashes($_POST['c' . $i]) . "',";
		}
		$choices = substr($choices, 0, -1);

		$sql = "UPDATE ".TABLE_PREFIX."polls SET question='$_POST[question]', $choices WHERE poll_id=$poll_id AND course_id=$_SESSION[course_id]";
		$result = mysql_query($sql,$db);

		$msg->addFeedback('POLL_UPDATED');
		Header('Location: '.$_base_href.'discussions/polls.php');
		exit;
	}
}

$_section[0][0] = _AT('discussions');
$_section[0][1] = 'discussions/index.php';
$_section[1][0] = _AT('polls');
$_section[1][1] = 'discussions/polls.php';
$_section[2][0] = _AT('edit_poll');


require(AT_INCLUDE_PATH.'header.inc.php');

$msg->printErrors();

echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<a href="discussions/" class="hide" ><img src="images/icons/default/square-large-discussions.gif" vspace="2" border="0"  class="menuimageh2" width="42" height="40" alt="" /></a> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo '<a href="discussions/" class="hide" >'._AT('discussions').'</a>';
	}
echo '</h2>';

echo '<h3>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '&nbsp;<img src="images/icons/default/polls-large.gif" class="menuimageh3" width="42" height="38" alt="" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo '<a href="discussions/polls.php" class="hide" >'._AT('polls').'</a>';
	}
echo '</h3>';

?>
<?php

	if ($poll_id == 0) {
		$msg->printErrors('POLL_NOT_FOUND');
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	
	$sql = "SELECT * FROM ".TABLE_PREFIX."polls WHERE poll_id=$poll_id AND course_id=$_SESSION[course_id]";
	$result = mysql_query($sql,$db);
	if (!($row = mysql_fetch_assoc($result))) {
		$msg->printErrors('POLL_NOT_FOUND');
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="edit_poll" value="true" />
<input type="hidden" name="poll_id" value="<?php echo $row['poll_id']; ?>" />
<br />
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
<tr>
	<th colspan="2" class="cyan"><img src="images/pen2.gif" border="0" class="menuimage12" alt="<?php echo _AT('editor_on'); ?>" title="<?php echo _AT('editor_on'); ?>" height="14" width="16" /><?php echo _AT('edit_poll'); ?></th>
</tr>
<tr>
	<td align="right" class="row1"><b><?php echo _AT('question'); ?>:</b></td>
	<td class="row1"><textarea name="question" cols="55" rows="3" id="question" class="formfield"><?php echo $row['question']; ?></textarea></td>
</tr>

<?php for ($i=1; $i<= AT_NUM_POLL_CHOICES; $i++): ?>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right"><b><label for="c<?php echo $i; ?>"><?php echo _AT('choice'); ?> <?php echo $i; ?>:</label></b></td>
		<td class="row1"><input type="text" name="c<?php echo $i; ?>" id="c<?php echo $i; ?>" value="<?php echo htmlspecialchars(stripslashes($row['choice' . $i])); ?>" class="formfield" size="40" /></td>
	</tr>
<?php endfor; ?>

<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" colspan="2" align="center"><br /><a name="jumpcodes"></a><input type="submit" name="submit" value="<?php echo _AT('edit_poll'); ?>[Alt-s]" accesskey="s" class="button" /> - <input type="submit" name="cancel" class="button" value="<?php echo _AT('cancel'); ?> " /></td>
</tr>
</table>
</form>
<?php
	require (AT_INCLUDE_PATH.'footer.inc.php');
?>