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

	$myPrefs = getPrefs($_SESSION['login']);
	if ($_POST['submit'] || $_POST['submit_r']) {
		getAndWriteFormPrefs($myPrefs);

		if ($_POST['submit']) {
			$location = 'prefs2.php?firstLoginFlag='.$addslashes($_POST['firstLoginFlag']);
		} else {
			$location = 'chat.php?firstLoginFlag='.$addslashes($_POST['firstLoginFlag']);
		}
		
		Header('Location: '.$location);
		exit;
	}
	writePrefs($myPrefs, $_SESSION['login']);


require('include/html/chat_header.inc.php');
?>
<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<th align="left"><h4><?php echo _AC('chat_prefs_checking'); ?></h4></th>
</tr>
</table>
<br />
<form action="prefs.php" name="f1" method="post" target="_top">
	<input type="hidden" name="firstLoginFlag" value="<?php echo $_GET['firstLoginFlag']; ?>" />
<?php
    if ($myPrefs['refresh'] == 'manual') {
       $mCManSelT = 'selected';
    } else if ($myPrefs['refresh'] > 100) {
       $mC180SelT = 'selected';
    } else if ($myPrefs['refresh'] > 30) {
       $mC60SelT = 'selected';
    } else if ($myPrefs['refresh'] > 10) {
       $mC20SelT = 'selected';
    } else {
       $mc5SelT = 'selected';
    }

?>
<p><b><?php echo _AC('chat_message_checking')  ?></b>
	<select name="refresh">
		<option value="5" <?php echo $mc5SelT; ?>><?php echo _AC('chat_auto5_checking');  ?></option>
		<option value="20" <?php echo $mC20SelT; ?>><?php echo _AC('chat_auto20_checking');  ?></option>
        <option value="60" <?php echo $mC60SelT; ?>><?php echo _AC('chat_auto60_checking');  ?></option>
        <option value="180" <?php echo $mC180SelT; ?>><?php echo _AC('chat_auto180_checking');  ?></option>
        <option value="manual" <?php echo $mCManSelT; ?>><?php echo _AC('chat_manual_checking');  ?></option>
	</select></p>
	<p><?php echo _AC('chat_message_check_help');  ?></p>

<!--p style="margin-left: 40;">When you send a message to the <?php echo $admin['chatName']; ?>, the message is not sent immediately to other participants. Instead, it is stored until each participant checks for new messages.</p>

<p style="margin-left: 40;">You may check for messages "Manually" by clicking the "Refresh Messages" link, or you may tell the <?php echo $admin['chatName']; ?> to check for new messages "Automatically" every few seconds.</p>

<p style="margin-left: 40;"><i>We suggest that people using screen readers should set the message checking option to "Manual Refresh", or use a longer automatic checking interval.</i></p -->

<?php
    if ($myPrefs['bingFlag'] > 0) {
       $bFSelT = 'selected';
    }
?>

<p><b><?php echo _AC('chat_message_chime') ?></b>
	<select name="bingFlag">
		<option value="0"><?php echo _AC('chat_chime_no'); ?></option>
		<option value="1" <?php echo $bFSelT; ?>><?php echo _AC('chat_chime_yes'); ?></option>
	</select></p>
<p><?php echo _AC('chat_chime_help');  ?></p>
<!--p style="margin-left: 40;">To let you know when new messages are available, set the new message chime to "Yes". If you have set the message checking to "Manual Refresh", the chime will let you know that a new message is waiting, and clicking "Refresh Messages" will let you see it. If you have set message checking to "automatic", the chime will let you know when new messages are put up on your screen.</p>

<p style="margin-left: 40;"><i>We suggest that people using screen readers should set the new message chime option to "Yes".</i></p>

<p style="margin-left: 40;"><i>Macintosh Users: Please note that the combination of Manual Refresh and New Message Chime does not work in all browsers.</i></p -->

<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<td align="left"><input type="submit" value="<?php echo _AC('chat_next'); ?>" name="submit" class="submit" onFocus="this.className='submit highlight'" onBlur="this.className='submit'" />
                     <input type="submit" value="<?php echo _AC('chat_enter'); ?>" name="submit_r" class="submit" onFocus="this.className='submit highlight'" onBlur="this.className='submit'" /></td>
</tr>
</table>
</form>
<?php
	require('include/html/chat_footer.inc.php');
?>
