	<div class="headingbox"><h3><?php echo _AT('my_groups'); ?></h3></div>
	<div class="contentbox">
		<?php 
		//randomize index 
		$added_groups = array();
		for ($i=0; sizeof($added_groups)<SOCIAL_GROUP_HOMEPAGE_MAX ; $i++): 
			$grp = $this->my_groups[rand(0, sizeof($this->my_groups)-1)];
			if (in_array($grp, $added_groups)){
				continue;
			} else {
				$added_groups[] = $grp;
			}
			$grp_obj = new SocialGroup($grp);
		?>
		<div class="contact_mini">
			<?php if ($grp_obj->getUser() == $_SESSION['member_id']): ?>
				<div style="float:right;margin:1em;"><a href="<?php echo $_base_href; ?>mods/social/groups/edit.php?id=<?php echo $grp;?>"><img src="<?php echo $_base_href; ?>mods/social/images/icon-settings.png" alt="<?php echo _AT('settings'); ?>" title="<?php echo _AT('settings'); ?>" border="0"/></a></div>
			<?php endif; ?>
			<?php if ($grp_obj->getUser() != $_SESSION['member_id']): ?>
				<div style="float:right;margin:1em;"><a href="<?php echo $_base_href; ?>mods/social/groups/view.php?id=<?php echo $grp.SEP;?>remove=1"><img src="<?php echo $_base_href; ?>mods/social/images/b_drop.png" alt="<?php echo _AT('delete'); ?>" title="<?php echo _AT('delete'); ?>" border="0"/></a></div>
			<?php endif; ?>



			<div class="box">
				<?php echo $grp_obj->getLogo(); ?>
				<a href="<?php echo url_rewrite('mods/social/groups/view.php?id='.$grp);?>"><h4><?php echo $grp_obj->getName(); ?></h4></a><br/>
				<?php echo _AT('type') .': '. $grp_obj->getGroupType();?><br/>
				<?php echo _AT('description') .': '. $grp_obj->getDescription();?><br/>
			</div><br />
		</div>
		<?php endfor; ?>
		<?php		
		if(!$grp){ 
			echo _AT('no_groups_yet');
		 } ?>
	</div>
