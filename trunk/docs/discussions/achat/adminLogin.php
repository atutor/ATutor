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
	$myPrefs = loadDefaultPrefs();
?>
<html>
<META HTTP-EQUIV="Pragma" CONTENT="no-cache" />
<head>
	<title>Administrative Login Screen</title>
</head>
<?php
	printStylesheet($myPrefs);
?>
<body bgColor="<?php echo $myPrefs['back']; ?>" text="<?php echo $myPrefs['front']; ?>">

<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<td align="left" bgColor="<?php echo $myPrefs['darkBack'];?>"><h3><?php echo $admin['chatName'];?>: Administrator Login</h3></td>
</tr>
</table>

<form name="f1" method="post" action="./admin.php">
Please enter the administrative password: <br /><input type="password" name="adminPass" /> <input type="submit" value="submit" name="s" /></form></p>

</body>
</html>
