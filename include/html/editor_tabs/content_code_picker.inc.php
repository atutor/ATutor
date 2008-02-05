<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }

?>
<?php echo _AT('click_code'); ?>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<?php
	if (isset($current_tab)) {
			echo '<tr><td align="right"><small><strong>'._AT('codes').': </strong></small></td><td><small>';
			echo '<a href="javascript:smilie(\'[?] [/?]\')" title="[?][/?]">',_AT('add_term'), '</a> ';
			echo '<a href="javascript:smilie(\'[code] [/code]\')" title="[code][/code]">'._AT('add_code').'</a>';
			echo '</small></td></tr>';
	} ?>
<tr>
	<td align="right"><small><b><?php echo _AT('colors'); ?>:</b></small></td>
	<td><table border="0" cellspacing="2" cellpadding="0" summary="">
	<tr>
		<td bgcolor="blue"><a href="javascript:smilie('[blue]', '[/blue]')" title="[blue] [/blue]"><img src="<?php echo $_base_path; ?>images/clr.gif" alt="<?php echo _AT('blue'); ?>" height="15" width="15" border="0" /></a></td>
		<td bgcolor="red"><a href="javascript:smilie('[red]', '[/red]')" title="[red] [/red]"><img src="<?php echo $_base_path; ?>images/clr.gif" alt="<?php echo _AT('red'); ?>" height="15" width="15" border="0" /></a></td>
		<td bgcolor="green"><a href="javascript:smilie('[green]', '[/green]')" title="[green] [/green]"><img src="<?php echo $_base_path; ?>images/clr.gif" alt="<?php echo _AT('green'); ?>" height="15" width="15" border="0" /></a></td>
		<td bgcolor="orange"><a href="javascript:smilie('[orange]', '[/orange]')" title="[orange] [/orange]"><img src="<?php echo $_base_path; ?>images/clr.gif" alt="<?php echo _AT('orange'); ?>" height="15" width="15" border="0" /></a></td>
		<td bgcolor="purple"><a href="javascript:smilie('[purple]', '[/purple]')" title="[purple] [/purple]"><img src="<?php echo $_base_path; ?>images/clr.gif" alt="<?php echo _AT('purple'); ?>" height="15" width="15" border="0" /></a></td>
		<td bgcolor="gray"><a href="javascript:smilie('[gray]', '[/gray]')" title="[gray] [/gray]"><img src="<?php echo $_base_path; ?>images/clr.gif" alt="<?php echo _AT('gray'); ?>" height="15" width="15" border="0" /></a></td>
	</tr>
	</table></td>
</tr>
</table>

<?php if ($_POST['setvisual'] && !$_POST['settext']): ?>
	<script type="text/javascript">
	//<!--
	function smilie(thesmilie, extra) {
		if (!extra) {
			tinyMCE.execCommand("mceInsertContent", false, thesmilie);
		} else {
			tinyMCE.execCommand("mceInsertContent", false, thesmilie + extra);
		}
	}
	//--></script>
<?php else: ?>
	<script type="text/javascript">
	//<!--
	function smilie(thesmilie, extra) {
		if (!extra) {
			document.form.body_text.value += thesmilie+" ";
			document.form.body_text.focus();
		} else {
			document.form.body_text.value += thesmilie+extra+" ";
			document.form.body_text.focus();
		}

	}
	//--></script>
<?php endif; ?>