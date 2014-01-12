<?php if (!defined('AT_INCLUDE_PATH')) { exit; } ?>
<?php if ($this->mobile_device_type != IPAD_DEVICE): ?><!-- begin footer template for iphone, android, and blackberry -->
</div> 
</div> 
</div> 
</div> 
</div> 
</div> <!-- end wrapper -->
<div id="footer" class="fl-navbar fl-table">
<div id="footer-links"> <!-- top help/search/login links -->
	<ul class="footer-links-tabs fl-tabs flc-themer" role="menu">  
		<?php if (isset($_SESSION['member_id']) && $_SESSION['member_id'] > 0): ?>
			<?php if(!$this->just_social): ?>					
				<?php if ($_SESSION['is_super_admin']): ?>
				<li role="menuitem">	<a  href="<?php echo $this->base_path; ?>bounce.php?admin"><?php echo _AT('back'); ?></a> </li>
				<?php endif; ?>
	
				<?php if ($this->course_id > -1): ?>
					<?php if (get_num_new_messages()): ?>
				<li role="menuitem">		<a  href="<?php echo $this->base_path; ?>inbox/index.php"><?php echo _AT('inbox'); ?> (<?php echo get_num_new_messages(); ?>)</a> </li>
					<?php else: ?>
				<li role="menuitem">		<a href="<?php echo $this->base_path; ?>inbox/index.php"><?php echo _AT('inbox'); ?></a></li>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif; ?>
		<!--  SEARCH MOVED TO #topnavlist -->
		<?php if(!$this->just_social): ?>
			<li role="menuitem"><a href="<?php echo $this->base_path; ?>search.php"><?php echo _AT('search'); ?></a> </li>
		<?php endif; ?>
		<li role="menuitem"><a href="<?php echo $this->base_path; ?>help/index.php"><?php echo _AT('help'); ?></a></li>

		<?php if (isset($_SESSION['valid_user']) && $_SESSION['valid_user']): ?>					 
			<li role="menuitem"><a href="<?php echo $this->base_path; ?>logout.php"><?php echo _AT('logout'); ?></a></li>
		<?php else: ?>
			<!-- <li><a href="<?php echo $this->base_path; ?>login.php?course=<?php echo $this->course_id; ?>"><?php echo _AT('login'); ?></a></li> -->
		<?php endif; ?>
</ul>			
 </div>			
</div>

	<script type="text/javascript">
//<!--
    <?php require_once(AT_INCLUDE_PATH.'../jscripts/ATutor_js.php'); ?>
//-->
</script>
</body>
<?php endif;?><!--  end footer template for iphone, android and blackberry -->
<?php if ($this->mobile_device_type == IPAD_DEVICE): ?> <!-- start footer template for ipad/talets -->

</div> <!-- end main -->
</div> <!-- end innner-contentwrapper -->	
</div> <!-- end contentcolumn -->

<div id="" class="fl-navbar fl-table">
<div id="footer-links"> <!-- top help/search/login links -->
	<ul class="footer-links-tabs fl-tabs flc-themer" role="menu">  
		<?php if (isset($_SESSION['member_id']) && $_SESSION['member_id'] > 0): ?>
			<?php if(!$this->just_social): ?>					
				<?php if ($_SESSION['is_super_admin']): ?>
				<li role="menuitem">	<a  href="<?php echo $this->base_path; ?>bounce.php?admin"><?php echo _AT('back'); ?></a> </li>
				<?php endif; ?>
	
				<?php if ($this->course_id > -1): ?>
					<?php if (get_num_new_messages()): ?>
				<li role="menuitem">		<a  href="<?php echo $this->base_path; ?>inbox/index.php"><?php echo _AT('inbox'); ?> (<?php echo get_num_new_messages(); ?>)</a> </li>
					<?php else: ?>
				<li role="menuitem">		<a href="<?php echo $this->base_path; ?>inbox/index.php"><?php echo _AT('inbox'); ?></a></li>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif; ?>
		
		<?php if(!$this->just_social): ?>
			<li role="menuitem"><a href="<?php echo $this->base_path; ?>search.php"><?php echo _AT('search'); ?></a> </li>
		<?php endif; ?> 
		<li role="menuitem"><a href="<?php echo $this->base_path; ?>help/index.php"><?php echo _AT('help'); ?></a></li>

		<?php if (isset($_SESSION['valid_user']) && $_SESSION['valid_user']): ?>					 
			<li role="menuitem"><a href="<?php echo $this->base_path; ?>logout.php"><?php echo _AT('logout'); ?></a></li>
		<?php else: ?>
			<!-- <li><a href="<?php echo $this->base_path; ?>login.php?course=<?php echo $this->course_id; ?>"><?php echo _AT('login'); ?></a></li> -->
		<?php endif; ?>
</ul>			
 </div>			
</div>
	<script type="text/javascript">
//<!--
    <?php require_once(AT_INCLUDE_PATH.'../jscripts/ATutor_js.php'); ?>
//-->
</script>
</body>
<?php endif; ?><!--  end footer template for ipad/tablets -->
</html>