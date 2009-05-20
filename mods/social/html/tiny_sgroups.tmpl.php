	<div class="headingbox"><a href="<?php echo url_rewrite('mods/social/groups/index.php'); ?>"><h3><?php echo _AT('my_groups'); ?></h3></a></div>
	<div class="contentbox">
		<?php foreach ($this->my_groups as $i=>$grp): 
			$grp_obj = new SocialGroup($grp);

		?>
		<div class="contact_mini">
			<?php if ($grp_obj->getUser() == $_SESSION['member_id']): ?>
				<div style="float:right;margin:1em;"><a href="<?php echo $_base_href; ?>mods/social/groups/edit.php?id=<?php echo $grp;?>"><img src="<?php echo $_base_href; ?>mods/social/images/icon-settings.png" alt="<?php echo _AT('settings'); ?>" title="<?php echo _AT('settings'); ?>" border="0"/></a></div>
			<?php endif; ?>
			<?php if ($grp_obj->getUser() != $_SESSION['member_id']): ?>
				<div style="float:right;margin:1em;"><a href="<?php echo $_base_href; ?>mods/social/groups/view.php?id=<?php echo $grp.SEP;?>remove=1"><img src="<?php echo $_base_href; ?>mods/social/images/b_drop.png" alt="<?php echo _AT('delete'); ?>" title="<?php echo _AT('leave_group'); ?>" border="0"/></a></div>
			<?php endif; ?>



			<div class="box">
				<div style="float:left;">
				<?php echo $grp_obj->getLogo(); ?>
				
				</div>
				<div style="float:left; padding-left:0.5em;">
				<a href="<?php echo url_rewrite('mods/social/groups/view.php?id='.$grp);?>"><h4><?php echo $grp_obj->getName(); ?></h4></a><br/>
					<?php echo _AT('type') .': '. $grp_obj->getGroupType();?><br/>
					<?php echo _AT('description') .': <br/>'. $grp_obj->getDescription();?><br/>
				</div>
				<div style="clear:both;"></div>
			</div><br />
		</div>
		<?php endforeach; ?>
		<?php		
		if(!$grp){ 
			echo _AT('no_groups_yet');
		 } ?>
	</div>
