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

define('AT_INCLUDE_PATH', '../include/');
	require(AT_INCLUDE_PATH.'vitals.inc.php');
	//authenticate(USER_CLIENT, USER_ADMIN);
require(AT_INCLUDE_PATH.'lib/chat.inc.php');
	$myPrefs = getPrefs($_SESSION['username']);

	if ($_POST['submit'] || $_POST['submit_r'] || $_POST['submit_p']) {
		getAndWriteFormPrefs($myPrefs);

		if ($_POST['submit_p']) {
			$location = './prefs.php?firstLoginFlag='.$addslashes($_POST['firstLoginFlag']);
		} else if ($_POST['submit_r']) {
			$location = './chat.php?firstLoginFlag='.$addslashes($_POST['firstLoginFlag']);
		}

		Header('Location: '.$location);
		exit;
	}
	writePrefs($myPrefs, $_SESSION['username']);

require('include/html/chat_header.inc.php');
?>
<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<th align="left"><h4><?php echo _AC('chat_layout_prefs'); ?></h4></th>
</tr>
</table>
<br />
<form action="prefs2.php" name="f1" method="post" target="_top">
	<input type="hidden" name="firstLoginFlag" value="<?php echo $_REQUEST['firstLoginFlag']; ?>" />

<?php
    if ($myPrefs['newestFirstFlag'] > 0) {
       $nFFSelT = 'selected';
    }
?>

<p><b><?php echo _AC('chat_order_prefs'); ?></b>
	<select name="newestFirstFlag">
		<option value="0"><?php echo _AC('chat_oldnew_prefs'); ?></option>
		<option value="1" <?php echo $nFFSelT;?>><?php echo _AC('chat_newold_prefs'); ?></option>
	</select></p>
<p><?php echo _AC('chat_message_order_help') ;?></p>
<!-- p style="margin-left: 40;">The <?php echo $admin['chatName'];?> allows you to set the order of the message list to either "Old to New" or "New to Old".</p>

<p style="margin-left: 40;"><i>We suggest that people using screen readers set the order of messages to "New to Old" so that after each message check the focus will be on the most recent message.</i></p>

<p style="margin-left: 40;"><i>We suggest that people using screen magnifiers set the order of messages to "Old to New" so that sfter each message check the most recent message will be close to the Compose Message area, minimizing the necessary scrolling.</i></p -->

<?php
    if ($myPrefs['onlyNewFlag'] > 0) {
       $oNFSelT = 'selected';
    }
?>

<p><b><?php echo _AC('chat_newmsg_prefs'); ?></b>
	<select name="onlyNewFlag">
		<option value="0"><?php echo _AC('chat_newmsg_no'); ?></option>
        <option value="1" <?php echo $oNFSelT;?>><?php echo _AC('chat_newmsg_yes'); ?></option>
	</select></p>
<p><?php echo _AC('chat_message_new_help') ;?></p>
<!--p style="margin-left: 40;">To minimize the number of messages displayed, set the Show Only New Messages option to "Yes". This will cause the chat to display only messages that have arrived since your last check.</p>

<p style="margin-left: 40;"><b>Important:</b> This feature works best when Message Checking is set to manual. Using it in conjunction with "Automatic" message checking may cause messages to be removed before you have a chance to read them.</p>

<p style="margin-left: 40;"><i>We suggest this feature be used by users who prefer as few messages displayed as  possible.</i></p -->

<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<td align="left"><input type="submit" value="<?php echo _AC('chat_previous'); ?>" name="submit_p" class="submit" onFocus="this.className='submit highlight'" onBlur="this.className='submit'" /> <input type="submit" value="<?php echo _AC('chat_enter'); ?>" name="submit_r" class="submit" onFocus="this.className='submit highlight'" onBlur="this.className='submit'" /></td>
</tr>
</table>

</form>
<?php
	require('include/html/chat_footer.inc.php');
?>
