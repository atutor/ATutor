<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
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


require('include/html/chat_header.inc.php');

?>
<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<th align="left" class="box"><?php echo _AT('chat_help_screen'); ?></th>
</tr>
</table>

<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<td align="right"><a href="chat.php?chatID=<?php echo $_GET['chatID'].SEP.'uniqueID='.$_GET['uniqueID']; ?>" target="_top" onFocus="this.className='highlight'" onBlur="this.className=''"><?php echo _AT('chat_return'); ?></a></td>
</tr>
</table>
<a name="jumps"></a><p>
           <a href="help.php#display" onFocus="this.className='highlight'" onBlur="this.className=''"><?php echo _AT('chat_help_display_jump'); ?></a><br />
           <a href="help.php#options" onFocus="this.className='highlight'" onBlur="this.className=''"><?php echo _AT('chat_help_options_jump'); ?></a><br />
           <a href="help.php#history" onFocus="this.className='highlight'" onBlur="this.className=''"><?php echo _AT('chat_help_history_jump'); ?></a></p>
<a name="display"></a>
<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<th align="left" class="box"><small><?php echo _AT('chat_help_display_frame'); ?></small></th>
</tr>
</table>
<?php echo _AT('chat_help_display_blurb'); ?>

<p><a href="help.php#jumps" onFocus="this.className='highlight'" onBlur="this.className=''"><?php echo _AT('chat_help_jump_top'); ?></a>
</p><a name="options"></a>

<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<th align="left" class="box"><small><?php echo _AT('chat_help_options_frame'); ?></small></th>
</tr>
</table>
<?php echo _AT('chat_help_options_blurb'); ?>

<p><a href="help.php#jumps" onFocus="this.className='highlight'" onBlur="this.className=''"><?php echo _AT('chat_help_jump_top'); ?></a></p>
<a name="history"></a>
    
<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<th align="left" class="box"><small><?php echo _AT('chat_help_history_frame'); ?></small></th>
</tr>
</table>
<?php echo _AT('chat_help_history_blurb'); ?>

<p><br /><a href="help.php#jumps" onFocus="this.className='highlight'" onBlur="this.className=''"><?php echo _AT('chat_help_jump_top'); ?></a></p>

<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<td align="right"><a href="chat.php?chatID=<?php echo $_GET['chatID'].SEP.'uniqueID='.$_GET['uniqueID']; ?>" target="_top" onFocus="this.className='highlight'" onBlur="this.className=''"><?php echo _AT('chat_return'); ?></a></td>
</tr>
</table>

</body>
</html>