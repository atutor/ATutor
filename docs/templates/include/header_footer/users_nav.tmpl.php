<!-- opening <td> ommitted -->
<table width="100%" cellspacing="0" cellpadding="0" summary="">
<tr>
	<td valign="top" align="right" class="cyan">
		<img src="images/home.jpg" height="15" width="16" class="menuimage17" alt="<?php echo _AT('home'); ?>" /> <a href="users/index.php" class="cyan"><?php echo _AT('home'); ?></a>

		<span class="spacer">|</span> 
		
		<img src="images/profile.jpg" class="menuimage17" height="15" width="16" alt="<?php echo _AT('profile'); ?>" /> <a href="users/edit.php" class="cyan"><?php echo _AT('profile'); ?></a> 
		
		<span class="spacer">|</span> 
		
		<img src="images/browse.gif" height="14" width="16" style="height:1.10em;width:1.26em;" alt="<?php echo _AT('browse_courses'); ?>" /> <a href="users/browse.php" class="cyan"><?php echo _AT('browse_courses'); ?></a>
		
		<?php if ($tmpl_is_instructor): ?>
			<span class="spacer">|</span>
				<img src="images/create.jpg" height="15" width="16" class="menuimage17" alt="<?php echo _AT('create_course'); ?>" /> <a href="users/create_course.php" class="cyan"><?php echo _AT('create_course'); ?></a>
			<span class="spacer">|</span>
		<?php endif; ?>

		<img src="images/logout.gif" alt="<?php echo _AT('logout'); ?>" height="15" width="16" class="menuimage17" /> <a href="logout.php" class="cyan"><?php echo _AT('logout'); ?></a><br /></td>
</tr>
</table></td>
</tr>
<tr>
	<td><h2><?php echo _AT('control_centre'); ?></h2>