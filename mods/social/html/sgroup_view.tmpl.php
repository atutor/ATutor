<?php debug($this->group_obj); ?>
<div class="">
	<div style="float:left;">
		<div class="box">
			<h5><?php echo _AT('group_info'); ?></h5>
			<dl>
				<dt><?php echo _AT('group_name'); ?></dt>
				<dd><?php echo $this->group_obj->getName();?></dd>

				<dt><?php echo _AT('group_type'); ?></dt>
				<dd><?php echo $this->group_obj->getGroupType();?></dd>

				<dt><?php echo _AT('created_by'); ?></dt>
				<dd><?php echo printSocialName($this->group_obj->getUser());?></dd>

				<dt><?php echo _AT('created_date'); ?></dt>
				<dd><?php echo AT_DATE(_AT('startend_date_long_format'), $this->group_obj->getCreatedDate(), AT_DATE_MYSQL_DATETIME);?></dd>

				<dt><?php echo _AT('last_update'); ?></dt>
				<dd><?php echo AT_DATE(_AT('startend_date_long_format'), $this->group_obj->getLastUpdated(), AT_DATE_MYSQL_DATETIME);?></dd>

				<dt><?php echo _AT('number_of_members');?></dt>
				<dd><?php echo count($this->group_obj->group_members);?></dd>
			</dl>
		</div>
		
		<div class="box">
			<h5><?php echo _AT('message_board'); ?></h5>
			<form method="POST" action="">
				<textarea name="msg_body"></textarea>
				<input class="button" type="submit" name="submit" value="<?php echo _AT('post');?>" />
			</form><hr/>

			<?php 
				$counter=0;
				foreach ($this->group_obj->getMessages() as $id=>$message_array): 
					//Make this a constant later
					if ($counter > 10){
						echo '<a href="">show all</a>';
						break;
					}
			?>
				<div class="content">
					<?php echo $message_array['created_date'].' - '.printSocialName($message_array['member_id']); ?>
					<p><?php echo $message_array['body']; ?></p>
				</div>
			<?php 
				$counter++;
				endforeach;
			?>
		</div>
	</div>


	<div style="float:right;">
		<div><?php echo $this->logo;?></div>
		<div class="box">
			<?php if (in_array(new Member($_SESSION['member_id']), $this->group_obj->group_members)): ?>
			<a href="mods/social/groups/invite.php?id=<?php echo $this->group_obj->getID();?>"><?php echo _AT('invite'); ?></a>
			<a href="mods/social/groups/view.php?id=<?php echo $this->group_obj->getID().SEP;?>remove=1"><?php echo _AT('leave_group'); ?></a>				
			<?php else: ?>
			<a href=""><?php echo _AT('join_group'); ?></a>
			<?php endif; ?>
			<?php if ($this->group_obj->getUser() == $_SESSION['member_id']): ?>
			<a href="mods/social/groups/edit.php?id=<?php echo $this->group_obj->getID();?>"><?php echo _AT('edit_group'); ?></a>
			<a href="mods/social/groups/delete.php?id=<?php echo $this->group_obj->getID();?>"><?php echo _AT('disband_group'); ?></a>
			<?php endif; ?>

			<?php if(!empty($this->groupsInvitations)): ?>
			<div>
			<div class="box">New Group Invitations</div>
			<?php
				foreach ($this->groupsInvitations[$this->group_obj->getID()] as $index=>$sender_id){
					$name .= printSocialName($sender_id).', ';
				}
				$name = substr($name, 0, -2);
			?>
			<div class="box">
				<ul>
				<li><?php echo $name; ?> has invited you to join this group.</li>
				<li><?php echo 'Accept request?'; ?><a href="mods/social/groups/invitation_handler.php?action=accept<?php echo SEP;?>id=<?php echo $gobj->getID();?>"><?php echo _AT('accept_request'); ?></a>|<a href="mods/social/groups/invitation_handler.php?action=reject<?php echo SEP;?>id=<?php echo $gobj->getID();?>"><?php echo _AT('reject_request'); ?></a></li>
				</ul>
			</div>
			</div>
			<?php endif; ?>

		</div>
		<div>
			<?php 
				foreach ($this->group_obj->getGroupActivities() as $activity_id=>$activity_title){
					echo $activity_title;
				}				
			?>
		</div>
	</div>
</div>