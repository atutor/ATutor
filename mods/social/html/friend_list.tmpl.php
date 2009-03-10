<?php if(!empty($this->pendingRequests)): ?>
<h3>Pending Requests</h3>
<?php
	foreach ($this->pendingRequests as $id=>$r_obj): 
?>
<div>
	<ul>
	<li><a href="mods/social/sprofile.php?id=<?php echo $id;?>"><img src="get_profile_img.php?id=<?php echo $id; ?>" alt="Profile Picture" /></a></li>
	<li><?php echo printSocialName($id) ?></li>
	<li><?php echo 'Approve request?'; ?><a href="<?php echo url_rewrite('mods/social/index.php');?>?approval=y<?php echo SEP;?>id=<?php echo $r_obj->id;?>"><?php echo _AT('approve_request'); ?></a>|<a href="<?php echo url_rewrite('mods/social/index.php');?>?approval=n<?php echo SEP;?>id=<?php echo $r_obj->id;?>"><?php echo _AT('reject_request'); ?></a></li>
	</ul>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>

<div class="">
<div class="box"><a href="mods/social/connections.php"><?php echo _AT('connection'); ?></a></div>
<?php
/**
 * Loop through all the friends and print out a list.  
 */
if (!empty($this->friends)): ?>
	<div class="box">
	<?php foreach ($this->friends as $id=>$m_obj): 
		if (is_array($m_obj) && $m_obj['added']!=1){
			//skip over members that are not "my" friends
			continue;
		} ?>
		<div class="contact_mini">
			<ul>
			<li><a href="mods/social/sprofile.php?id=<?php echo $id;?>"><?php echo printSocialProfileImg($id); ?></a></li>
			<li><?php echo printSocialName($id); ?></li>
			<li><a style="vertical-align:top;" href="<?php echo url_rewrite('mods/social/index.php');?>?remove=yes<?php echo SEP;?>id=<?php echo $id;?>"><?php echo '[x]'; ?></a></li>
			</ul>
		</div>
	<?php endforeach; ?>
	</div>
<?php else: ?>
<?php echo _AT('NO_FRIENDS'); ?>
<?php endif; ?>
</div>