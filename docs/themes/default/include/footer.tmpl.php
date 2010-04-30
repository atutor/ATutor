<?php if (!defined('AT_INCLUDE_PATH')) { exit; } ?>

		<?php if (isset($_SESSION['course_id']) && $_SESSION['course_id'] > 0): ?>
			<div style="clear: left; text-align:right;" id="gototop">		
				<br />
				<span style="font-size:smaller;padding-right:3px;"><a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#content" title="<?php echo _AT('goto_content'); ?> Alt-c" ><?php echo _AT('goto_top'); ?>
				<img src="<?php echo $this->base_path; ?>themes/default/images/up.png" alt="<?php echo _AT('goto_top'); ?> Alt-c" border="0"/> 
				
				</a>	</span>
			</div>  
		<?php endif; ?> 

	</div>
</div>
		<div class="sequence-links">
		<?php if ($_SESSION["prefs"]["PREF_SHOW_NEXT_PREVIOUS_BUTTONS"]) { ?>
			<?php if ($this->sequence_links['resume']): ?>
					<a style="color:white;" href="<?php echo $this->sequence_links['resume']['url']; ?>" accesskey="."><img src="<?php echo $this->img; ?>resume.png" border="0" title="<?php echo _AT('resume').': '.$this->sequence_links['resume']['title']; ?> Alt+." alt="<?php echo $this->sequence_links['resume']['title']; ?> Alt+." class="img-size-ascdesc" /></a>
			<?php else:
				if ($this->sequence_links['previous']): ?>
					<a href="<?php echo $this->sequence_links['previous']['url']; ?>" title="<?php echo _AT('previous_topic').': '. $this->sequence_links['previous']['title']; ?> Alt+," accesskey=","><img src="<?php echo $this->img; ?>previous.png" border="0" alt="<?php echo _AT('previous_topic').': '. $this->sequence_links['previous']['title']; ?> Alt+," class="img-size-ascdesc" /></a>
				<?php endif;
				if ($this->sequence_links['next']): ?>
					<a href="<?php echo $this->sequence_links['next']['url']; ?>" title="<?php echo _AT('next_topic').': '.$this->sequence_links['next']['title']; ?> Alt+." accesskey="."><img src="<?php echo $this->img; ?>next.png" border="0" alt="<?php echo _AT('next_topic').': '.$this->sequence_links['next']['title']; ?> Alt+." class="img-size-ascdesc" /></a>
				<?php endif; ?>
			<?php endif; ?>
		<?php } ?>
			&nbsp;
		</div>

<div id="footer">
	<br /><br />
	<?php require(AT_INCLUDE_PATH.'html/languages.inc.php'); ?>
	<?php require(AT_INCLUDE_PATH.'html/copyright.inc.php'); ?>
</div>
</div> <!-- end page wrapper --> 
<script language="javascript" type="text/javascript">
//<!--
    <?php require_once(AT_INCLUDE_PATH.'../jscripts/ATutor_js.php'); ?>
//-->
</script>
</body>
</html>