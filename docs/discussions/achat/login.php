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

exit('does not get used');

	require('include/html/login_header.inc.php');
	require('include/vitals.inc.php');
?>

<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<td align="right" bgColor="#ddeecc"><a href="#login">Login</a> | <a href="#help">Help</a></td>
</tr></table>
<br />
<a name="login"></a>
<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<td align="left" bgColor="#bbccaa"><h4>Login</h4></td>
</tr>
</table>

	<p class="light">Welcome to the Accessible Chat. Please select a login option below.</p>

	<p>
		<a href="./new_user.php">New Users</a>
		<br />
		<a href="./returning_user.php">Returning Users</a>
	</p>

<a name="help"></a>
<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<td align="left" bgColor="#bbccaa"><h4>Help</h4></td>
</tr>
</table>

	<p>If you are a new user to the ATRC A-Chat, or you did not save your settings last time you used it, select the <em>New User</em> link. If you are a returning user, and you did save your settings, select the <em>Returning User</em> link. You will be prompted for your <em>Chat ID</em> and <em>Password</em>.</p>
<br />
<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<td align="right" bgColor="#ddeecc"><a href="#login">Back to Login</a> | <?php echo $admin['returnLink']; ?></td>
</tr>
</table>
<?php
	require('include/html/login_footer.inc.php');
?>
