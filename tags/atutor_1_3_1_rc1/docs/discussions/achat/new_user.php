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

require('include/vitals.inc.php');

if ($_POST['submit']) {

	if (file_exists('users/'.$_POST['username'].'.prefs')) {
		echo 'user already exists!';
		exit;
	}
	if ($_POST['username'] == ''){
		echo 'username == ""';
		exit;
	}
	if ($_POST['pass1'] != $_POST['pass2']) {
		echo 'pass1 != pass2';
		exit;
	}

	/* load defaults */
	$myPrefs = loadDefaultPrefs();
	$myPrefs['password'] = md5($_POST['pass1']);
	$myPrefs['uniqueID'] = time();
	$myPrefs['language'] = 'EN'; /* for future functionality */

	if (!writePrefs($myPrefs, $_POST['username'])) {
		echo 'error creating user';
		exit;
	}

	$location = './chat.php?chatID='.$_POST['username'].SEP.'uniqueID='.$myPrefs['uniqueID'].SEP.'firstLoginFlag=1';
	Header('Location: '.$location);
	exit;
}

	require('include/html/login_header.inc.php');
?>
<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<td align="right" bgColor="#ddeecc"><a href="#login">Login</a> | <a href="#help">Help</a></td>
</tr>
</table>
<br />
<a name="login"></a>
<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<td align="left" bgColor="#bbccaa"><h4>New User Login</h4></td>
</tr>
</table>

<p class="light">Please create a new Chat ID using the form below.</p>
<form action="./new_user.php" name="f1" method="post" target="_top">
	<input type="hidden" name="chatID" value="" />
	<input type="hidden" name="uniqueID" value="" />
	<input type="hidden" name="function" value="signInNew" />
	<p>
		<table border="0" cellpadding="5" cellspacing="0">
		<tr>
			<td align="right">Chat ID:</td>
			<td><input type="text" maxlength="10" name="username" /></td>
		</tr>
		<tr>
			<td align="right">Password:</td>
			<td><input type="password" maxlength="10" name="pass1" /></td>
		</tr>
		<tr>
			<td align="right">Re-Enter Password:</td>
			<td><input type="password" maxlength="10" name="pass2" /></td>
		</tr>
		<tr>
			<td><input type="submit" name="submit" value="Enter Chat" /></td>
			<td></td>
		</tr>
		</table>
	</p>
</form>

<a name="help"></a>
<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<td align="left" bgColor="#bbccaa"><h4>Help</h4></td>
</tr>
</table>
<p>The <em>Chat ID</em> is the name that you will
	have in the chat session. The Chat ID doesn't have to be your
	real name - it could be a nickname or even a made-up word.
	Just make sure it is easy for you to remember. <em>The Chat ID
	should be alphanumeric, and contain no white space</em>.
	</p>
	
	<p> The <em>Password</em> prevents others from
	using your Chat ID to send messages. 
	Choose a password that is easy
	for you to remember, but hard for other to guess.
	</p>
	<p>
	You will need to <em>Re-enter your Password</em> to ensure it has been
	typed correctly.
	</p><br />
<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<td align="right" bgColor="#ddeecc"><a href="./login.php">Back to Login</a> | <a href="http://achat.atrc.utoronto.ca/">Return to A-Chat Home</a></td>
</tr>
</table>

<?php
	require('include/html/login_footer.inc.php');
?>
