<?php if ((!isset($_SESSION['course_id'])||$_SESSION['course_id'] == 0) && $_SESSION['valid_user']==1):
?>
<ul class="social_inline_menu">
	<li class="inlinelist"><a href="<?php echo 'mods/social/index.php'; ?>"><?php echo _AT('home'); ?></a></li>
	<li class="inlinelist"><a href="<?php echo 'mods/social/connections.php'; ?>"><?php echo _AT('connections'); ?></a></li>
	<li class="inlinelist"><a href="<?php echo 'mods/social/sprofile.php'; ?>"><?php echo _AT('social_profile'); ?></a></li>
	<li class="inlinelist"><a href="<?php echo 'mods/social/applications.php'; ?>"><?php echo _AT('applications'); ?></a></li>
	<li class="inlinelist"><a href="<?php echo 'mods/social/groups/index.php'; ?>"><?php echo _AT('social_groups'); ?></a></li>
	<li class="inlinelist"><a href="<?php echo 'mods/social/settings.php'; ?>"><?php echo _AT('settings'); ?></a></li>
</ul>
<?php endif; ?>