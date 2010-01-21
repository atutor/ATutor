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

	$myPrefs = getPrefs($_SESSION['username']);
	writePrefs($myPrefs, $_SESSION['username']);

	cleanUp();
	$topMsgNum = $bottomMsgNum = 0;
    howManyMessages($topMsgNum, $bottomMsgNum);

    $filterChatID = $_GET['filterChatID'];

require('include/html/chat_header.inc.php');
?>
<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<td align="right"><a href="chat.php" target="_top" onFocus="this.className='highlight'" onBlur="this.className=''"><?php echo _AT('chat_return'); ?></a></td>
</tr>
</table>

<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<th align="left" class="box"><?php echo _AT('history'); ?></th>
</tr>
</table>
<p><table border="0" cellpadding="2" cellspacing="0" width="90%" class="box2">
<?php
    if ($myPrefs['newestFirstFlag'] > 0) {
        for ($i = $topMsgNum; $i >= 1; $i--) {
            showMessageFiltered($i, $myPrefs, $filterChatID);
        }
    } else {
        for ($i = 1; $i <= $topMsgNum ; $i++) {
            showMessageFiltered($i, $myPrefs, $filterChatID);
        }    
    }
?>
</table></p>
<br />

<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<td align="right"><a href="chat.php" target="_top" onFocus="this.className='highlight'" onBlur="this.className=''"><?php echo _AT('chat_return'); ?></a></td>
</tr>
</table>
<?php require('include/html/chat_footer.inc.php'); ?>