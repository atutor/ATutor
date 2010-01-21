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

define('AT_INCLUDE_PATH', '../../../include/');
	require(AT_INCLUDE_PATH.'vitals.inc.php');
	//authenticate(USER_CLIENT, USER_ADMIN);
require(AT_INCLUDE_PATH.'../mods/_standard/chat/lib/chat.inc.php');

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
	<th align="left"><h4><?php echo _AT('chat_prefs_checking'); ?></h4></th>
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
<p><b><?php echo _AT('chat_message_checking')  ?></b>
	<select name="refresh">
		<option value="5" <?php echo $mc5SelT; ?>><?php echo _AT('chat_auto5_checking');  ?></option>
		<option value="20" <?php echo $mC20SelT; ?>><?php echo _AT('chat_auto20_checking');  ?></option>
        <option value="60" <?php echo $mC60SelT; ?>><?php echo _AT('chat_auto60_checking');  ?></option>
        <option value="180" <?php echo $mC180SelT; ?>><?php echo _AT('chat_auto180_checking');  ?></option>
        <option value="manual" <?php echo $mCManSelT; ?>><?php echo _AT('chat_manual_checking');  ?></option>
	</select></p>
	<p><?php echo _AT('chat_message_check_help');  ?></p>

<?php
    if ($myPrefs['bingFlag'] > 0) {
       $bFSelT = 'selected';
    }
?>

<p><b><?php echo _AT('chat_message_chime') ?></b>
	<select name="bingFlag">
		<option value="0"><?php echo _AT('no'); ?></option>
		<option value="1" <?php echo $bFSelT; ?>><?php echo _AT('yes'); ?></option>
	</select></p>
<p><?php echo _AT('chat_chime_help');  ?></p>

<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<td align="left"><input type="submit" value="<?php echo _AT('next'); ?>" name="submit" class="submit" onFocus="this.className='submit highlight'" onBlur="this.className='submit'" />
                     <input type="submit" value="<?php echo _AT('chat_enter'); ?>" name="submit_r" class="submit" onFocus="this.className='submit highlight'" onBlur="this.className='submit'" /></td>
</tr>
</table>
</form>
<?php require('include/html/chat_footer.inc.php'); ?>