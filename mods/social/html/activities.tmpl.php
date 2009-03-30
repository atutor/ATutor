
<div class="">
<div class="headingbox"><h3><?php echo _AT('network_updates'); ?></h3></div>
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
	</div><br />
<?php else: ?>
<?php echo _AT('no_activities'); ?>
<?php endif; ?>
</div>