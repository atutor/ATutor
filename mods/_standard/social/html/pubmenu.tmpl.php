<?php 
//will only be displayed when the user is outside of the course scope.
if ((!isset($_SESSION['course_id'])||$_SESSION['course_id'] == 0) && $_SESSION['valid_user']==1):
?>
<ul class="social_inline_menu">
	<li class="inlinelist"><a href="<?php echo AT_SOCIAL_BASENAME.AT_SOCIAL_INDEX; ?>"><?php echo _AT('network_home'); ?></a></li>
	<li class="inlinelist"><a href="<?php echo AT_SOCIAL_BASENAME.'connections.php'; ?>"><?php echo _AT('connections'); ?></a></li>
	<li class="inlinelist"><a href="<?php echo AT_SOCIAL_BASENAME.'sprofile.php'; ?>"><?php echo _AT('social_profile'); ?></a></li>
	<li class="inlinelist"><a href="<?php echo AT_SOCIAL_BASENAME.'applications.php'; ?>"><?php echo _AT('applications'); ?></a></li>
	<li class="inlinelist"><a href="<?php echo AT_SOCIAL_BASENAME.'groups/index.php'; ?>"><?php echo _AT('social_groups'); ?></a></li>
	<li class="inlinelist"><a href="<?php echo AT_SOCIAL_BASENAME.'settings.php'; ?>"><?php echo _AT('settings'); ?></a></li>
</ul>
<br/>
<?php endif; ?>