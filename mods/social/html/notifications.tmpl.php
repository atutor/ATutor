<?php 
//Friend request
if(!empty($this->pending_requests)): 
?>
<div>
<div class="box"><?php echo _AT('pending_friend_requests'); ?></div>
<?php
	foreach ($this->pending_requests as $id=>$r_obj): 
?>
<div class="box">
	<ul>
	<li><a href="mods/social/sprofile.php?id=<?php echo $id;?>"><img src="get_profile_img.php?id=<?php echo $id; ?>" alt="<?php echo _AT('profile_picture'); ?>" /></a></li>
	<li><?php echo printSocialName($id) ?></li>
	<li><?php echo _AT('approve_request'); ?><a href="<?php echo url_rewrite('mods/social/index.php');?>?approval=y<?php echo SEP;?>id=<?php echo $r_obj->id;?>"><?php echo _AT('approve_request'); ?></a>|<a href="<?php echo url_rewrite('mods/social/index.php');?>?approval=n<?php echo SEP;?>id=<?php echo $r_obj->id;?>"><?php echo _AT('reject_request'); ?></a></li>
	</ul>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>



<?php 
//Group invitations requests
if(!empty($this->groups_invitations)): 
?>
<div>
<div class="headingbox"><?php echo _AT('new_group_invitations'); ?></div>
<?php
	foreach ($this->groupsInvitations as $id=>$sender_ids): 
	$gobj = new SocialGroup($id);
	$name = '';
		foreach($sender_ids as $index=>$sender_id){
			$name .= printSocialName($sender_id).', ';
		}
	$name = substr($name, 0, -2);
?>
<div class="contentbox">
	<ul>
	<li id="activity"><?php echo _AT('has_invited_join', $name, $gobj->getID(), $gobj->getName()); ?></li>
	<li  id="activity"><?php echo _AT('accept_request'); ?><a href="mods/social/groups/invitation_handler.php?action=accept<?php echo SEP;?>id=<?php echo $gobj->getID();?>"><?php echo _AT('accept_request'); ?></a>|<a href="mods/social/groups/invitation_handler.php?action=reject<?php echo SEP;?>id=<?php echo $gobj->getID();?>"><?php echo _AT('reject_request'); ?></a></li>
	</ul>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>


<?php
//Group requests
if (!empty($this->groups_requests)): 
?>
<div>
<div class="box">New Group Requests</div>
<div class="box">
<?php
	foreach ($this->groups_requests as $index=>$sender_id):
		$name = printSocialName($sender_id);
?>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>