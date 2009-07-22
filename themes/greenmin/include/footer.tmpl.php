<?php if (!defined('AT_INCLUDE_PATH')) { exit; } ?>

	<?php if ($_SESSION['course_id'] > 0): ?>
		
		<div align="right" style="clear: left;" id="gototop">		
			<br />
			<span style="font-size:smaller;padding-right:3px;"><a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#content" title="<?php echo _AT('goto_content'); ?> Alt-c" ><?php echo _AT('goto_top'); ?></a>	</span>
		</div>  

	<?php endif; ?> 

</div>
</div>
	<?php if (($_SESSION['course_id'] > 0) && $this->side_menu): ?>
		<div id="leftcolumn">
			<script type="text/javascript">
			//<![CDATA[
			var state = getcookie("side-menu");
			if (state && (state == 'none')) {
				document.writeln('<a name="menu"></a><div style="display:none;" id="side-menu">');
			} else {
				document.writeln('<a name="menu"></a><div style="" id="side-menu">');
			}
			//]]>
			</script>

			<?php require(AT_INCLUDE_PATH.'side_menu.inc.php'); ?>

			<script type="text/javascript">
			//<![CDATA[
				document.writeln('</div>');
			//]]>
			</script>
		</div>
	<?php endif; ?>
</div>

<div id="footer">
	<br /><br />
	<?php require(AT_INCLUDE_PATH.'html/languages.inc.php'); ?>
	<?php require(AT_INCLUDE_PATH.'html/copyright.inc.php'); ?>
</div>
</body>
</html>