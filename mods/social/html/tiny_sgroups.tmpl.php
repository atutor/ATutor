
	<div class="headingbox"><h3><?php echo _AT('my_groups'); ?></h3></div>
	<div class="contentbox">
		<?php foreach ($this->my_groups as $i=>$grp): 
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
				<a href="mods/social/groups/view.php?id=<?php echo $grp;?>"><h4><?php echo $grp_obj->getName(); ?></h4></a><br/>
				<?php echo _AT('type') .': '. $grp_obj->getGroupType();?><br/>
				<?php echo _AT('description') .': '. $grp_obj->getDescription();?><br/>
			</div><br />
		</div>
		<?php endforeach; ?>
	</div>
