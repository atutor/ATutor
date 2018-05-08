<?php if (!defined('AT_INCLUDE_PATH')) { exit; } ?>

		<?php if (isset($_SESSION['course_id']) && $_SESSION['course_id'] > 0): ?>
			<div style="clear: left; text-align:right;" id="gototop">		
				<br />
				<a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#content" title="<?php echo _AT('goto_content'); ?> Alt-c" >
				<img src="<?php echo $this->img; ?>goup.png" alt="<?php echo _AT('goto_top'); ?>" title="<?php echo _AT('goto_top'); ?>"/></a>
			</div>  
		<?php endif; ?> 
			<div class="sequence-links">
		<?php if (isset($_SESSION["prefs"]["PREF_SHOW_NEXT_PREVIOUS_BUTTONS"])) { ?>
			<?php if (isset($this->sequence_links['resume'])): ?>
					<a style="color:white;" href="<?php echo $this->sequence_links['resume']['url']; ?>" accesskey="."><img src="<?php echo $this->img; ?>resume.png" border="0" title="<?php echo _AT('resume').': '.$this->sequence_links['resume']['title']; ?> Alt+." alt="<?php echo $this->sequence_links['resume']['title']; ?> Alt+." class="img1616"/></a>
			<?php else:
				if (isset($this->sequence_links['previous'])): ?>
					<a href="<?php echo $this->sequence_links['previous']['url']; ?>" title="<?php echo _AT('previous_topic').': '. $this->sequence_links['previous']['title']; ?> Alt+," accesskey=","><img src="<?php echo $this->img; ?>previous.png" border="0" alt="<?php echo _AT('previous_topic').': '. $this->sequence_links['previous']['title']; ?> Alt+," class="img1616" /></a>
				<?php endif;
				if (isset($this->sequence_links['next'])): ?>
					<a href="<?php echo $this->sequence_links['next']['url']; ?>" title="<?php echo _AT('next_topic').': '.$this->sequence_links['next']['title']; ?> Alt+." accesskey="."><img src="<?php echo $this->img; ?>next.png" border="0" alt="<?php echo _AT('next_topic').': '.$this->sequence_links['next']['title']; ?> Alt+." class="img1616" /></a>
				<?php endif; ?>
			<?php endif; ?>
		<?php } ?>
			&nbsp;
		</div>
	</div>

</div>
<p><br /><br />&nbsp;</p>
</div> <!-- end page wrapper --> 
<div id="footer" role="contentinfo">
    <div class="logo">
          <a href="<?php echo $this->custom_logo_url; ?>"><img src="<?php echo get_custom_logo(); ?>"  alt="<?php echo $this->custom_logo_alt_text; ?>" style="border:none;" /></a>
    </div>
    <div id="footer-right">
	<?php require(AT_INCLUDE_PATH.'html/languages.inc.php'); ?>
	<?php require(AT_INCLUDE_PATH.'html/copyright.inc.php'); ?>
	</div>
    <script type="text/javascript">
    //<!--
        <?php require_once(AT_INCLUDE_PATH.'../jscripts/ATutor_js.php'); ?>
    //-->
    </script>

</div>
</body>
</html>