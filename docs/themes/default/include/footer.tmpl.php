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
if (!defined('AT_INCLUDE_PATH')) { exit; } 
?>
	</td>
<?php if ($_SESSION['course_id'] > 0): ?>
	<td valign="top">
		<?php require(AT_INCLUDE_PATH.'html/dropdowns.inc.php'); ?>
	</td>
<?php endif; ?>

</tr>
</table>

<br />
<br />
<?php require(AT_INCLUDE_PATH.'html/languages.inc.php'); ?>
<?php require(AT_INCLUDE_PATH.'html/copyright.inc.php'); ?>

</body>
</html>