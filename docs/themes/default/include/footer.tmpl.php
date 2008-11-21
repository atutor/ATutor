<?php if (!defined('AT_INCLUDE_PATH')) { exit; } ?>

		<?php if (isset($_SESSION['course_id']) && $_SESSION['course_id'] > 0): ?>
			<div style="clear: left; text-align:right;" id="gototop">		
				<br />
				<span style="font-size:smaller;padding-right:3px;"><a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#content" title="<?php echo _AT('goto_content'); ?> Alt-c" ><?php echo _AT('goto_top'); ?>
				<img src="<?php echo $this->base_path; ?>themes/default/images/top.gif" alt="<?php echo _AT('goto_top'); ?> Alt-c" border="0"/> 
				
				</a>	</span>
			</div>  
		<?php endif; ?> 

	</div>
</div>

<div id="footer">
	<br /><br />
	<?php require(AT_INCLUDE_PATH.'html/languages.inc.php'); ?>
	<?php require(AT_INCLUDE_PATH.'html/copyright.inc.php'); ?>
</div>

</body>
</html>