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
		
		<!-- handles sliding -->
		<script src="http://code.jquery.com/jquery-latest.js"></script>
		<script>
		  $(document).ready(function(){		
			var h = $("#messages").height();
			var line_of_height = 250;

			if (h>=500){
				$('#buttonList').show();
				$('#message_board').css({'height':'500px'});
			} else {
				$('#buttonList').hide();
			}

			$("#prevButton").click(function(event){
			  if (h < $("#messages").height()){
				  $("#messages").animate({"marginTop": "+="+line_of_height+"px"}, "slow");
				  h += line_of_height;
 				  $("#temp").html(h);
			  }
			  event.preventDefault();
			});

			$("#nextButton").click(function(event){
			  if ( h >= line_of_height) {
				  $("#messages").animate({"marginTop": "-="+line_of_height +"px"}, "slow");
				  h -= line_of_height ;
				  $("#temp").html(h);
			  }
			  event.preventDefault();
			});

		  });
		  </script>

		<div id="message_board" style="max-height:500px; overflow:hidden;">
		<div id="messages">
		<?php 
			$counter=0;
			foreach ($this->group_obj->getMessages() as $id=>$message_array): ?>
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
		</div></div>
		<div id="buttonList">
			<a id="prevButton" href="#">&lt;&lt;<?php echo _AT('previous'); ?></a>
			<a id="nextButton" href="#"><?php echo _AT('next'); ?>&gt;&gt;</a>
		</div>
		<div id="temp"></div>
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

			<dt><?php echo _AT('group_privacy'); ?></dt>
			<dd><?php echo ($this->group_obj->getPrivacy()?_AT('private'):_AT('public'))?><br/></dd>			

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