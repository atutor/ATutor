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
	<td colspan="2"><small><?php echo _AT('click_code'); ?></small></td>
</tr>
<?php if (!$hide_learning_concepts) { ?>
<tr>
	<td colspan="2"><?php require(AT_INCLUDE_PATH.'html/learning_concepts.inc.php'); ?></td>
</tr>
<?php } ?>
<tr>
	<td align="right"><small><b><?php echo _AT('emoticons'); ?>:</b></small></td>
	<td><small><?php
		echo '<a href="javascript:smilie(\':)\')" title=":)" onfocus="this.className=\'highlight\'" onblur="this.className=\'\'">'.smile_replace(':)').'</a> ';
		echo '<a href="javascript:smilie(\';)\')" title=";)" onfocus="this.className=\'highlight\'" onblur="this.className=\'\'">'.smile_replace(';)').'</a> ';
		echo '<a href="javascript:smilie(\':(\')" title=":(" onfocus="this.className=\'highlight\'" onblur="this.className=\'\'">'.smile_replace(':(').'</a> ';
		echo '<a href="javascript:smilie(\':\\\ \')" title=":\\" onfocus="this.className=\'highlight\'" onblur="this.className=\'\'">'.smile_replace(':\\').'</a> ';

		echo '<a href="javascript:smilie(\':P\')" title=":P" onfocus="this.className=\'highlight\'" onblur="this.className=\'\'">'.smile_replace(':P').'</a> ';
		echo '<a href="javascript:smilie(\'::angry::\')" title="::angry::" onfocus="this.className=\'highlight\'" onblur="this.className=\'\'">'.smile_replace('::angry::').'</a> ';

		echo '<a href="javascript:smilie(\'::evil::\')" title="::evil::" onfocus="this.className=\'highlight\'" onblur="this.className=\'\'">'.smile_replace('::evil::').'</a> ';
		echo '<a href="javascript:smilie(\'::lol::\')" title="::lol::" onfocus="this.className=\'highlight\'" onblur="this.className=\'\'">'.smile_replace('::lol::').'</a> ';
		echo '<a href="javascript:smilie(\'::confused::\')" title="::confused::" onfocus="this.className=\'highlight\'" onblur="this.className=\'\'">'.smile_replace('::confused::').'</a> ';
		echo '<a href="javascript:smilie(\'::crazy::\')" title="::crazy::" onfocus="this.className=\'highlight\'" onblur="this.className=\'\'">'.smile_replace('::crazy::').'</a> ';

		echo '<a href="javascript:smilie(\'::tired::\')" title="::tired::" onfocus="this.className=\'highlight\'" onblur="this.className=\'\'">'.smile_replace('::tired::').'</a> ';
		echo '<a href="javascript:smilie(\'::muah::\')" title="::muah::" onfocus="this.className=\'highlight\'" onblur="this.className=\'\'">'.smile_replace('::muah::').'</a> ';
		echo '<a href="javascript:smilie(\'::wow::\')" title="::wow::" onfocus="this.className=\'highlight\'" onblur="this.className=\'\'">'.smile_replace('::wow::').'</a>';
	?></small></td>
</tr>
<tr>
	<td align="right"><small><b><?php echo _AT('codes'); ?>:</b></small></td>
	<td><small><a href="javascript:smilie('[b] [/b]')" title="[b] [/b]" onfocus="this.className='highlight'" onblur="this.className=''"><?php echo _AT('bold'); ?></a>
	<a href="javascript:smilie('[i] [/i]')" title="[i] [/i]" onfocus="this.className='highlight'" onblur="this.className=''"><?php echo _AT('italic'); ?></a>,
	<a href="javascript:smilie('[u] [/u]')" title="[u] [/u]" onfocus="this.className='highlight'" onblur="this.className=''"><?php echo _AT('underline'); ?></a>,
	<a href="javascript:smilie('[center] [/center]')" title="[center] [/center]" onfocus="this.className='highlight'" onblur="this.className=''"><?php echo _AT('center'); ?></a>,
	<a href="javascript:smilie('[quote] [/quote]')" title="[quote] [/quote]" onfocus="this.className='highlight'" onblur="this.className=''"><?php echo _AT('quote'); ?></a>,
	<a href="javascript:smilie('http://')" title="http://" onfocus="this.className='highlight'" onblur="this.className=''"><?php echo _AT('link'); ?></a>,
	<a href="javascript:smilie('[image|alt text][/image]')" title="[image|alt text][/image]" onfocus="this.className='highlight'" onblur="this.className=''"><?php echo _AT('image'); ?></a></small></td>
</tr>
<tr>
	<td align="right"><small><b><?php echo _AT('colors'); ?>:</b></small></td>
	<td><table border="0" cellspacing="2" cellpadding="0" summary="">
	<tr>
		<td bgcolor="blue"><a href="javascript:smilie('[blue] [/blue]')" title="[blue] [/blue]"><img src="<?php echo $_base_path; ?>images/clr.gif" alt="<?php echo _AT('blue'); ?>" height="15" width="15" border="0" /></a></td>
		<td bgcolor="red"><a href="javascript:smilie('[red] [/red]')" title="[red] [/red]"><img src="<?php echo $_base_path; ?>images/clr.gif" alt="<?php echo _AT('red'); ?>" height="15" width="15" border="0" /></a></td>
		<td bgcolor="green"><a href="javascript:smilie('[green] [/green]')" title="[green] [/green]"><img src="<?php echo $_base_path; ?>images/clr.gif" alt="<?php echo _AT('green'); ?>" height="15" width="15" border="0" /></a></td>
		<td bgcolor="orange"><a href="javascript:smilie('[orange] [/orange]')" title="[orange] [/orange]"><img src="<?php echo $_base_path; ?>images/clr.gif" alt="<?php echo _AT('orange'); ?>" height="15" width="15" border="0" /></a></td>
		<td bgcolor="purple"><a href="javascript:smilie('[purple] [/purple]')" title="[purple] [/purple]"><img src="<?php echo $_base_path; ?>images/clr.gif" alt="<?php echo _AT('purple'); ?>" height="15" width="15" border="0" /></a></td>
		<td bgcolor="gray"><a href="javascript:smilie('[gray] [/gray]')" title="[gray] [/gray]"><img src="<?php echo $_base_path; ?>images/clr.gif" alt="<?php echo _AT('gray'); ?>" height="15" width="15" border="0" /></a></td>
	</tr>
	</table></td>
</tr>
</table>
	
	<script type="text/javascript">
	<!--
	function smilie(thesmilie) {
		// inserts smilie text
		document.form.body.value += thesmilie+" ";
		document.form.body.focus();
	}

	//-->
	</script>