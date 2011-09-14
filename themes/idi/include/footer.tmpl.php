<?php if (!defined('AT_INCLUDE_PATH')) { exit; } ?>

	<?php if ($_SESSION['course_id'] > 0): ?>
		
		<div align="right" style="clear: left;" id="gototop">		
			<br />
			<span style="font-size:smaller;padding-right:3px;"><a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#content" title="<?php echo _AT('goto_content'); ?> Alt-c" ><?php echo _AT('goto_top'); ?></a>	</span>
		</div>  

	<?php endif; ?> 
</div>
</div>
<div id="footer">
	<br /><br />
	<?php require(AT_INCLUDE_PATH.'html/languages.inc.php'); ?>
	<?php require(AT_INCLUDE_PATH.'html/copyright.inc.php'); ?>
</div>
<script type="text/javascript">
//<!--
    <?php require_once(AT_INCLUDE_PATH.'../jscripts/ATutor_js.php'); ?>
//-->
</script>
</body>
</html>