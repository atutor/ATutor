<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

if($_POST['m']){
	$m =str_replace('.', '', $_POST['m']);
}else if ($_GET['m']){
	$m =str_replace('.', '', $_GET['m']);
}

if ($_POST['cancel']) {
	$msg->addFeedback('CANCELLED');
	Header('Location: index.php');
	exit;
}

$_section[0][0] = _AC('chat');
$_section[0][1] = 'chat/';
$_section[1][0] = _AT('chat_delete_transcript');

if ($_POST['submit']) {
	unlink(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/tran/'.$m.'.html');
	$msg->addFeedback('TRAN_DELETED');
	Header('Location: index.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');
echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/square-large-discussions.gif" width="42" height="38" border="0" alt="" class="menuimage" /> ';
}

echo '<a href="discussions/">'._AT('discussions').'</a>';
echo '</h2>';
echo'<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/chat-small.gif" width="28" height="25" border="0" alt="" class="menuimage" />';
}
echo _AT('chat');
echo '</h3>';

	if (!file_exists(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/tran/'.$m.'.html')) {
		$msg->printErrors($errors);
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}


?>
<br />
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<input type="hidden" name="m" value="<?php echo $m; ?>">
	<table border="0" cellspacing="0" cellpadding="2" align="center" class="box2">
	<tr>
		<th colspan="3" class="box"><h3><? echo _AT('chat_delete_transcript'); ?></h3></th>
	</tr>
	<tr bgcolor="white">
		<td>&nbsp;</td>
		<td><b><? echo _AT('name'); ?>:</b> <?php echo $m; ?></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td class="row1">&nbsp;</td>
		<td class="row1"><img src="images/clr.gif" /><br /><? echo _AT('chat_delete_transcript_confirm'); ?><br /></td>
		<td class="row1">&nbsp;</td>
	</tr>
	<tr bgcolor="white">
		<td class="row1">&nbsp;</td>
		<td class="row1" align="right"><br /><input type="submit" name="submit" value="<?php echo _AT('chat_delete'); ?>" class="button" /> &nbsp; <input type="submit" name="cancel" value="<? echo _AT('cancel'); ?>" class="button" /><br /><br /></td>
		<td class="row1">&nbsp;</td>
	</tr>
	</table>
</form>
<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>
