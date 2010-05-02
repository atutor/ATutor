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
if (!defined('AT_INCLUDE_PATH')) { exit; }

?>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
	<td colspan="2"><small><?php echo _AT('click_code'); ?></small></td>
</tr>
<tr>
	<td align="right"><small><strong><?php echo _AT('emoticons'); ?>:</strong></small></td>
	<td><small><?php
		echo '<a href="javascript:smilie(\':)\')" title=":)">'.smile_replace(':)').'</a> ';
		echo '<a href="javascript:smilie(\';)\')" title=";)">'.smile_replace(';)').'</a> ';
		echo '<a href="javascript:smilie(\':(\')" title=":(">'.smile_replace(':(').'</a> ';
		echo '<a href="javascript:smilie(\'::ohwell::\')" title=":\\">'.smile_replace('::ohwell::').'</a> ';

		echo '<a href="javascript:smilie(\':P\')" title=":P">'.smile_replace(':P').'</a> ';
		echo '<a href="javascript:smilie(\'::angry::\')" title="::angry::">'.smile_replace('::angry::').'</a> ';

		echo '<a href="javascript:smilie(\'::evil::\')" title="::evil::">'.smile_replace('::evil::').'</a> ';
		echo '<a href="javascript:smilie(\'::lol::\')" title="::lol::">'.smile_replace('::lol::').'</a> ';
		echo '<a href="javascript:smilie(\'::confused::\')" title="::confused::">'.smile_replace('::confused::').'</a> ';
		echo '<a href="javascript:smilie(\'::crazy::\')" title="::crazy::">'.smile_replace('::crazy::').'</a> ';

		echo '<a href="javascript:smilie(\'::tired::\')" title="::tired::">'.smile_replace('::tired::').'</a> ';
		echo '<a href="javascript:smilie(\'::muah::\')" title="::muah::">'.smile_replace('::muah::').'</a>';
	?></small></td>
</tr>
<tr>
	<td align="right"><small><strong><?php echo _AT('codes'); ?>:</strong></small></td>
	<td> <small><a href="javascript:smilie('[b] [/b]')" title="[b] [/b]"><?php echo _AT('bold'); ?></a>,
	<a href="javascript:smilie('[i] [/i]')" title="[i] [/i]"><?php echo _AT('italic'); ?></a>,
	<a href="javascript:smilie('[u] [/u]')" title="[u] [/u]"><?php echo _AT('underline'); ?></a>,
	<a href="javascript:smilie('[center] [/center]')" title="[center] [/center]"><?php echo _AT('center'); ?></a>,
	<a href="javascript:smilie('[quote] [/quote]')" title="[quote] [/quote]"><?php echo _AT('quote'); ?></a>,
	<a href="javascript:smilie('http://')" title="http://"><?php echo _AT('link'); ?></a>,
	<a href="javascript:smilie('[image|alt text][/image]')" title="[image|alt text][/image]"><?php echo _AT('image'); ?></a>,
	<a href="javascript:smilie('[media][/media]')" title="[media] [/media]"><?php echo _AT('media'); ?></a><?php
	if (isset($current_tab)) {
		echo ',	<a href="javascript:smilie(\' [?][/?]\')" title="[?][/?]">',_AT('add_term'), '</a>';
		echo '<a href="javascript:smilie(\' [code][/code]\')" title="[code][/code]" onclick="document.form.formatting.html.checked=true;">',_AT('add_code'), '</a>';
	} ?></small></td>
</tr>
<tr>
	<td align="right"><small><strong><?php echo _AT('colors'); ?>:</strong></small></td>
	<td><table border="0" cellspacing="2" cellpadding="0" summary="">
	<tr>
		<td style="background:blue;"><a href="javascript:smilie('[blue] [/blue]')" title="[blue] [/blue]" style="color:white; margin:.5em;"><?php echo _AT('blue'); ?></a></td>
		
		<td  style="background:red;"><a href="javascript:smilie('[red] [/red]')" title="[red] [/red]" style="color:white;margin:.5em;"><?php echo _AT('red'); ?></a></td>
		
		<td  style="background:green;"><a href="javascript:smilie('[green] [/green]')" title="[green] [/green]" style="color:white;margin:.5em;"><?php echo _AT('green'); ?></a></td>
		
		<td  style="background:orange;" style="color:white;margin:.5em;"><a href="javascript:smilie('[orange] [/orange]')" title="[orange] [/orange]" style="color:white;margin:.5em;"> <?php echo _AT('orange'); ?></a></td>
		
		<td  style="background:purple;"><a href="javascript:smilie('[purple] [/purple]')" title="[purple] [/purple]" style="color:white;margin:.5em;"><?php echo _AT('purple'); ?></a></td>
		
		<td style="background:gray;"><a href="javascript:smilie('[gray] [/gray]')" title="[gray] [/gray]" style="color:white;margin:.5em;"><?php echo _AT('gray'); ?></a></td>
	</tr>
	</table></td>
</tr>
</table>
	<script type="text/javascript">
	//<!--
	function smilie(thesmilie) {
		// inserts smilie text
		document.form.body.value += thesmilie+" ";
		document.form.body.focus();
	}

	//-->
	</script>