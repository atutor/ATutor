<?php global $msg; $msg->printConfirm(); ?>
<div class="box">
	<?php //existing members ?>
	<?php if (in_array(new Member($_SESSION['member_id']), $this->group_obj->group_members)): ?>
	| <a href="mods/social/groups/invite.php?id=<?php echo $this->group_obj->getID();?>"><?php echo _AT('invite'); ?></a> |

	<?php //group admin ?>
	<?php if ($this->group_obj->getUser() == $_SESSION['member_id']): ?>
	<a href="mods/social/groups/edit.php?id=<?php echo $this->group_obj->getID();?>"><?php echo _AT('edit_group'); ?></a> |
	<a href="mods/social/groups/view.php?id=<?php echo $this->group_obj->getID().SEP;?>delete=confirm"><?php echo _AT('disband_group'); ?></a> |
	<?php //existing members ?>
	<?php else: ?>
	<a href="mods/social/groups/view.php?id=<?php echo $this->group_obj->getID().SEP;?>remove=1"><?php echo _AT('leave_group'); ?></a> |
	<?php endif; ?>

	<?php //new members ?>
	<?php else: ?>
	<a href="mods/social/groups/join.php?id=<?php echo $this->group_obj->getID();?>"><?php echo _AT('join_group'); ?></a> |
	<?php endif; ?>

	<?php //everyone ?>
	<a href="mods/social/groups/list.php?id=<?php echo $this->group_obj->getID();?>"><?php echo _AT('group_members'); ?></a> |

	<?php include('notifications.tmpl.php'); ?>
</div>
<div>
	<?php 
		foreach ($this->group_obj->getGroupActivities() as $activity_id=>$activity_title){
			echo $activity_title;
		}				
	?>
</div>


<?php if (in_array(new Member($_SESSION['member_id']), $this->group_obj->group_members)): ?>
<div style="width:59%; float:left;">
	<div class="headingbox">
		<h3><?php echo _AT('message_board'); ?></h3></div>
	<div class="contentbox">	
		<form method="POST" action="">
			<label for="message"></label>
			<textarea name="msg_body" id="message" cols="40" rows="5"></textarea><br />
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
				<?php 
				if ($message_array['member_id']==$_SESSION['member_id'] || $this->group_obj->getUser()==$_SESSION['member_id']){
					echo '<a href="'.url_rewrite('mods/social/groups/delete_message.php?gid='.$this->group_obj->getID().SEP.'delete='.$id).'"><img src="'.$_base_href.'mods/social/images/b_drop.png" alt="'._AT('remove').'" title="'._AT('remove').'" border="0" /></a>';
				}
				?>
				<p><?php echo $message_array['body']; ?></p>
			</div>
		<?php 
			$counter++;
			endforeach;
		?>
	</div>
</div>
<?php endif; ?>

<div style="width:39%;float:right;">
	<div class="headingbox"><h3><?php echo _AT('group_info'); ?></h3></div>
	<div class="contentbox">
		<div><?php echo $this->group_obj->getLogo();?></div>
		<dl  id="public-profile">
			<dt><?php echo _AT('group_name'); ?></dt>
			<dd><?php echo $this->group_obj->getName();?></dd>

			<dt><?php echo _AT('group_type'); ?></dt>
			<dd><?php echo $this->group_obj->getGroupType();?></dd>

			<dt><?php echo _AT('created_by'); ?></dt>
			<dd><?php echo printSocialName($this->group_obj->getUser());?></dd>

			<dt><?php echo _AT('created_date'); ?></dt>
			<dd><?php echo AT_DATE(_AT('startend_date_long_format'), $this->group_obj->getCreatedDate(), AT_DATE_MYSQL_DATETIME);?></dd>

			<dt><?php echo _AT('group_last_updated'); ?></dt>
			<dd><?php echo AT_DATE(_AT('startend_date_long_format'), $this->group_obj->getLastUpdated(), AT_DATE_MYSQL_DATETIME);?></dd>

			<dt><?php echo _AT('number_of_members');?></dt>
			<dd><?php echo count($this->group_obj->group_members);?></dd>
		</dl>
	</div><br />
</div>