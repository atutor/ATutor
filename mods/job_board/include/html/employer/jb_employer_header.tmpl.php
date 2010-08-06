<div class="jb_add_posting">
	<?php if(isset($_SESSION['jb_employer_id']) && $_SESSION['jb_employer_id'] > 0): ?>
	<a href="<?php echo AT_JB_BASENAME;?>employer/home.php"><?php echo _AT('jb_employer_home');?></a> | 
	<a href="<?php echo AT_JB_BASENAME;?>employer/logout.php"><?php echo _AT('jb_logout');?></a>
	<?php else: ?>
	<a href="<?php echo AT_JB_BASENAME;?>employer/login.php"><?php echo _AT('jb_login');?></a> | 
	<a href="<?php echo AT_JB_BASENAME;?>employer/registration.php"><?php echo _AT('jb_employer_registration');?></a>
	<?php endif; ?>
</div>
<div style="clear:both;"></div>
