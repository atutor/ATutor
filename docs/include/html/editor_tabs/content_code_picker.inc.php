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
		echo '<a href="javascript:smilie(\':)\')" title=":)" onClick="document.form.formatting.html.checked=true;">'.smile_replace(':)').'</a> ';
		echo '<a href="javascript:smilie(\';)\')" title=";)" onClick="document.form.formatting.html.checked=true;">'.smile_replace(';)').'</a> ';
		echo '<a href="javascript:smilie(\':(\')" title=":(" onClick="document.form.formatting.html.checked=true;">'.smile_replace(':(').'</a> ';
		echo '<a href="javascript:smilie(\':\\\ \')" title=":\\" onClick="document.form.formatting.html.checked=true;">'.smile_replace(':\\').'</a> ';

		echo '<a href="javascript:smilie(\':P\')" title=":P" onClick="document.form.formatting.html.checked=true;">'.smile_replace(':P').'</a> ';
		echo '<a href="javascript:smilie(\'::angry::\')" title="::angry::" onClick="document.form.formatting.html.checked=true;">'.smile_replace('::angry::').'</a> ';

		echo '<a href="javascript:smilie(\'::evil::\')" title="::evil::" onClick="document.form.formatting.html.checked=true;">'.smile_replace('::evil::').'</a> ';
		echo '<a href="javascript:smilie(\'::lol::\')" title="::lol::" onClick="document.form.formatting.html.checked=true;">'.smile_replace('::lol::').'</a> ';
		echo '<a href="javascript:smilie(\'::confused::\')" title="::confused::" onClick="document.form.formatting.html.checked=true;">'.smile_replace('::confused::').'</a> ';
		echo '<a href="javascript:smilie(\'::crazy::\')" title="::crazy::" onClick="document.form.formatting.html.checked=true;">'.smile_replace('::crazy::').'</a> ';

		echo '<a href="javascript:smilie(\'::tired::\')" title="::tired::" onClick="document.form.formatting.html.checked=true;">'.smile_replace('::tired::').'</a> ';
		echo '<a href="javascript:smilie(\'::muah::\')" title="::muah::" onClick="document.form.formatting.html.checked=true;">'.smile_replace('::muah::').'</a>';
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
	// inserts smilie text
	document.form[27].value += thesmilie+" ";
	document.form[27].focus();
/*

// Fiddling around with the smilies for the Visual Editor here.  Not yet in a stable state.

//	doFormatF (,thesmilie);
	var el = document.form[28];
	var parent=el.parentNode;
//	if (parent.value.IndexOf("<iframe") >= 0) {
		var pos=el.value.lastIndexOf("'></input>");
		alert (pos);
		var newstr = thesmilie;//el.value.substring(0, pos) + thesmilie + " " + el.value.substring(pos, el.value.length);
		var oDiv=document.createElement('div');
		parent.insertBefore(oDiv, el);
		parent.removeChild(el);	 
		oDiv.innerHTML=newstr;
//	}

/*	var edit=el.document;
	edit.execCommand("",false,thesmilie);

/*	// inserts smilie text for visual editor
	var str = document.form[28].value;
	if(document.all)
		el.outerHTML= str;
	else {
		var parent=el.parentNode;
		var oDiv=document.createElement('div');
		var pos=parent.lastIndexOf("'></input>");
		var newstr = parent.substring(0, pos) + thesmilie + " " + parent.substring(pos, parent.length);
//var newstr = thesmilie;

		parent.insertBefore(oDiv, el);
		parent.removeChild(el);	 
		oDiv.innerHTML=newstr;
	}
*/
}
//-->
</script>