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
require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

authenticate(AT_PRIV_POLLS);

if ($_POST['cancel']) {
	$msg->addFeedback('CANCELLED');
	Header('Location: '.$_base_href.'discussions/polls.php');
	exit;
}

if ($_POST['add_poll'] && (authenticate(AT_PRIV_POLLS, AT_PRIV_RETURN))) {
	if (trim($_POST['question']) == '') {
		$msg->addError('POLL_QUESTION_EMPTY');
	}

	if (!$msg->containsErrors()) {
		$_POST['question'] = $addslashes($_POST['question']);

		for ($i=1; $i<= AT_NUM_POLL_CHOICES; $i++) {
			$choices .= "'" . $addslashes($_POST['c' . $i]) . "',0,";
		}
		$choices = substr($choices, 0, -1);

		$sql	= "INSERT INTO ".TABLE_PREFIX."polls VALUES (0, $_SESSION[course_id], '$_POST[question]', NOW(), 0, $choices)";
		$result = mysql_query($sql,$db);
		
		$msg->addFeedback('POLL_ADDED');
		header('Location: '.$_base_href.'discussions/polls.php');
		exit;
	}
}

$_section[0][0] = _AT('discussions');
$_section[0][1] = 'discussions/index.php';
$_section[1][0] = _AT('polls');
$_section[1][1] = 'discussions/polls.php';
$_section[2][0] = _AT('add_poll');

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
		echo '&nbsp;<img src="images/icons/default/polls-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo '<a href="discussions/polls.php" class="hide" >'._AT('polls').'</a>';
	}
echo '</h3>';
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="add_poll" value="true" />
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
<tr>
	<th colspan="2" class="cyan"><?php  echo _AT('add_poll'); ?></th>
</tr>
<tr>
	<td class="row1" align="right"><b><label for="question"><?php  echo _AT('question'); ?>:</label></b></td>
	<td class="row1"><textarea name="question" cols="45" rows="3" class="formfield" id="question"></textarea></td>
</tr>

<?php for ($i=1; $i<= AT_NUM_POLL_CHOICES; $i++): ?>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right"><b><label for="c<?php echo $i; ?>"><?php echo _AT('choice'); ?> <?php echo $i; ?>:</label></b></td>
		<td class="row1"><input type="text" name="c<?php echo $i; ?>" class="formfield" size="40" id="c<?php echo $i; ?>" /></td>
	</tr>
<?php endfor; ?>

<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" colspan="2" align="center"><br /><input type="submit" name="submit" value="<?php  echo _AT('add_poll'); ?> [Alt-s]" class="button" accesskey="s" /> - <input type="submit" name="cancel" value="<?php  echo _AT('cancel'); ?>" class="button" /></td>
</tr>
</table>
</form>

<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>