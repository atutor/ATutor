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
// $Id: content_code_picker.inc.php 8901 2009-11-11 19:10:19Z cindy $

if (!defined('AT_INCLUDE_PATH')) { exit; }

?>
<?php echo _AT('click_code'); ?>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<?php
	if (isset($current_tab)) {
			echo '<tr><td align="right"><small><strong>'._AT('codes').': </strong></small></td><td><small> ';
			echo '<a href="javascript:smilie(\'[?] [/?]\')" title="[?][/?]">',_AT('add_term'), '</a> ';
			echo '<a href="javascript:smilie(\'[code] [/code]\')" title="[code][/code]">'._AT('add_code').'</a> ';
			echo '<a href="javascript:smilie(\'[media|640|480]http://[/media]\')" title="[media][/media]">'._AT('add_media').'</a>';
			echo '</small></td></tr>';
	} ?>
</table>

<script type="text/javascript">
//<!--
function smilie(thesmilie, extra) {
	if (document.form.setvisual.value == 1)
		if (!extra) {
			tinyMCE.execCommand("mceInsertContent", false, thesmilie);
		} else {
			tinyMCE.execCommand("mceInsertContent", false, thesmilie + extra);
		}
	else
		if (!extra) {
			document.form.body_text.value += thesmilie+" ";
			document.form.body_text.focus();
		} else {
			document.form.body_text.value += thesmilie+extra+" ";
			document.form.body_text.focus();
		}
}
//--></script>
