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
	//authenticate(USER_CLIENT, USER_ADMIN);
	require('include/functions.inc.php');
	$myPrefs = getPrefs($_SESSION['username']);


require('include/html/chat_header.inc.php');

?>
<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<th align="left" class="box"><?php echo _AC('chat_help_screen'); ?></th>
</tr>
</table>

<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<td align="right"><a href="chat.php?chatID=<?php echo $_GET['chatID'].SEP.'uniqueID='.$_GET['uniqueID']; ?>" target="_top" onFocus="this.className='highlight'" onBlur="this.className=''"><?php echo _AC('chat_return'); ?></a></td>
</tr>
</table>
<a name="jumps"></a><p>
           <a href="help.php#display" onFocus="this.className='highlight'" onBlur="this.className=''"><?php echo _AC('chat_help_display_jump'); ?></a><br />
           <a href="help.php#options" onFocus="this.className='highlight'" onBlur="this.className=''"><?php echo _AC('chat_help_options_jump'); ?></a><br />
           <a href="help.php#history" onFocus="this.className='highlight'" onBlur="this.className=''"><?php echo _AC('chat_help_history_jump'); ?></a></p>
<a name="display"></a>
<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<th align="left" class="box"><small><?php echo _AC('chat_help_display_frame'); ?></small></th>
</tr>
</table>
<?php echo _AC('chat_help_display_blurb'); ?>
<!-- p>Note: <em>Jump To</em> links and the <em>Quick Key</em> list only appear if you have turned on <em>Navigation Aids</em> in you preferences.</p>

<ul>
	<li>The <em>Jump to Quick Keys</em> link at the top takes you to a list of quick keys for the Chat. </li>
	<li>The <em>Message Area</em> displays the most recent messages (up to ten). </li>
    <li>The <em>Jump to Messages</em> links take you to the beginning of the list of messages. (Alt+M) </li>
    <li>The <em>Refresh Messages</em> link lets you check for new messages (Alt+R). </li>
    <li>The <em>Compose Message</em> Field and <em>Send Button</em> let you enter and send messages (Alt+C to enter the Compose Field, Enter to send a completed message). </li>
</ul -->
<p><a href="help.php#jumps" onFocus="this.className='highlight'" onBlur="this.className=''"><?php echo _AC('chat_help_jump_top'); ?></a>
</p><a name="options"></a>

<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<th align="left" class="box"><small><?php echo _AC('chat_help_options_frame'); ?></small></th>
</tr>
</table>
<?php echo _AC('chat_help_options_blurb'); ?>
<!--ul>
	<li>The <em>Edit Preferences</em> link opens the preferences so you can modify your control and display settings. </li>
    <li>The <em>Exit Chat</em> link ends your chat session.</li>
    <li>The <em>Help</em> link brings you to this screen. (Alt+Q)</li>
</ul -->

<p><a href="help.php#jumps" onFocus="this.className='highlight'" onBlur="this.className=''"><?php echo _AC('chat_help_jump_top'); ?></a></p>
<a name="history"></a>
    
<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<th align="left" class="box"><small><?php echo _AC('chat_help_history_frame'); ?></small></th>
</tr>
</table>
<?php echo _AC('chat_help_history_blurb'); ?>
<!-- p>The <em>User List</em> shows the Chat IDs of all the users in the chat. Click on a Chat ID to see the messages sent by that participant, along with your own messages. Click on <em>All Users</em> to see the messages sent by all participants.<br / -->
<p><br /><a href="help.php#jumps" onFocus="this.className='highlight'" onBlur="this.className=''"><?php echo _AC('chat_help_jump_top'); ?></a></p>

<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<td align="right"><a href="chat.php?chatID=<?php echo $_GET['chatID'].SEP.'uniqueID='.$_GET['uniqueID']; ?>" target="_top" onFocus="this.className='highlight'" onBlur="this.className=''"><?php echo _AC('chat_return'); ?></a></td>
</tr>
</table>

</body>
</html>