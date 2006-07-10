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
session_write_close();
//authenticate(USER_CLIENT, USER_ADMIN);
require(AT_INCLUDE_PATH.'lib/chat.inc.php');

$myPrefs = getPrefs($_GET['chatID']);

require('include/html/chat_header.inc.php');

$now = time();
?>
<table width="100%" border="0" cellpadding="5" cellspacing="0" class="input-form">
<tr>
	<th align="left"><?php echo _AC('chat_compose_message'); ?></th>
</tr>
<tr>
	<td>
	<form action="display.php?set=<?php echo $now; ?>" target="display" name="f1" method="post" onsubmit="return checkForm();" />
		<input type="hidden" name="message" value="<?php echo $now; ?>" /><label accesskey="c" for="tempField"><input type="text" maxlength="200" size="40" name="tempField" id="tempField" value="" class="input" onfocus="this.className='input highlight'" onblur="this.className='input'" /></label><input type="submit" value="<?php echo _AC('chat_send'); ?>" class="button" onfocus="this.className='submit highlight'" onblur="this.className='submit'" />	
	</form>
	</td>
</tr>
</table>
<script language="javascript" type="text/javascript"><!--
	function checkForm() {
		document.f1.message.value = document.f1.tempField.value;
        if (document.f1.message.value == "" || !document.f1.message.value) return false;
		document.f1.tempField.value = "";
        return true;
	}
//--></script>
<?php require('include/html/chat_footer.inc.php'); ?>