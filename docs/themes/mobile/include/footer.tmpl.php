<?php if (!defined('AT_INCLUDE_PATH')) { exit; } ?>
	<?php if (isset($_SESSION['course_id']) && $_SESSION['course_id'] > 0): ?>
			<div id="gototop">		
				<br />
				<a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#navigation-contentwrapper" title="<?php echo _AT('goto_content'); ?> Alt-c" ><?php echo _AT('goto_top'); ?>
				<img src="<?php echo $this->base_path; ?>themes/mobile/images/arrow-up.png" alt="<?php echo _AT('goto_top'); ?> Alt-c" border="0"/> 
				
				</a>
			</div>  
		<?php endif; ?> 
</div> <!-- end innner-contentwrapper -->	
</div> <!-- end contentcolumn? -->
</div> <!-- end contentwrapper -->
</div> <!-- end main -->
</div> <!-- end wrapper -->


<div id="footer" class="fl-navbar fl-table">
<div id="top-links"> <!-- top help/search/login links -->
	<ul class="fl-tabs flc-themer">  
		<?php if (isset($_SESSION['member_id']) && $_SESSION['member_id'] > 0): ?>
			<?php if(!$this->just_social): ?>					
				<?php if ($_SESSION['is_super_admin']): ?>
				<li>	<a  href="<?php echo $this->base_path; ?>bounce.php?admin"><?php echo _AT('return_to_admin_area'); ?></a> </li>
				<?php endif; ?>
	
				<?php if ($this->course_id > -1): ?>
					<?php if (get_num_new_messages()): ?>
				<li>		<a  href="<?php echo $this->base_path; ?>inbox/index.php"><?php echo _AT('inbox'); ?> (<?php echo get_num_new_messages(); ?>)</a> </li>
					<?php else: ?>
				<li>		<a href="<?php echo $this->base_path; ?>inbox/index.php"><?php echo _AT('inbox'); ?></a></li>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif; ?>

		<?php if(!$this->just_social): ?>
			<li><a href="<?php echo $this->base_path; ?>search.php"><?php echo _AT('search'); ?></a> </li>
		<?php endif; ?> 
		<li><a href="<?php echo $this->base_path; ?>help/index.php"><?php echo _AT('help'); ?></a></li>

		<?php if (isset($_SESSION['valid_user']) && $_SESSION['valid_user']): ?>					 
			<li><a href="<?php echo $this->base_path; ?>logout.php"><?php echo _AT('logout'); ?></a></li>
		<?php else: ?>
			<!-- <li><a href="<?php echo $this->base_path; ?>login.php?course=<?php echo $this->course_id; ?>"><?php echo _AT('login'); ?></a></li> -->
		<?php endif; ?>


		

</ul>			
</div>			
</div>
	<script language="javascript" type="text/javascript">
//<!--
    <?php require_once(AT_INCLUDE_PATH.'../jscripts/ATutor_js.php'); ?>
//-->
</script>
</body>
</html>