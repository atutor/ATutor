<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

$page    = 'about';
$_user_location	= 'public';

define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH.'/vitals.inc.php');
require(AT_INCLUDE_PATH.'header.inc.php');

?>
<table border="0" cellspacing="0" cellpadding="0">
<tr>
	<td valign="top"><br /><img src="images/ss.gif" height="223" width="301" alt="ATutor screen shot"/></td>
	<td><p><?php echo _AT('atutor_is');  ?></p>
	<?php echo _AT('atutor_links');  ?><br /><br />
	</td>
</tr>
</table>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>