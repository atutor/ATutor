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
		echo '<a href="javascript:smilie(\':)\')" title=":)" onclick="document.form.formatting.html.checked=true;">'.smile_replace(':)').'</a> ';
		echo '<a href="javascript:smilie(\';)\')" title=";)" onclick="document.form.formatting.html.checked=true;">'.smile_replace(';)').'</a> ';
		echo '<a href="javascript:smilie(\':(\')" title=":(" onclick="document.form.formatting.html.checked=true;">'.smile_replace(':(').'</a> ';
		echo '<a href="javascript:smilie(\'::ohwell::\')" title=":\\" onclick="document.form.formatting.html.checked=true;">'.smile_replace('::ohwell::').'</a> ';

		echo '<a href="javascript:smilie(\':P\')" title=":P" onclick="document.form.formatting.html.checked=true;">'.smile_replace(':P').'</a> ';
		echo '<a href="javascript:smilie(\'::angry::\')" title="::angry::" onclick="document.form.formatting.html.checked=true;">'.smile_replace('::angry::').'</a> ';

		echo '<a href="javascript:smilie(\'::evil::\')" title="::evil::" onclick="document.form.formatting.html.checked=true;">'.smile_replace('::evil::').'</a> ';
		echo '<a href="javascript:smilie(\'::lol::\')" title="::lol::" onclick="document.form.formatting.html.checked=true;">'.smile_replace('::lol::').'</a> ';
		echo '<a href="javascript:smilie(\'::confused::\')" title="::confused::" onclick="document.form.formatting.html.checked=true;">'.smile_replace('::confused::').'</a> ';
		echo '<a href="javascript:smilie(\'::crazy::\')" title="::crazy::" onclick="document.form.formatting.html.checked=true;">'.smile_replace('::crazy::').'</a> ';

		echo '<a href="javascript:smilie(\'::tired::\')" title="::tired::" onclick="document.form.formatting.html.checked=true;">'.smile_replace('::tired::').'</a> ';
		echo '<a href="javascript:smilie(\'::muah::\')" title="::muah::" onclick="document.form.formatting.html.checked=true;">'.smile_replace('::muah::').'</a>';
	?></small></td>
</tr>
<tr>
	<td align="right"><small><b><?php echo _AT('codes'); ?>: </b></small></td>
	<td><small><?php
	if (isset($current_tab)) {
		echo '<a href="javascript:smilie(\' [?][/?]\')" title="[?][/?]">',_AT('add_term'), '</a>';
	} ?></small></td>
</tr>
</table>


<script type="text/javascript">
<!--
function smilie(thesmilie) {
	if (VISUAL) {
		var pic;

		// Definitions
		switch(thesmilie) {
			<?php smile_javascript(); ?>
		}

		// Insert smilie in visual mode
		var devID = "VDevID";
		var formnum = "2";
		var fieldname = "body_text";
		var setID = formnum + devID + fieldname;

		if(document.all) {
			var el = document.frames[setID];
			var edit = el.document; 
			edit.execCommand("InsertImage",false,"http://titmouse.ic.utoronto.ca"+pic);
		} else {
			var el = document.getElementById(setID).contentWindow; 
			var edit = el.document; 
			edit.execCommand("InsertImage",false, "http://titmouse.ic.utoronto.ca"+pic);
		}
	} else {
		// inserts smilie in text mode (original)
		document.form.body_text.value += thesmilie+" ";
		document.form.body_text.focus();
	}
}
//-->
</script>