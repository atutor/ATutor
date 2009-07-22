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
	//not getting session username
	$myPrefs = getPrefs($_SESSION['login']);

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
	<th align="left"><h4><?php echo _AT('chat_layout_prefs'); ?></h4></th>
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

<p><b><?php echo _AT('chat_order_prefs'); ?></b>
	<select name="newestFirstFlag">
		<option value="0"><?php echo _AT('chat_oldnew_prefs'); ?></option>
		<option value="1" <?php echo $nFFSelT;?>><?php echo _AT('chat_newold_prefs'); ?></option>
	</select></p>
<p><?php echo _AT('chat_message_order_help') ;?></p>

<?php
    if ($myPrefs['onlyNewFlag'] > 0) {
       $oNFSelT = 'selected';
    }
?>

<p><b><?php echo _AT('chat_newmsg_prefs'); ?></b>
	<select name="onlyNewFlag">
		<option value="0"><?php echo _AT('no'); ?></option>
        <option value="1" <?php echo $oNFSelT;?>><?php echo _AT('yes'); ?></option>
	</select></p>
<p><?php echo _AT('chat_message_new_help') ;?></p>

<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<td align="left"><input type="submit" value="<?php echo _AT('previous'); ?>" name="submit_p" class="submit" onFocus="this.className='submit highlight'" onBlur="this.className='submit'" /> <input type="submit" value="<?php echo _AT('chat_enter'); ?>" name="submit_r" class="submit" onFocus="this.className='submit highlight'" onBlur="this.className='submit'" /></td>
</tr>
</table>

</form>
<?php require('include/html/chat_footer.inc.php'); ?>