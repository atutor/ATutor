<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }

?>
	<tr>
		<td colspan="2" valign="top" align="left" class="row1">
		<?php print_popup_help(AT_HELP_KEYWORDS); ?>
		<b><label for="keys"><?php echo _AT('keywords'); ?>:</label></b><br />
		<p><textarea name="keywords" class="formfield" cols="73" rows="2" id="keys"><?php echo stripslashes($_POST['keywords']); ?></textarea></p>
		<br />
		</td>
	</tr>