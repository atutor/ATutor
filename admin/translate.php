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

$_REQUEST['u'] = 'admin';

$_user_location = 'admin';

$variables = array('_template', '_msgs');
// Get the language codes for the languages on the current system

define('AT_INCLUDE_PATH', '../include/');
?>

<script language="javascript" type="text/javascript">
function openWindow(page) {
	newWindow = window.open(page, "progWin", "width=400,height=200,toolbar=no,location=no");
	newWindow.focus();
}
</script>

<br /><form name="form1" method="post" action="admin/import_lang.php" enctype="multipart/form-data" onsubmit="openWindow('tools/prog.php');">
<input type="hidden" name="import" value="1" />
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="80%" summary="" align="center">
<tr>
	<td class="cyan" colspan="2"><?php echo _AT('import_a_new_lang'); ?></td>
</tr>
<tr>
	<td colspan="2"><small><?php echo _AT('import_lang_howto'); ?></small></td>
</tr>
<tr>
	<td align="center" colspan="2"><br /><strong><?php echo _AT('import_a_new_lang'); ?></strong>: <input type="file" name="file" class="formfield" /> | <input type="submit" name="submit" value="<?php echo _AT('import'); ?>" class="button" /><br /><br /></td>
</tr>
</table>
</form>
