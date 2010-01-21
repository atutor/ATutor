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
	session_write_close();
	//authenticate(USER_CLIENT, USER_ADMIN);
	require(AT_INCLUDE_PATH.'../mods/_standard/chat/lib/chat.inc.php');

	$myPrefs = getPrefs($_SESSION['login']);


	$topMsgNum = $bottomMsgNum = 0;
    howManyMessages($topMsgNum, $bottomMsgNum);
	postMessage(_AT('chat_system'),
				_AT('chat_logged_out', $_SESSION['login']),
				$topMsgNum,
				$bottomMsgNum);

	$myPrefs['lastAccessed'] = 0;
	writePrefs($myPrefs, $_SESSION['login']);

	Header('Location: index.php');
	exit;
	//exit;
require('include/html/chat_header.inc.php');
?>
<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<td align="left"><h4><?php echo $admin['chatName']; ?>: Logout</h4></td>
</tr>
</table>

<p>The <?php echo $admin['chatName']; ?> will automatically save an account for you so that the next time you login with your <em>Chat ID</em> and <em>Password</em> your <em>Preference Settings</em> will be reloaded.</p>

<p align="center"><b>Thank you for using the <?php echo $admin['chatName']; ?>.<br />
<a href="http://www.utoronto.ca/atrc/" target="_new" onFocus="this.className='highlight'" onBlur="this.className=''"><img src="chat/atrc.gif" border="0" /></a></p>


<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr> 
	<td align="right"><a href="chat/index.php" onFocus="this.className='highlight'" onBlur="this.className=''">Re-enter Chat</a></td>
</tr>
</table>
<?php
	require('include/html/chat_footer.inc.php');
?>
