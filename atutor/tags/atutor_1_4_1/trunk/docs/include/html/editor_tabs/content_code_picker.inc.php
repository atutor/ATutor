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
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
	<td></td><td><small><?php echo _AT('click_code'); ?></small></td>
</tr>
<?php if (!$hide_learning_concepts) { ?>
<tr>
	<td></td><td><?php require(AT_INCLUDE_PATH.'html/learning_concepts.inc.php'); ?></td>
</tr>
<?php } ?>
<tr>
	<td align="right"><small><b><?php echo _AT('emoticons'); ?>: </b></small></td>
	<td><small><?php
		echo '<a href="javascript:smilie(\':)\')" title=":)" onclick="document.form.formatting.html.checked=\'true\';">'.smile_replace(':)').'</a> ';
		echo '<a href="javascript:smilie(\';)\')" title=";)" onclick="document.form.formatting.html.checked=\'true\';">'.smile_replace(';)').'</a> ';
		echo '<a href="javascript:smilie(\':(\')" title=":(" onclick="document.form.formatting.html.checked=\'true\';">'.smile_replace(':(').'</a> ';
		echo '<a href="javascript:smilie(\'::ohwell::\')" title=":\\" onclick="document.form.formatting.html.checked=\'true\';">'.smile_replace('::ohwell::').'</a> ';

		echo '<a href="javascript:smilie(\':P\')" title=":P" onclick="document.form.formatting.html.checked=\'true\';">'.smile_replace(':P').'</a> ';
		echo '<a href="javascript:smilie(\'::angry::\')" title="::angry::" onclick="document.form.formatting.html.checked=\'true\';">'.smile_replace('::angry::').'</a> ';

		echo '<a href="javascript:smilie(\'::evil::\')" title="::evil::" onclick="document.form.formatting.html.checked=\'true\';">'.smile_replace('::evil::').'</a> ';
		echo '<a href="javascript:smilie(\'::lol::\')" title="::lol::" onclick="document.form.formatting.html.checked=\'true\';">'.smile_replace('::lol::').'</a> ';
		echo '<a href="javascript:smilie(\'::confused::\')" title="::confused::" onclick="document.form.formatting.html.checked=\'true\';">'.smile_replace('::confused::').'</a> ';
		echo '<a href="javascript:smilie(\'::crazy::\')" title="::crazy::" onclick="document.form.formatting.html.checked=\'true\';">'.smile_replace('::crazy::').'</a> ';

		echo '<a href="javascript:smilie(\'::tired::\')" title="::tired::" onclick="document.form.formatting.html.checked=\'true\';">'.smile_replace('::tired::').'</a> ';
		echo '<a href="javascript:smilie(\'::muah::\')" title="::muah::" onclick="document.form.formatting.html.checked=\'true\';">'.smile_replace('::muah::').'</a>';
	?></small></td>
</tr>
<?php
	if (isset($current_tab)) {
		if ($_POST['setvisual'] && !$_POST['settext']) { /*
			echo '<tr><td align="right"><small><b>'._AT('codes').': </b></small></td><td><small>';
			echo '<a href="javascript:myglossary(editor, \'body_text\')" title="[?][/?]">',_AT('add_term'), '</a> ';
			echo '<a href="javascript:mycode(editor, \'body_text\')" title="[code][/code]" onclick="document.form.formatting.html.checked=\'true\';">'._AT('add_code').'</a>';
			echo '</small></td></tr>';
			*/
		} else {
			echo '<tr><td align="right"><small><b>'._AT('codes').': </b></small></td><td><small>';
			echo '<a href="javascript:smilie(\'[?]\', \'[/?]\')" title="[?][/?]">',_AT('add_term'), '</a> ';
			echo '<a href="javascript:smilie(\'[code]\', \'[/code]\')" title="[code][/code]" onclick="document.form.formatting.html.checked=\'true\';">'._AT('add_code').'</a>';
			echo '</small></td></tr>';
		}
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

<?php if ($_POST['setvisual'] && !$_POST['settext']) { ?>

<script type="text/javascript"><!--
function smilie(thesmilie, extra) {
	editor.focusEditor();
	if (!extra) {
		editor.insertHTML(thesmilie);
	}
	else
	{
		editor.surroundHTML(thesmilie, extra);
	}
}
//--></script>

<?php } else { ?>

<script type="text/javascript"><!--
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

<?php } ?>