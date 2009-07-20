<?php 
//Friend request
if(!empty($this->pending_requests)): 
?>

<div class="headingbox"><h3><?php echo _AT('pending_friend_requests'); ?></h3></div>
<div class="contentbox">
<?php
	foreach ($this->pending_requests as $id=>$r_obj): 
?>

	<div class="box" style="border:thin solid black;">
	<div style="float:right;"><a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.AT_SOCIAL_INDEX);?>?approval=y<?php echo SEP;?>id=<?php echo $r_obj->id;?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/check_icon.gif" alt="<?php echo _AT('accept_request'); ?>" title="<?php echo _AT('accept_request'); ?>" border="0"/></a><a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'index.php');?>?approval=n<?php echo SEP;?>id=<?php echo $r_obj->id;?>"> <img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/b_drop.png" alt="<?php echo _AT('reject_request'); ?>" title="<?php echo _AT('reject_request'); ?>" border="0"/></a>
	</div>
	<ul style="list-style:none;">
		<li style="display:inline;"><?php echo printSocialProfileImg($id);?></li>
		<li style="display:inline;"><?php echo printSocialName($id) ?></li>
	</ul>
	</div><br />
<?php endforeach; ?>

</div><br />
<?php endif; ?>

<?php 
//Group invitations requests
if(!empty($this->group_invitations)): 
?>

<div class="headingbox"><h3><?php echo _AT('new_group_invitations'); ?></h3></div>
<?php
	foreach ($this->group_invitations as $id=>$sender_ids): 
	$gobj = new SocialGroup($id);
	$name = '';
		foreach($sender_ids as $index=>$sender_id){
			$name .= printSocialName($sender_id).', ';
		}
	$name = substr($name, 0, -2);
?>
<div class="contentbox">
	<div style="float:right;"><a href="<?php echo AT_SOCIAL_BASENAME; ?>groups/invitation_handler.php?invitation=accept<?php echo SEP;?>id=<?php echo $gobj->getID();?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/check_icon.gif" alt="<?php echo _AT('accept_request'); ?>" title="<?php echo _AT('accept_request'); ?>" border="0"/></a> <a href="<?php echo AT_SOCIAL_BASENAME;?>groups/invitation_handler.php?invitation=reject<?php echo SEP;?>id=<?php echo $gobj->getID();?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/b_drop.png" alt="<?php echo _AT('reject_request'); ?>" title="<?php echo _AT('reject_request'); ?>" border="0"/></a></div>
	<ul>
	<li id="activity"><?php echo _AT('has_invited_join', $name, $gobj->getID(), $gobj->getName()); ?></li>

	</ul>
</div>
<?php endforeach; ?><br />
</div>
<?php endif; ?>

<?php
//Group requests
if (!empty($this->group_requests)): 
?>

<div class="headingbox"><h3><?php echo _AT('new_group_requests'); ?></h3></div>
<div class="contentbox">

<?php
foreach ($this->group_requests as $id=>$senders):
	$gobj = new SocialGroup($id);
	foreach($senders as $index=>$sender_id):
	$name = printSocialName($sender_id);
?>
<div class="box" style="border:thin solid black;">
	<div style="float:right;">
		<a href="<?php echo AT_SOCIAL_BASENAME; ?>groups/invitation_handler.php?request=accept<?php echo SEP;?>id=<?php echo $gobj->getID().SEP;?>sender_id=<?php echo $sender_id;?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/check_icon.gif" alt="<?php echo _AT('accept_request'); ?>" title="<?php echo _AT('accept_request'); ?>" border="0"/></a> <a href="<?php echo AT_SOCIAL_BASENAME; ?>groups/invitation_handler.php?request=reject<?php echo SEP;?>id=<?php echo $gobj->getID().SEP;?>sender_id=<?php echo $sender_id;?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/b_drop.png" alt="<?php echo _AT('reject_request'); ?>" title="<?php echo _AT('reject_request'); ?>" border="0"/></a>
	</div>
	<ul>
		<li id="activity"><?php echo _AT('has_requested_to', $name, $gobj->getName()); ?></li>

	</ul>
<?php endforeach;?>
</div>
<?php endforeach; ?>
</div><br />

<?php endif; ?>