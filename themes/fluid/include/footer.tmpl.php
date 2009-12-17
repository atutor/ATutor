<?php if (!defined('AT_INCLUDE_PATH')) { exit; } global $system_courses; ?>

		<?php if ($_SESSION['course_id'] > 0): ?>
			<div id="gototop">		
				<a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#content" title="<?php echo _AT('goto_content'); ?> Alt-c" ><?php echo _AT('goto_top'); ?></a>
			</div>  
		<?php endif; ?> 
	</div>   <!-- END of content style -->
	
	<?php if ($_SESSION['course_id']>0 && $system_courses[$_SESSION['course_id']]['side_menu'] && $_SESSION['prefs']['PREF_MENU']=="right"): ?>
	<div id="side-menu" class="orderable" style="display:inline; float:left">
		<div class="grab"><img src="<?php echo $this->img; ?>layers.png" /></div>
		<?php require(AT_INCLUDE_PATH.'side_menu.inc.php'); ?>
	</div>
	<?php endif; ?>
	
	</div> <!-- END of <div id="contentwrapper"> -->

<div id="footer">
	<br /><br />
	<?php require(AT_INCLUDE_PATH.'html/languages.inc.php'); ?>
	<?php require(AT_INCLUDE_PATH.'html/copyright.inc.php'); ?>
</div>


<?php
debug($_SESSION);
?>

</body>
</html>