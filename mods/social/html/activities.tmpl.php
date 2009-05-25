
<div class="">
<div class="headingbox">
	<div style="float:right">
	<?php
		$user = new Member($_SESSION['member_id']); 
		$count = $user->getVisitors();
		echo _AT('visitor_counts').': '.$count['total'];
	?>
	</div>
	<h3><?php echo _AT('network_updates'); ?></h3>
</div>
<?php
/**
 * Loop through all the friends and print out a list.  
 */
if (!empty($this->activities)): ?>
	<div class="contentbox">
		<ul>
			<?php foreach ($this->activities as $id=>$array): ?>
			<li id="activity"><?php echo $array['created_date']. ' - '. printSocialName($array['member_id']).' '. $array['title']; ?></li>
			<?php endforeach; ?>
		</ul>
		<?php //little hack, show_all will only be displayed when the flag is used.
		if (sizeof($this->activities)==SOCIAL_FRIEND_ACTIVITIES_MAX): ?>
		<a href="mods/social/activities.php"><?php echo _AT('show_all');?></a>
		<?php endif; ?>
	</div><br />
<?php else: ?>
<?php echo _AT('no_activities'); ?>
<?php endif; ?>
</div>