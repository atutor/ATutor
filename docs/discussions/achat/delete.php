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
	$chatID		= $_REQUEST['chatID'];
	$uniqueID	= intval($_REQUEST['uniqueID']);

	$myPrefs = loadDefaultPrefs();

	deleteUser($chatID);
	require('include/html/login_header.inc.php');
?>

<br />

<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<td align="left" bgColor="<?php echo $myPrefs['darkBack']; ?>"><h4><?php echo $admin['chatName'];?>: Logout</h4></td>
</tr>
</table>

<p>Your account has been deleted.</p>

<p align="center"><b>Thank you for using the <?php echo $admin['chatName'];?>.<br />
<a href="http://www.utoronto.ca/atrc/" target="_new"><img src="atrc.gif" border="0" /></a></p>

<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<td align="right" bgColor="<?php echo $myPrefs['lightBack'];?>"><a href="./login.php">Re-enter Chat</a> | <?php echo $admin['returnLink']; ?></td>
</tr>
</table>

<?php
	require('include/html/login_footer.inc.php');
?>
