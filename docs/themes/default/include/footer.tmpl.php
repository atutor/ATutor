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
	<?php if (($_SESSION['course_id'] > 0) && $this->side_menu): ?>
		<td valign="top">
		<script type="text/javascript">
		//<![CDATA[
		var state = getcookie("side-menu");
		if (state && (state == 'none')) {
			document.writeln('<div style="display:none;" id="side-menu">');
		} else {
			document.writeln('<div style="" id="side-menu">');
		}
		//]]>
		</script>
			<?php foreach ($this->side_menu as $dropdown_file): ?>
				<?php require(AT_INCLUDE_PATH . 'html/dropdowns/' . $dropdown_file . '.inc.php'); ?>
			<?php endforeach; ?>
		<script type="text/javascript">
		//<![CDATA[
			document.writeln('</div>');
		//]]>
		</script>
		</td>
	<?php endif; ?>
</tr>
</table>
<br />
<br />
<div class="footer">
	<?php require(AT_INCLUDE_PATH.'html/languages.inc.php'); ?>
	<?php require(AT_INCLUDE_PATH.'html/copyright.inc.php'); ?>
</div>
</body>
</html>