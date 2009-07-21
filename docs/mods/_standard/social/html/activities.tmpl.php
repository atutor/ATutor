
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


	<div class="contentbox">
	<?php
	/**
	 * Loop through all the friends and print out a list.  
	 */
	if (!empty($this->activities)): ?>
			<ul>
				<?php foreach ($this->activities as $id=>$array): ?>
				<li class="activity"><?php echo $array['created_date']. ' - '. printSocialName($array['member_id']).' '. $array['title']; ?></li>
				<?php endforeach; ?>
			</ul>
			<?php //little hack, show_all will only be displayed when the flag is used.
			if (sizeof($this->activities)==SOCIAL_FRIEND_ACTIVITIES_MAX): ?>
			<a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'activities.php', AT_PRETTY_URL_IS_HEADER); ?>"><?php echo _AT('show_all');?></a>
			<?php endif; ?>	
	<?php else: ?>
	<?php echo _AT('no_activities'); ?>
	<?php endif; ?>
	</div><br />
</div>