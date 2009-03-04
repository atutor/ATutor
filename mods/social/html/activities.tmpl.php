
<div class="gadget_wrapper">
<div class="gadget_title_bar"><?php echo _AT('network_updates'); ?></div>
<?php
/**
 * Loop through all the friends and print out a list.  
 */
if (!empty($this->activities)): ?>
	<div class="gadget_container">
		<ul>
			<?php foreach ($this->activities as $id=>$array): ?>
			<li><?php echo $array['created_date']. ' - '. printSocialName($array['member_id']).' '. $array['title']; ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php else: ?>
<?php echo _AT('NO_ACTIVITIES'); ?>
<?php endif; ?>
</div>