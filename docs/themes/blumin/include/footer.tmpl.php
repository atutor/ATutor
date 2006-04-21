<?php if (!defined('AT_INCLUDE_PATH')) { exit; } ?>

	<?php if ($_SESSION['course_id'] > 0): ?>
		
		<div align="right" style="clear: left;">		
			<br />
			<span style="font-size:smaller;padding-right:3px;"><a href="<?php echo $_SERVER['REQUEST_URI']; ?>#content" title="<?php echo _AT('goto_content'); ?> Alt-c" ><?php echo _AT('goto_top'); ?></a>	</span>
		</div>  

	<?php endif; ?> 

	</td>
	<?php if (($_SESSION['course_id'] > 0) && $this->side_menu): ?>
		<td valign="top" style="width: 25%">
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
				<?php if (file_exists($dropdown_file)) { require($dropdown_file); } ?>
			<?php endforeach; ?>
		<script type="text/javascript">
		//<![CDATA[
			document.writeln('</div>');
		//]]>
		</script>
		</td>
	<?php endif; ?>
</tr>
<tr>
	<td colspan="2">
		<br /><br />
		<?php require(AT_INCLUDE_PATH.'html/languages.inc.php'); ?>
		<?php require(AT_INCLUDE_PATH.'html/copyright.inc.php'); ?>
		<br />
	</td>
</tr>
</table>
</body>
</html>