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

	$hisTopNum = $_GET['hisTopNum'];

	if ($hisTopNum > $topMsgNum) {
		$hisTopNum = $topMsgNum;
	}
    if (!$hisTopNum) {
		$hisTopNum = $topMsgNum;
	}

    $hisBottomNum = getLower20Bound($hisTopNum, $bottomMsgNum);

    if ($hisBottomNum == 0) {
		$hisBottomNum = 1;
	}
    $totalNum = $topMsgNum - $bottomMsgNum + 1;

    $hisTopNumUserPerspective = $hisTopNum - $bottomMsgNum + 1;
    $hisBottomNumUserPerspective = $hisBottomNum - $bottomMsgNum + 1;

    if ($hisBottomNumUserPerspective < 1) {
		$hisBottomNumUserPerspective = 1;
	}

	$prevNumT = $hisBottomNum - 1;
    $nextNumT = $hisTopNum + 20;

require('include/html/chat_header.inc.php');


	if ($hisTopNum < $topMsgNum && $hisBottomNum > $bottomMsgNum) {
?>
		<table width="100%" border="0" cellpadding="5" cellspacing="0">
		<tr>
			<td align="right"><a href="history.php?hisTopNum=<?php echo $prevNumT; ?>" target="_top" onFocus="this.className='highlight'" onBlur="this.className=''"><?php echo _AT('previous'); ?></a> | <a href="history.php?hisTopNum=<?php echo $nextNumT; ?>" target="_top" onFocus="this.className='highlight'" onBlur="this.className=''"><?php echo _AT('next'); ?></a> | <a href="chat.php" target="_top" onFocus="this.className='highlight'" onBlur="this.className=''"><?php echo _AT('chat_return'); ?></a></td>
		</tr>
		</table>
<?php
    } else if ($hisBottomNum > $bottomMsgNum) {
?>
		<table width="100%" border="0" cellpadding="5" cellspacing="0">
		<tr>
			<td align="right"><a href="history.php?hisTopNum=<?php echo $prevNumT; ?>" target="_top" onFocus="this.className='highlight'" onBlur="this.className=''"><?php echo _AT('previous'); ?></a> | <a href="chat.php" target="_top" onFocus="this.className='highlight'" onBlur="this.className=''"><?php echo _AT('chat_return'); ?></a></td>
		</tr>
		</table>
<?php
	} else {
?>
		<table width="100%" border="0" cellpadding="5" cellspacing="0">
		<tr>
			<td align="right"><a href="history.php?hisTopNum=<?php echo $nextNumT; ?>" target="_top" onFocus="this.className='highlight'" onBlur="this.className=''"><?php echo _AT('next'); ?></a> | <a href="chat.php" target="_top" onFocus="this.className='highlight'" onBlur="this.className=''"><?php echo _AT('chat_return'); ?></a></td>
		</tr>
		</table>
<?php
	}
?>
<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<th align="left" class="box"><?php echo _AT('chat_history_messages', $hisBottomNumUserPerspective, $hisTopNumUserPerspective, $totalNum); ?></th>
</tr>
</table>
<?php
    echo '<p><table border="0" cellpadding="2" cellspacing="0" width="90%" class="box2">';

    if ($myPrefs['newestFirstFlag'] > 0) {
        for ($i = $hisTopNum; $i >= $hisBottomNum; $i--) {
            showMessage($i, $myPrefs);
        }
    } else {
        for ($i = $hisBottomNum; $i <= $hisTopNum ; $i++) {
            showMessage($i, $myPrefs);
        }
    }
    echo '</table></p>';

	if ($hisTopNum < $topMsgNum && $hisBottomNum > $bottomMsgNum) {
?>
		<table width="100%" border="0" cellpadding="5" cellspacing="0">
		<tr>
			<td align="right"><a href="history.php?hisTopNum=<?php echo $prevNumT; ?>" target="_top" onFocus="this.className='highlight'" onBlur="this.className=''">Previous</a> | <a href="history.php?hisTopNum=<?php echo $nextNumT; ?>" target="_top" onFocus="this.className='highlight'" onBlur="this.className=''">Next</a> | <a href="chat.php" target="_top" onFocus="this.className='highlight'" onBlur="this.className=''">Return to Chat</a></td>
		</tr>
		</table>
<?php

	} else if ($hisBottomNum > $bottomMsgNum) {
?>
		<table width="100%" border="0" cellpadding="5" cellspacing="0">
		<tr>
			<td align="right"><a href="history.php?hisTopNum=<?php echo $prevNumT; ?>" target="_top" onFocus="this.className='highlight'" onBlur="this.className=''">Previous</a> | <a href="chat.php" target="_top" onFocus="this.className='highlight'" onBlur="this.className=''">Return to Chat</a></td>
		</tr>
		</table>
<?php
    } else {
?>
		<table width="100%" border="0" cellpadding="5" cellspacing="0">
		<tr>
			<td align="right"><a href="history.php?hisTopNum=<?php echo $nextNumT; ?>" target="_top" onFocus="this.className='highlight'" onBlur="this.className=''">Next</a> | <a href="chat.php" target="_top" onFocus="this.className='highlight'" onBlur="this.className=''">Return to Chat</a></td>
		</tr>
		</table>
<?php
    }

	require('include/html/chat_footer.inc.php');
?>